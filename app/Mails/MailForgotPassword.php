<?php

namespace App\Mails;

use App\Models\ForgotPassword;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailForgotPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ForgotPassword $forgotPassword, public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Redefinição de senha'.' - '. config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.forgot-password',
            with: [
                'forgotPassword' => $this->forgotPassword,
                'user' => $this->user,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}