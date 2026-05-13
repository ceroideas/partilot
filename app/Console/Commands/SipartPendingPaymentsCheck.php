<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Punto de entrada para reconciliar pagos pendientes (Stripe, transferencias, etc.).
 * Pendiente: consultar modelos (PrintOrder, …), Stripe API o webhooks reprocesados.
 */
class SipartPendingPaymentsCheck extends Command
{
    protected $signature = 'sipart:pending-payments-check
                            {--dry-run : Solo describe qué haría, sin persistir cambios}';

    protected $description = '[Stub] Revisar y alertar pagos pendientes / incoherentes';

    public function handle(): int
    {
        $this->warn('sipart:pending-payments-check — lógica pendiente de implementación.');
        $this->line('Ideas: órdenes con payment_status pending; reconciliar PaymentIntent; notificar gestores o inbox.');

        if ($this->option('dry-run')) {
            $this->info('Modo --dry-run: no se aplicarían cambios.');
        }

        return self::SUCCESS;
    }
}
