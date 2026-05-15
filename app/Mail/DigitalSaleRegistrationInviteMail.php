<?php

namespace App\Mail;

use App\Models\PendingDigitalSale;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DigitalSaleRegistrationInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PendingDigitalSale $pending) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Completa tu registro en Partilot para recibir tus participaciones');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.digital-sale-registration-invite');
    }
}
