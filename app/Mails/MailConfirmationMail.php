<?php

namespace App\Mails;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailConfirmationMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $url,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirme seu email'.' - '. config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mail-confirmation',
        );
    }
}