<?php

namespace App\Console\Commands;

use App\Models\PrintOrder;
use App\Services\PrintOrderPaymentReconciliationService;
use Illuminate\Console\Command;

class SipartPendingPaymentsCheck extends Command
{
    protected $signature = 'sipart:pending-payments-check
                            {--dry-run : Consulta Stripe y muestra cambios sin persistir}';

    protected $description = 'Reconciliar pagos Stripe de órdenes de imprenta y listar incidencias';

    public function handle(PrintOrderPaymentReconciliationService $reconciliation): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->info('Modo --dry-run: no se persistirán cambios.');
        }

        $summary = $reconciliation->reconcileAll($dryRun);
        $this->line('Órdenes Stripe revisadas: '.$summary['checked']);
        $this->line('Actualizadas: '.$summary['changed']);
        $this->line('Con incidencia tras revisión: '.$summary['issues']);

        foreach ($summary['details'] as $row) {
            $prefix = ($row['ok'] ?? false) ? '  OK' : '  !!';
            $this->line($prefix.' '.$row['order_code'].': '.$row['message']);
        }

        $pending = PrintOrder::query()
            ->where(function ($q) {
                $q->whereIn('payment_status', [PrintOrder::PAYMENT_STATUS_PENDING, PrintOrder::PAYMENT_STATUS_FAILED])
                    ->orWhere(function ($q2) {
                        $q2->where('payment_provider', 'stripe')
                            ->whereIn('status', [PrintOrder::STATUS_IN_PRODUCTION, PrintOrder::STATUS_SENT])
                            ->where('payment_status', '!=', PrintOrder::PAYMENT_STATUS_PAID);
                    });
            })
            ->count();

        if ($pending > 0) {
            $this->warn('Quedan '.$pending.' orden(es) con posible incidencia de cobro. Revisa Configuración → Órdenes Imprenta.');
        } else {
            $this->info('No hay órdenes con cobro claramente pendiente o fallido.');
        }

        return self::SUCCESS;
    }
}
