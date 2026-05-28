<?php

namespace App\Services;

use App\Models\PendingDigitalSale;
use App\Models\Seller;
use Illuminate\Support\Facades\Log;

class DigitalSaleSmsService
{
    public function __construct(
        private HttpSmsClient $httpSms,
    ) {}

    public function isEnabled(): bool
    {
        if (! config('digital_sale_sms.enabled')) {
            return false;
        }

        $driver = (string) config('digital_sale_sms.driver', 'httpsms');

        if ($driver === 'log') {
            return true;
        }

        if ($driver === 'httpsms') {
            return $this->httpSms->isConfigured();
        }

        return false;
    }

    public function maxResends(): int
    {
        return max(0, (int) config('digital_sale_sms.max_resends', 1));
    }

    public function maxSendsPerPending(): int
    {
        return 1 + $this->maxResends();
    }

    public function canSendToBuyer(PendingDigitalSale $pending): bool
    {
        return (int) $pending->buyer_sms_sent_count < $this->maxSendsPerPending();
    }

    public function sendsRemaining(PendingDigitalSale $pending): int
    {
        return max(0, $this->maxSendsPerPending() - (int) $pending->buyer_sms_sent_count);
    }

    public function findPendingForSeller(Seller $seller, int $pendingId): ?PendingDigitalSale
    {
        return PendingDigitalSale::query()
            ->where('seller_id', $seller->id)
            ->where('id', $pendingId)
            ->pendingNotExpired()
            ->with(['entity', 'lottery'])
            ->first();
    }

    /**
     * Normaliza teléfono a E.164 (ej. +34600111222).
     */
    public function normalizeSmsAddress(string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        $country = (string) config('digital_sale_sms.default_country_code', '34');
        if (strlen($digits) === 9 && in_array($digits[0], ['6', '7', '8', '9'], true)) {
            $digits = $country.$digits;
        }

        if (strlen($digits) < 8 || strlen($digits) > 15) {
            return null;
        }

        return '+'.$digits;
    }

    /**
     * @return string ID del mensaje (httpSMS o log)
     */
    public function sendToBuyer(PendingDigitalSale $pending, string $buyerPhone): string
    {
        if (! $this->isEnabled()) {
            throw new \RuntimeException('SMS de venta digital no está configurado en el servidor.');
        }

        if (! $pending->isStillValid()) {
            if ($pending->status !== PendingDigitalSale::STATUS_PENDING) {
                throw new \InvalidArgumentException('Esta venta ya no está pendiente (el comprador puede haberla reclamado).');
            }
            if (! $pending->valid_until) {
                throw new \InvalidArgumentException('Esta venta pendiente no tiene fecha de validez.');
            }
            $hasta = $pending->valid_until->timezone(config('app.timezone'))->format('d/m/Y');
            $months = (int) config('digital_sale.wallet_validity_months_after_draw', 3);

            throw new \InvalidArgumentException(
                "La reserva ha caducado (plazo de {$months} meses desde el sorteo, hasta {$hasta}). Ya no se puede enviar el SMS."
            );
        }

        if (! $this->canSendToBuyer($pending)) {
            $max = $this->maxResends();
            $msg = $max === 0
                ? 'Solo se permite un envío de SMS por venta.'
                : 'Solo se permite 1 reenvío por venta. Ya se ha enviado el SMS el máximo de veces.';

            throw new \InvalidArgumentException($msg);
        }

        $to = $this->normalizeSmsAddress($buyerPhone);
        if (! $to) {
            throw new \InvalidArgumentException('Teléfono no válido. Usa prefijo internacional (ej. 34600111222).');
        }

        $body = DigitalSaleBuyerMessageBuilder::build($pending);

        if (config('digital_sale_sms.driver') === 'log') {
            Log::info('[SMS venta digital] '.$to.': '.$body);
            $messageId = 'log-'.uniqid('', true);
        } else {
            $messageId = $this->httpSms->send($to, $body, 'pending-sale-'.$pending->id);
        }

        // Solo incrementar el contador (evitar save() que en algunos MySQL tocaba valid_until).
        PendingDigitalSale::query()
            ->whereKey($pending->id)
            ->update(['buyer_sms_sent_count' => (int) $pending->buyer_sms_sent_count + 1]);

        return $messageId;
    }
}
