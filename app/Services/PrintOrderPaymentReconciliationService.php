<?php

namespace App\Services;

use App\Models\PrintConfiguration;
use App\Models\PrintOrder;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrintOrderPaymentReconciliationService
{
    public const ISSUE_PENDING_WITH_ORDER = 'pending_with_order';
    public const ISSUE_FAILED = 'payment_failed';
    public const ISSUE_PAID_WITHOUT_PI = 'paid_without_payment_intent';
    public const ISSUE_PRODUCTION_WITHOUT_PAYMENT = 'production_without_payment';
    public const ISSUE_STRIPE_MISMATCH = 'stripe_status_mismatch';
    public const ISSUE_AMOUNT_MISMATCH = 'amount_mismatch';
    public const ISSUE_STRIPE_UNREACHABLE = 'stripe_unreachable';

    /**
     * @return array{code: string, label: string, severity: string}|null
     */
    public function detectIssue(PrintOrder $order): ?array
    {
        $status = (string) ($order->payment_status ?? '');
        $provider = (string) ($order->payment_provider ?? '');
        $orderStatus = (string) $order->status;

        if ($status === PrintOrder::PAYMENT_STATUS_FAILED) {
            return $this->issue(self::ISSUE_FAILED, 'Pago fallido en Stripe', 'error');
        }

        if ($provider === 'stripe') {
            if ($status === PrintOrder::PAYMENT_STATUS_PAID && trim((string) $order->payment_intent_id) === '') {
                return $this->issue(self::ISSUE_PAID_WITHOUT_PI, 'Marcado como cobrado sin PaymentIntent', 'error');
            }

            if ($status === PrintOrder::PAYMENT_STATUS_PENDING) {
                return $this->issue(self::ISSUE_PENDING_WITH_ORDER, 'Pago Stripe pendiente de confirmar', 'warning');
            }

            if (
                in_array($orderStatus, [PrintOrder::STATUS_IN_PRODUCTION, PrintOrder::STATUS_SENT], true)
                && $status !== PrintOrder::PAYMENT_STATUS_PAID
            ) {
                return $this->issue(self::ISSUE_PRODUCTION_WITHOUT_PAYMENT, 'Orden en producción/enviada sin cobro confirmado', 'error');
            }
        } elseif ($status === PrintOrder::PAYMENT_STATUS_PENDING) {
            return $this->issue(self::ISSUE_PENDING_WITH_ORDER, 'Estado de cobro pendiente', 'warning');
        }

        return null;
    }

    /**
     * @return Collection<int, array{order: PrintOrder, issue: array{code: string, label: string, severity: string}}>
     */
    public function ordersWithIssues(?Collection $orders = null): Collection
    {
        $orders ??= PrintOrder::query()->orderByDesc('id')->get();

        return $orders
            ->map(function (PrintOrder $order) {
                $issue = $this->detectIssue($order);

                return $issue ? ['order' => $order, 'issue' => $issue] : null;
            })
            ->filter()
            ->values();
    }

    /**
     * @return array{ok: bool, message: string, changed: bool, issue?: array|null}
     */
    public function reconcile(PrintOrder $order, bool $dryRun = false): array
    {
        $localIssue = $this->detectIssue($order);

        if ((string) $order->payment_provider !== 'stripe' || trim((string) $order->payment_intent_id) === '') {
            if ($localIssue) {
                return [
                    'ok' => false,
                    'message' => $localIssue['label'],
                    'changed' => false,
                    'issue' => $localIssue,
                ];
            }

            return ['ok' => true, 'message' => 'Sin cobro Stripe que conciliar.', 'changed' => false];
        }

        $pi = $this->fetchPaymentIntent((string) $order->payment_intent_id);
        if ($pi === null) {
            return [
                'ok' => false,
                'message' => 'No se pudo consultar Stripe.',
                'changed' => false,
                'issue' => $this->issue(self::ISSUE_STRIPE_UNREACHABLE, 'Stripe no disponible o PI inexistente', 'warning'),
            ];
        }

        $stripeStatus = (string) ($pi['status'] ?? '');
        $stripeAmount = (int) ($pi['amount'] ?? 0);
        $expectedAmount = (int) round(((float) $order->quoted_amount) * 100);
        $updates = [];
        $messages = [];

        if ($stripeStatus === 'succeeded' && $order->payment_status !== PrintOrder::PAYMENT_STATUS_PAID) {
            $updates['payment_status'] = PrintOrder::PAYMENT_STATUS_PAID;
            if (! $order->paid_at) {
                $updates['paid_at'] = now();
            }
            $messages[] = 'Pago confirmado en Stripe → marcado como cobrado.';
        } elseif (in_array($stripeStatus, ['canceled', 'requires_payment_method'], true) && $order->payment_status === PrintOrder::PAYMENT_STATUS_PAID) {
            $updates['payment_status'] = PrintOrder::PAYMENT_STATUS_FAILED;
            $messages[] = 'Stripe indica pago no completado → marcado como fallido.';
        } elseif ($stripeStatus === 'processing' && $order->payment_status !== PrintOrder::PAYMENT_STATUS_PENDING) {
            $updates['payment_status'] = PrintOrder::PAYMENT_STATUS_PENDING;
            $messages[] = 'Pago en proceso en Stripe → pendiente.';
        }

        if ($stripeStatus === 'succeeded' && $expectedAmount > 0 && $stripeAmount !== $expectedAmount) {
            return [
                'ok' => false,
                'message' => 'Importe en Stripe ('.number_format($stripeAmount / 100, 2, ',', '.').'€) distinto del presupuesto ('.number_format($expectedAmount / 100, 2, ',', '.').'€). Revisar manualmente.',
                'changed' => false,
                'issue' => $this->issue(self::ISSUE_AMOUNT_MISMATCH, 'Importe Stripe ≠ presupuesto', 'error'),
            ];
        }

        if ($updates === []) {
            $issue = $this->detectIssue($order);

            return [
                'ok' => $issue === null,
                'message' => $issue ? $issue['label'] : 'Conciliado: sin cambios necesarios.',
                'changed' => false,
                'issue' => $issue,
            ];
        }

        if ($dryRun) {
            return [
                'ok' => true,
                'message' => '[Dry-run] '.implode(' ', $messages),
                'changed' => false,
            ];
        }

        DB::transaction(function () use ($order, $updates, $messages, $stripeStatus) {
            $order->fill($updates);
            $order->save();

            DB::table('print_order_status_audits')->insert([
                'print_order_id' => $order->id,
                'entity_id' => $order->entity_id,
                'set_id' => $order->set_id,
                'design_format_id' => $order->design_format_id,
                'user_id' => auth()->id(),
                'action' => 'payment_reconciled',
                'from_status' => null,
                'to_status' => null,
                'message' => implode(' ', $messages).' [Stripe: '.$stripeStatus.']',
                'created_at' => now(),
            ]);
        });

        $order->refresh();

        return [
            'ok' => true,
            'message' => implode(' ', $messages),
            'changed' => true,
            'issue' => $this->detectIssue($order),
        ];
    }

    /**
     * @return array{checked: int, changed: int, issues: int, details: list<array<string, mixed>>}
     */
    public function reconcileAll(bool $dryRun = false): array
    {
        $orders = PrintOrder::query()
            ->where('payment_provider', 'stripe')
            ->whereNotNull('payment_intent_id')
            ->where('payment_intent_id', '!=', '')
            ->orderBy('id')
            ->get();

        $details = [];
        $changed = 0;

        foreach ($orders as $order) {
            $result = $this->reconcile($order, $dryRun);
            if ($result['changed'] ?? false) {
                $changed++;
            }
            if (($result['issue'] ?? null) || ($result['changed'] ?? false) || ! ($result['ok'] ?? true)) {
                $details[] = [
                    'order_code' => $order->order_code,
                    'ok' => $result['ok'],
                    'message' => $result['message'],
                    'changed' => $result['changed'] ?? false,
                ];
            }
        }

        return [
            'checked' => $orders->count(),
            'changed' => $changed,
            'issues' => $this->ordersWithIssues($orders)->count(),
            'details' => $details,
        ];
    }

    /**
     * @return array{code: string, label: string, severity: string}
     */
    private function issue(string $code, string $label, string $severity): array
    {
        return ['code' => $code, 'label' => $label, 'severity' => $severity];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchPaymentIntent(string $paymentIntentId): ?array
    {
        if ($paymentIntentId === '') {
            return null;
        }

        $cfg = PrintConfiguration::first();
        $secretKey = trim((string) ($cfg->stripe_secret_key ?? ''));
        if ($secretKey === '') {
            $secretKey = (string) config('services.stripe.secret');
        }
        if ($secretKey === '') {
            return null;
        }

        try {
            $client = new Client(['base_uri' => 'https://api.stripe.com/v1/', 'timeout' => 15]);
            $response = $client->get('payment_intents/'.$paymentIntentId, [
                'auth' => [$secretKey, ''],
            ]);
            $payload = json_decode((string) $response->getBody(), true);

            return is_array($payload) ? $payload : null;
        } catch (\Throwable $e) {
            Log::warning('Print order payment reconciliation: Stripe PI fetch failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
