<?php

/**
 * @deprecated No se usa. SMS = httpSMS; manual = wa.me en la app.
 */

namespace App\Services;

use App\Models\PendingDigitalSale;
use App\Models\Seller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

class DigitalSaleWhatsAppService
{
    public function isEnabled(): bool
    {
        if (! config('whatsapp.enabled')) {
            return false;
        }

        $sid = (string) config('whatsapp.twilio.sid');
        $token = (string) config('whatsapp.twilio.token');
        $from = (string) config('whatsapp.twilio.from');

        return $sid !== '' && $token !== '' && $from !== '';
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
     * Normaliza teléfono a E.164 con prefijo whatsapp: para Twilio.
     */
    public function normalizeWhatsAppAddress(string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        $country = (string) config('whatsapp.default_country_code', '34');
        if (strlen($digits) === 9 && in_array($digits[0], ['6', '7', '8', '9'], true)) {
            $digits = $country.$digits;
        }

        if (strlen($digits) < 8 || strlen($digits) > 15) {
            return null;
        }

        return 'whatsapp:+'.$digits;
    }

    public function buildBuyerMessage(PendingDigitalSale $pending): string
    {
        return DigitalSaleBuyerMessageBuilder::build($pending);
    }

    /**
     * Envía el mensaje al comprador. No devuelve el código al llamador (solo uso interno).
     *
     * @return string Message SID de Twilio o identificador en modo log
     */
    public function sendToBuyer(PendingDigitalSale $pending, string $buyerPhone): string
    {
        if (! $this->isEnabled()) {
            throw new \RuntimeException('WhatsApp no está configurado en el servidor.');
        }

        if ($pending->isExpired() || ! $pending->isStillValid()) {
            throw new \InvalidArgumentException('Esta venta pendiente ya no está disponible.');
        }

        $to = $this->normalizeWhatsAppAddress($buyerPhone);
        if (! $to) {
            throw new \InvalidArgumentException('Teléfono no válido. Usa prefijo internacional (ej. 34600111222).');
        }

        $body = $this->buildBuyerMessage($pending);
        $from = $this->normalizeFromAddress((string) config('whatsapp.twilio.from'));

        if (config('whatsapp.use_template') && config('whatsapp.twilio.content_sid')) {
            return $this->sendViaTwilioTemplate($to, $from, $pending);
        }

        return $this->sendViaTwilioBody($to, $from, $body);
    }

    private function normalizeFromAddress(string $from): string
    {
        $from = trim($from);
        if (str_starts_with($from, 'whatsapp:')) {
            return $from;
        }

        $digits = preg_replace('/\D+/', '', $from) ?? '';
        if ($digits === '') {
            throw new \RuntimeException('TWILIO_WHATSAPP_FROM no válido.');
        }

        return 'whatsapp:+'.$digits;
    }

    private function sendViaTwilioBody(string $to, string $from, string $body): string
    {
        $sid = (string) config('whatsapp.twilio.sid');
        $token = (string) config('whatsapp.twilio.token');

        try {
            if (class_exists(TwilioClient::class)) {
                $twilio = new TwilioClient($sid, $token);
                $message = $twilio->messages->create($to, [
                    'from' => $from,
                    'body' => $body,
                ]);

                return (string) $message->sid;
            }

            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->timeout(20)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'To' => $to,
                    'From' => $from,
                    'Body' => $body,
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException($response->body());
            }

            return (string) ($response->json('sid') ?? 'ok');
        } catch (\Throwable $e) {
            Log::error('Twilio WhatsApp error', [
                'message' => $e->getMessage(),
                'to' => $to,
            ]);
            throw new \RuntimeException('No se pudo enviar el WhatsApp. Comprueba el número y la configuración Twilio.');
        }
    }

    /**
     * Plantilla opcional (requiere contentSid aprobado en Twilio con variables acordes).
     */
    private function sendViaTwilioTemplate(string $to, string $from, PendingDigitalSale $pending): string
    {
        $pending->ensureLinkCode();
        $sid = (string) config('whatsapp.twilio.sid');
        $token = (string) config('whatsapp.twilio.token');
        $contentSid = (string) config('whatsapp.twilio.content_sid');

        $variables = json_encode([
            '1' => (string) $pending->quantity,
            '2' => $pending->link_code,
            '3' => $pending->registrationUrlForShare(),
        ], JSON_UNESCAPED_UNICODE);

        try {
            if (class_exists(TwilioClient::class)) {
                $twilio = new TwilioClient($sid, $token);
                $message = $twilio->messages->create($to, [
                    'from' => $from,
                    'contentSid' => $contentSid,
                    'contentVariables' => $variables,
                ]);

                return (string) $message->sid;
            }

            throw new \RuntimeException('SDK Twilio no disponible.');
        } catch (\Throwable $e) {
            Log::warning('WhatsApp plantilla falló, reintento con body', ['error' => $e->getMessage()]);

            return $this->sendViaTwilioBody($to, $from, $this->buildBuyerMessage($pending));
        }
    }
}
