<?php

namespace App\Services;

use App\Jobs\SendVerifyEmail;
use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use App\repositories\Interfaces\UserRepositoryInterface as UserRepository;
use App\Repositories\EmailVerificationRepository;
use App\Repositories\PasswordResetRepository;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Events\ForgotPassword;
use Throwable;

class AuthService
{
    protected UserRepository $userRepository;
    protected EmailVerificationRepository $emailVerificationRepository;
    protected JWTService $jwtService;
    protected PasswordResetRepository $passwordResetRepository;

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
     * @return User
     */
    public function register(array $data): User
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
     * @throws Throwable
     */
    public function sendVerificationEmail(string $email):void
    {
        $user = $this->userRepository->findByEmail(email: $email);

        $token = Str::random(length: 60);

        // Store the token in the email_verifications table
        $this->emailVerificationRepository->updateOrCreate(
            attributes: ['user_id' => $user->id],
            values: ['token' => $token, 'expires_at' => now()->addHours(value: 24)]
        );

        // Send the verification email
        Mail::send(view: 'emails.verify', data: ['token' => $token], callback: function ($message) use ($user): void {
            $message->to($user->email);
            $message->subject('Verify Your Email Address');
            $message->from(env(key: 'MAIL_FROM_ADDRESS'), env(key: 'MAIL_FROM_NAME'));
        });
    }

    /**
     * Authenticates a user with the given credentials.
     *
     * @param array $credentials
     * @return string
     *
     * @throws Exception if the credentials are invalid
     */
    public function login(array $credentials):string
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new Exception('Invalid credentials');
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
     * @throws Exception if the current password is incorrect
     */
    public function changePassword(array $data):void
    {
        $user = $this->userRepository->find($data['user_id']);

        if (!Hash::check($data['current_password'], $user->password)) {
            throw new Exception('Current password is incorrect');
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($data['new_password'])
        ]);
    }


    /**
     * @throws Exception
     */
    public function verifyEmail($token): array
    {
        $verification = $this->emailVerificationRepository->findByToken($token);

        if (!$verification) {
            throw new Exception('Invalid or expired token');
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

    /**
     * Sends a password reset link to the user with the given email address.
     *
     * @param  string  $email
     * @return void
     *
     * @throws Exception
     */
    public function sendPasswordResetLink(string $email): void
    {
        $token = Str::random(60);

        try {
            $this->passwordResetRepository->updateOrCreate(
                ['email' => $email],
                ['token' => $token, 'created_at' => now()]
            );

            event(new ForgotPassword($email, $token));
        } catch (Exception $e) {
            throw new Exception('Failed to send password reset email', 500, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function resetPassword(array $data): void
    {
        $passwordReset = $this->passwordResetRepository->findByEmailAndToken($data['email'], $data['token']);

        if (!$passwordReset) {
            throw new Exception('Invalid token');
        }

        $user = $this->userRepository->findByEmail($data['email']);
        $this->userRepository->update($user->id, ['password' => Hash::make($data['password'])]);
        $this->passwordResetRepository->deleteByEmail($data['email']);
    }

    public function logout(): bool
    {
        return true;
    }
}
