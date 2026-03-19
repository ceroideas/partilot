<?php

namespace App\Mail;

use App\Models\Entity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EntityManagerInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Entity $entity,
        public User $managerUser
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Invitación como gestor de entidad - Partilot');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.entity-manager-invitation');
    }
}

