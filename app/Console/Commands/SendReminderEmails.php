<?php

namespace App\Console\Commands;

use App\Mail\ReminderEmail;
use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;


class SendReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $users = User::whereNull('email_verified_at')->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new ReminderEmail($user));
            $this->info("Sent reminder email to: $user->email");
        }

        $this->info('Reminder emails sent successfully!');
    }
}
