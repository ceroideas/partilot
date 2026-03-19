<?php

namespace App\Mail;

use App\Models\Entity;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EntityManagerInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $acceptUrl;
    public string $rejectUrl;

    public function __construct(
        public Entity $entity,
        public User $managerUser,
        public Manager $manager
    ) {
        $this->acceptUrl = route('entity-managers.confirm-accept', ['token' => $manager->confirmation_token]);
        $this->rejectUrl = route('entity-managers.confirm-reject', ['token' => $manager->confirmation_token]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Invitación como gestor de entidad - Partilot');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.entity-manager-invitation');
    }
}

