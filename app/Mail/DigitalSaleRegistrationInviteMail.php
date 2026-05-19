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
        $pending = $this->pending->ensureLinkCode();
        $qty = (int) $pending->quantity;
        $participaciones = $qty === 1 ? '1 participación digital' : "{$qty} participaciones digitales";
        $subject = "Partilot: {$participaciones} — código {$pending->link_code}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.digital-sale-registration-invite');
    }
}
