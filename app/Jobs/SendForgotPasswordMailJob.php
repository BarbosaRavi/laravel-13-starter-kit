<?php

namespace App\Jobs;

use App\Mails\MailForgotPassword;
use App\Models\ForgotPassword;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendForgotPasswordMailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public ForgotPassword $forgotPassword)
    {
        $this->queue='email';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)
            ->send(new MailForgotPassword($this->forgotPassword, $this->user));
    }
}
