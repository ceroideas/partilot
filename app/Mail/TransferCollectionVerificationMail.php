<?php

namespace App\Mail;

use App\Models\ParticipationCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransferCollectionVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $confirmUrl;
    public string $cancelUrl;

    public function __construct(public ParticipationCollection $collection)
    {
        $this->confirmUrl = route('transfer-collection.confirm', ['token' => $collection->confirmation_token]);
        $this->cancelUrl = route('transfer-collection.cancel', ['token' => $collection->confirmation_token]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirma tu solicitud de cobro - Partilot');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.transfer-collection-verification');
    }
}
