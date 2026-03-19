<?php

namespace App\Mail;

use App\Models\Seller;
use App\Models\SellerSettlement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerSettlementStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Seller $seller,
        public SellerSettlement $settlement,
        public bool $isFullySettled
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isFullySettled
            ? 'Liquidación completada - Partilot'
            : 'Actualización de liquidación - Partilot';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.seller-settlement-status');
    }
}

