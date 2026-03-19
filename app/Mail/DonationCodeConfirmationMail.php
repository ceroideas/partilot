<?php

namespace App\Mail;

use App\Models\ParticipationDonation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationCodeConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ParticipationDonation $donation) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Donación y código de recarga - Partilot');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.donation-code-confirmation');
    }
}

