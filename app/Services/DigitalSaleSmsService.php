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

    public function findPendingForSeller(Seller $seller, int $pendingId): ?PendingDigitalSale
    {
        return PendingDigitalSale::query()
            ->where('seller_id', $seller->id)
            ->where('id', $pendingId)
            ->where('status', PendingDigitalSale::STATUS_PENDING)
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

        if ($pending->isExpired() || ! $pending->isStillValid()) {
            throw new \InvalidArgumentException('Esta venta pendiente ya no está disponible.');
        }

        $to = $this->normalizeSmsAddress($buyerPhone);
        if (! $to) {
            throw new \InvalidArgumentException('Teléfono no válido. Usa prefijo internacional (ej. 34600111222).');
        }

        $body = DigitalSaleBuyerMessageBuilder::build($pending);

        if (config('digital_sale_sms.driver') === 'log') {
            Log::info('[SMS venta digital] '.$to.': '.$body);

            return 'log-'.uniqid('', true);
        }

        return $this->httpSms->send($to, $body, 'pending-sale-'.$pending->id);
    }
}
