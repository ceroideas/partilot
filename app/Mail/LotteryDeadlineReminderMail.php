<?php

namespace App\Mail;

use App\Services\LotteryDeadlineReminderService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LotteryDeadlineReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param array<string, mixed> $context */
    public function __construct(
        public array $context,
        public string $recipientChannel
    ) {}

    public function envelope(): Envelope
    {
        $daysBefore = (int) $this->context['days_before'];
        $subject = $daysBefore === 0
            ? 'Último día para devolver participaciones - Partilot'
            : "Aviso de fecha límite de devolución ({$daysBefore} días) - Partilot";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lottery-deadline-reminder',
            with: [
                'messageText' => app(LotteryDeadlineReminderService::class)->buildMessage($this->context),
                'entityName' => $this->context['entity_name'],
                'lotteryName' => $this->context['lottery_name'],
                'deadlineLabel' => $this->context['deadline']->format('d/m/Y'),
                'pendingCount' => $this->context['pending_count'],
                'daysBefore' => $this->context['days_before'],
                'devolutionsUrl' => url('/devolutions'),
            ],
        );
    }
}
