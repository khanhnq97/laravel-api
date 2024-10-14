<?php

namespace App\Listeners;

use App\Events\ForgotPassword;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ForgotPassword $event
     *
     * @return void
     */
    public function handle(ForgotPassword $event): void
    {
        Mail::send('emails.reset_password', ['token' => $event->token], function ($m) use ($event) {
            $m->to($event->email)->subject('Reset Your Password (Notification)');
        });
    }
}
