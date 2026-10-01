<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The 6-digit code email. $purpose 'reset' = forgot password, 'register' = confirm email at sign-up. */
class PasswordResetCode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public string $name, public int $minutes, public string $purpose = 'reset') {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->purpose === 'register'
            ? $this->code . ' is your RakanKampus verification code'
            : $this->code . ' is your RakanKampus reset code');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.password-code', text: 'emails.password-code-text');
    }
}
