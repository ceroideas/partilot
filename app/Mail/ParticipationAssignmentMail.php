<?php

namespace App\Mail;

use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipationAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $assignments; // Array agrupado por set/sorteo: [set_id => ['set' => Set, 'lottery' => Lottery, 'count' => int]]

    /**
     * Create a new message instance.
     */
    public function __construct(Seller $seller, array $assignments)
    {
        $this->seller = $seller;
        $this->assignments = $assignments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Asignación de Participaciones - Partilot',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.participation-assignment',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
