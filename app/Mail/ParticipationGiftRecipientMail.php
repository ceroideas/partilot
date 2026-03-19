<?php

namespace App\Mail;

use App\Models\ParticipationGift;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipationGiftRecipientMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ParticipationGift $gift) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Te han regalado una participación - Partilot');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.participation-gift-recipient');
    }
}

