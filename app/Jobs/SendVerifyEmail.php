<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\AuthService;
use App\Models\User;

class SendVerifyEmail implements ShouldQueue
{
    use Queueable;

    protected $user;

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
     */
    public function handle(AuthService $authService): void
    {
        $authService->sendVerificationEmail($this->user->email);
    }
}
