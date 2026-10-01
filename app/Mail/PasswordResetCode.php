<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public string $name, public int $minutes) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->code . ' is your RakanKampus reset code');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.password-code', text: 'emails.password-code-text');
    }
}
