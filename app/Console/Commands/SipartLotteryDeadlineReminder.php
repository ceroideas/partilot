<?php

namespace App\Console\Commands;

use App\Services\LotteryDeadlineReminderService;
use Illuminate\Console\Command;

class SipartLotteryDeadlineReminder extends Command
{
    protected $signature = 'sipart:lottery-deadline-reminder
                            {--dry-run : Solo simula destinatarios sin enviar correos}';

    protected $description = 'Envía avisos por email 3/2/1/0 días antes del cierre de devoluciones por sorteo';

    public function handle(LotteryDeadlineReminderService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Modo --dry-run: no se enviarán correos ni se guardará log.');
        }

        $result = $service->runEmailReminders($dryRun);

        $this->info("Contextos activos: {$result['contexts']}");
        $this->info('Correos '.($dryRun ? 'simulados' : 'enviados').": {$result['sent']}");
        $this->line("Omitidos (ya enviados hoy): {$result['skipped']}");

        return self::SUCCESS;
    }
}
