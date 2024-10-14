<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\repositories\Interfaces\EmailVerificationRepositoryInterface;
use App\repositories\Interfaces\UserRepositoryInterface;
use App\repositories\Interfaces\PasswordResetRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\EmailVerificationRepository;
use App\Repositories\PasswordResetRepository;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(EmailVerificationRepositoryInterface::class, EmailVerificationRepository::class);
        $this->app->bind(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
