<?php

namespace App\Console\Commands;

use App\Services\LotteryDeadlineClosureService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SipartLotteryDeadlineClosure extends Command
{
    protected $signature = 'sipart:lottery-deadline-closure
                            {--dry-run : Simula sin crear devoluciones}
                            {--ignore-disabled : Ejecutar aunque LOTTERY_AUTO_DEADLINE_CLOSURE_ENABLED=false}
                            {--force : Repetir aunque ya exista log de cierre (solo pruebas)}
                            {--entity-id= : Limitar a una entidad}
                            {--lottery-id= : Limitar a un sorteo}
                            {--as-of= : Fecha de referencia YYYY-MM-DD (pruebas con sorteos pasados)}';

    protected $description = 'Cierra participaciones no devueltas tras la fecha límite (vendidas + deuda)';

    public function handle(LotteryDeadlineClosureService $service): int
    {
        if (! $service->isEnabled() && ! $this->option('ignore-disabled')) {
            $this->warn('Cierre automático desactivado (LOTTERY_AUTO_DEADLINE_CLOSURE_ENABLED=false).');
            $this->line('Usa --ignore-disabled para probar manualmente.');

            return self::SUCCESS;
        }

        $asOf = null;
        if ($this->option('as-of')) {
            try {
                $asOf = Carbon::parse($this->option('as-of'))->startOfDay();
            } catch (\Throwable) {
                $this->error('Fecha --as-of inválida. Usa formato YYYY-MM-DD.');

                return self::FAILURE;
            }
        }

        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Modo --dry-run: no se crearán devoluciones ni logs.');
        }

        $entityId = $this->option('entity-id') ? (int) $this->option('entity-id') : null;
        $lotteryId = $this->option('lottery-id') ? (int) $this->option('lottery-id') : null;

        $result = $service->run(
            dryRun: $dryRun,
            asOf: $asOf,
            entityId: $entityId,
            lotteryId: $lotteryId,
            force: (bool) $this->option('force')
        );

        $this->info("Candidatos procesados: {$result['processed']}");
        $this->info("Completados: {$result['completed']}");
        $this->line("Omitidos: {$result['skipped']}");
        $this->line("Fallidos: {$result['failed']}");

        foreach ($result['details'] as $detail) {
            $prefix = match ($detail['stat_key']) {
                'completed' => '[OK]',
                'failed' => '[ERROR]',
                default => '[SKIP]',
            };
            $this->line("{$prefix} {$detail['label']} — {$detail['message']}");
        }

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
