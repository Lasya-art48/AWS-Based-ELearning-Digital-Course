<?php

namespace Mindigo\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $otp,
        public readonly string $email = '',
    ) {
        $this->locale(app()->getLocale());
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Mindigo-auth::app.mail.otp_forgot_password_subject'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'Mindigo-auth::otp',
            with: [
                'otp' => $this->otp,
                'email' => $this->email,
            ],
        );
    }
}
