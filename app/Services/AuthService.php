<?php

namespace App\Services;

use App\Jobs\SendVerifyEmail;
use App\Jobs\SendWelcomeEmail;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use App\Repositories\EmailVerificationRepository;
use App\Repositories\PasswordResetRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    protected $userRepository;
    protected $emailVerificationRepository;
    protected $jwtService;
    protected $passwordResetRepository;

    public function __construct(
        UserRepository $userRepository,
        EmailVerificationRepository $emailVerificationRepository,
        JWTService $jwtService,
        PasswordResetRepository $passwordResetRepository
    ) {
        $this->userRepository = $userRepository;
        $this->emailVerificationRepository = $emailVerificationRepository;
        $this->jwtService = $jwtService;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * Registers a new user with the given data and sends a verification email.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function register(array $data)
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // dispatch event send verification email
        SendWelcomeEmail::dispatch($user);

        // dispatch event send verification email
        SendVerifyEmail::dispatch($user);

        return $user;
    }

    /**
     * Sends a verification email to the user with the given email address.
     *
     * @param string $email
     * @return void
     */
    public function sendVerificationEmail($email)
    {
        try {
            $user = $this->userRepository->findByEmail($email);

            $token = Str::random(60);

            // Store the token in the email_verifications table
            $this->emailVerificationRepository->updateOrCreate(
                ['user_id' => $user->id],
                ['token' => $token, 'expires_at' => now()->addHours(24)]
            );

            // Send the verification email
            Mail::send('emails.verify', ['token' => $token], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Verify Your Email Address');
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Authenticates a user with the given credentials.
     *
     * @param array $credentials
     * @return string
     *
     * @throws \Exception if the credentials are invalid
     */
    public function login(array $credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        // Encode the user's ID and email into a JWT
        return $this->jwtService->encode([
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + (60 * 60) // Token expires in 1 hour
        ]);
    }

    /**
     * Changes the password for a user with the given ID.
     *
     * @param array $data
     * @return void
     *
     * @throws \Exception if the current password is incorrect
     */
    public function changePassword(array $data)
    {
        $user = $this->userRepository->find($data['user_id']);

        if (!Hash::check($data['current_password'], $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($data['new_password'])
        ]);
    }


    public function verifyEmail($token)
    {
        $verification = $this->emailVerificationRepository->findByToken($token);

        if (!$verification) {
            throw new \Exception('Invalid or expired token');
        }

        $user = $this->userRepository->find($verification->user_id);
        $this->userRepository->update($user->id, ['email_verified_at' => now()]);
        $this->emailVerificationRepository->delete($verification->id);

        $token = $this->jwtService->encode([
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + (60 * 60)
        ]);

        return [
            'message' => 'Email verified successfully',
            'token' => $token
        ];
    }

    public function forgotPassword($email)
    {
        $token = Str::random(60);

        $this->passwordResetRepository->updateOrCreate(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()]
        );

        Mail::send('emails.reset_password', ['token' => $token], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Reset Your Password');
        });
    }

    public function resetPassword(array $data)
    {
        $passwordReset = $this->passwordResetRepository->findByEmailAndToken($data['email'], $data['token']);

        if (!$passwordReset) {
            throw new \Exception('Invalid token');
        }

        $user = $this->userRepository->findByEmail($data['email']);
        $this->userRepository->update($user->id, ['password' => Hash::make($data['password'])]);
        $this->passwordResetRepository->deleteByEmail($data['email']);
    }

    public function logout()
    {
        return true;
    }
}
