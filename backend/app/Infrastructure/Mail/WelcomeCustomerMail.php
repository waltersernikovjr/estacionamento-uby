<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail;

use App\Infrastructure\Persistence\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * Welcome email for new customers with email verification link
 */
final class WelcomeCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Customer $customer
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo ao Estacionamento Uby - Confirme seu Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $verificationUrl = $this->generateVerificationUrl();

        return new Content(
            view: 'emails.customer-welcome',
            with: [
                'customer' => $this->customer,
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
                'id' => $this->customer->id,
                'hash' => sha1($this->customer->email),
                'type' => 'customer'
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
