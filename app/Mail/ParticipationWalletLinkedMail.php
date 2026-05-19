<?php

namespace App\Mail;

use App\Models\Participation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipationWalletLinkedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Participation $participation
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Participación vinculada a tu cartera - Partilot');
    }

    public function content(): Content
    {
        $this->participation->loadMissing(['set.entity', 'set.reserve.lottery']);

        return new Content(view: 'emails.participation-wallet-linked');
    }
}
