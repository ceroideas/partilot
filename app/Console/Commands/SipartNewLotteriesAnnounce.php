<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Difusión de sorteos nuevos o recién publicados en catálogo (distinto de resultados ya guardados en LotteryController).
 * Pendiente: criterio "nuevo" (created_at, status, visibilidad por administración).
 */
class SipartNewLotteriesAnnounce extends Command
{
    protected $signature = 'sipart:new-lotteries-announce
                            {--dry-run : No envía notificaciones}';

    protected $description = '[Stub] Notificar sorteos nuevos disponibles para compra';

    public function handle(): int
    {
        $this->warn('sipart:new-lotteries-announce — lógica pendiente de implementación.');
        $this->line('Ideas: usuarios opt-in; segmentar por administración/entidad; rate-limit por usuario.');

        if ($this->option('dry-run')) {
            $this->info('Modo --dry-run.');
        }

        return self::SUCCESS;
    }
}
