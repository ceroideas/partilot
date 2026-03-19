<?php

namespace App\Mail;

use App\Models\Devolution;
use App\Models\DevolutionDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DevolutionReturnedToEntityManagerMail extends Mailable
{
    use Queueable, SerializesModels;

    public Devolution $devolution;
    public ?\App\Models\User $entityManagerUser;
    public \Illuminate\Support\Collection $returnedParticipations;

    public function __construct(Devolution $devolution)
    {
        $this->devolution = $devolution->loadMissing([
            'entity',
            'entity.manager.user',
            'entity.administration',
            'lottery',
        ]);

        $this->entityManagerUser = $this->devolution->entity?->manager?->user;

        $this->returnedParticipations = DevolutionDetail::query()
            ->where('devolution_id', $this->devolution->id)
            ->where('action', 'devolver')
            ->with('participation')
            ->get()
            ->pluck('participation')
            ->filter();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Devolución procesada - Partilot',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.devolution-transmission-to-entity-manager',
        );
    }
}

