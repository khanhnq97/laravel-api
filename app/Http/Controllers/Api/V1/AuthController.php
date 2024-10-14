<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\V1\Auth\VerifyEmailRequest;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Throwable;


class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $this->authService->register($data);

            return response()->json([
                'message' => 'User successfully registered. Please check your email to verify your account.',
                'user' => new UserResource($user),
            ], 201);
        } catch (Exception) {
            return response()->json(['message' => 'Failed to register user'], 500);
        }
    }

    /**
     * Log in a user.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->authService->login($request->validated());

            return response()->json(['token' => $token]);
        } catch (Exception) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
    /**
     * Log out the user.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            return response()->json(['message' => 'Successfully logged out']);
        } catch (Exception) {
            return response()->json(['message' => 'Logout failed'], 500);
        }
    }

    /**
     * Change the password for a user with the given ID.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|different:current_password',
            ]);

            $userId = $request->user_id;
            $user = User::findOrFail($userId);

            // Check the current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 400);
            }

            // Update the user's password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['message' => 'Password was changed successfully']);
        } catch (Exception) {
            return response()->json(['message' => 'Failed to change password'], 500);
        }
    }

    /**
     * Sends a verification email to the user with the given email address.
     *
     * @param VerifyEmailRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function sendVerificationEmail(VerifyEmailRequest $request): JsonResponse
    {
        try {
            $this->authService->sendVerificationEmail($request->email);

            return response()->json(['message' => 'Verification email sent']);
        } catch (Exception) {
            return response()->json(['message' => 'Failed to send verification email'], 500);
        }
    }

    /**
     * Verify the user's email address.
     *
     * @param VerifyEmailRequest $request
     * @return JsonResponse
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->verifyEmail($request->token);

            return response()->json($result);
        } catch (Exception) {
            return response()->json(['message' => 'Verification failed'], 500);
        }
    }

    /**
     * Handle a forgot password request.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->sendPasswordResetLink($request->input('email'));
            return response()->json(['message' => 'Password reset link sent to your email']);
        } catch (Exception) {
            return response()->json(['message' => 'Failed to send password reset link'], 500);
        }
    }

    /**
     * Reset the password for a user with the given email and token.
     *
     * This function is responsible for resetting the password for a user with the given email and token.
     * It will validate the request data and call the resetPassword method of the AuthService class.
     * If the request is successful, it will return a JSON response with a success message.
     * If the request fails, it will return a JSON response with an error message.
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword($request->validated());

            return response()->json(['message' => 'Password has been reset successfully']);
        } catch (Exception) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
