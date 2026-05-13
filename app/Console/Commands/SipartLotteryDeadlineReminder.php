<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Recordatorios automáticos ligados a sorteos (fecha límite de compra, escrutinio, etc.).
 * Pendiente: consultar Lottery.deadline_date / draw_date y usuarios con participaciones.
 */
class SipartLotteryDeadlineReminder extends Command
{
    protected $signature = 'sipart:lottery-deadline-reminder
                            {--dry-run : Solo simula destinatarios}';

    protected $description = '[Stub] Avisos de sorteos próximos a vencer o calendario';

    public function handle(): int
    {
        $this->warn('sipart:lottery-deadline-reminder — lógica pendiente de implementación.');
        $this->line('Ideas: AppInboxNotificationService + FCM; ventanas N horas antes de deadline_date.');

        if ($this->option('dry-run')) {
            $this->info('Modo --dry-run.');
        }

        return self::SUCCESS;
    }
}
