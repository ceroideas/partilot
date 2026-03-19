<?php

namespace App\Mail;

use App\Models\Devolution;
use App\Models\DevolutionDetail;
use App\Models\Manager;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DevolutionReturnedToAdministrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Devolution $devolution;
    public ?\App\Models\User $adminManagerUser;
    public \Illuminate\Support\Collection $returnedParticipations;

    public function __construct(Devolution $devolution)
    {
        $this->devolution = $devolution->loadMissing([
            'entity',
            'entity.administration',
            'entity.manager.user',
            'lottery',
        ]);

        // Gestor principal de la administración
        $admin = $this->devolution->entity?->administration;
        $this->adminManagerUser = $admin
            ? Manager::query()
                ->where('administration_id', $admin->id)
                ->where('is_primary', true)
                ->with('user')
                ->first()?->user
            : null;

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
            subject: 'Devolución recibida - Partilot',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.devolution-transmission-to-administration',
        );
    }
}

