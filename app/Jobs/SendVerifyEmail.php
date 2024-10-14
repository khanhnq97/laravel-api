<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\AuthService;
use App\Models\User;
use Throwable;

class SendVerifyEmail implements ShouldQueue
{
    use Queueable;

    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(
        User $user,
    ) {
        $this->user = $user;
    }

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(AuthService $authService): void
    {
        $authService->sendVerificationEmail($this->user->email);
    }
}
