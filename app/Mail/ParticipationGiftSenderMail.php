<?php

namespace App\Mail;

use App\Models\ParticipationGift;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipationGiftSenderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ParticipationGift $gift) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirmación de regalo de participación - Partilot');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.participation-gift-sender');
    }
}

