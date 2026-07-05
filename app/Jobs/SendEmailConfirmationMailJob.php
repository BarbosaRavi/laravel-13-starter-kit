<?php

namespace App\Jobs;

use App\Mails\MailConfirmationMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class SendEmailConfirmationMailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $userId)
    {
        $this->queue='email';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            return;
        }

        if (blank($user->email_confirmation_token)) {
            $user->forceFill(['email_confirmation_token' => Str::uuid()])->save();
        }

        $base = rtrim(config('app.frontend_url'), '/');
        $url = $base .'/confirmar-email?token='.urlencode($user->email_confirmation_token);

        Mail::to($user->email)->send(new MailConfirmationMail($user, $url));
    }
}
