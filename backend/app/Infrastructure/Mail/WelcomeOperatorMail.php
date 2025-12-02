<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail;

use App\Infrastructure\Persistence\Models\Operator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * Welcome email for new operators with email verification link
 */
final class WelcomeOperatorMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Operator $operator
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo ao Sistema de Estacionamento Uby',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $verificationUrl = $this->generateVerificationUrl();

        return new Content(
            view: 'emails.operator-welcome',
            with: [
                'operator' => $this->operator,
                'verificationUrl' => $verificationUrl,
            ],
        );
    }

    /**
     * Generate email verification URL
     */
    private function generateVerificationUrl(): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            [
                'id' => $this->operator->id,
                'hash' => sha1($this->operator->email),
                'type' => 'operator'
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
