<?php

namespace App\Mail;

use App\Models\ParticipationCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransferCollectionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ParticipationCollection $collection) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Cobro por transferencia registrado - Partilot');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.transfer-collection-confirmation');
    }
}

