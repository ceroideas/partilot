<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DigitalPurchaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $buyer,
        public array $items,
        public float $totalAmount
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirmación de compra digital - Partilot');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.digital-purchase-confirmation');
    }
}

