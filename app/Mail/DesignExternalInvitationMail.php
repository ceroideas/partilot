<?php

namespace App\Mail;

use App\Models\DesignExternalInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DesignExternalInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public DesignExternalInvitation $invitation;

    public string $inviteUrl;

    public function __construct(DesignExternalInvitation $invitation)
    {
        $this->invitation = $invitation;
        $this->inviteUrl = route('design.external.invite', ['token' => $invitation->token]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitación para realizar un diseño - Partilot',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.design-external-invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
