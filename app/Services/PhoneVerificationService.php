<?php

namespace App\Services;

use App\Models\PhoneVerificationCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

/**
 * Verificación de teléfono por OTP SMS.
 * Desarrollo: SMS_DRIVER=log (código en storage/logs/laravel.log).
 * Producción: SMS_DRIVER=twilio con cuenta Twilio.
 */
class PhoneVerificationService
{
    public function sendVerificationCode(string $phone): void
    {
        $phone = $this->normalizePhone($phone);
        $this->assertCanSendTo($phone);

        $code = $this->generateCode();
        $message = 'Tu código de verificación Partilot es: '.$code;

        PhoneVerificationCode::query()
            ->where('phone', $phone)
            ->whereNull('verified_at')
            ->delete();

        PhoneVerificationCode::create([
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes((int) config('sms.code_ttl_minutes', 10)),
        ]);

        $this->dispatchSms($phone, $message);

        Cache::put($this->cooldownCacheKey($phone), true, (int) config('sms.resend_cooldown_seconds', 60));
    }

    public function verifyCode(string $phone, string $code): bool
    {
        $phone = $this->normalizePhone($phone);
        $code = trim($code);

        if ($code === '') {
            return false;
        }

        $row = PhoneVerificationCode::query()
            ->where('phone', $phone)
            ->whereNull('verified_at')
            ->where('expires_at', '>=', now())
            ->latest('id')
            ->first();

        if (! $row || ! Hash::check($code, $row->code_hash)) {
            return false;
        }

        $row->update(['verified_at' => now()]);

        return true;
    }

    /**
     * Normaliza teléfono si el usuario lo indicó; null si dejó el campo vacío.
     */
    public function resolveOptionalPhone(?string $phone): ?string
    {
        $phone = trim((string) $phone);
        if ($phone === '') {
            return null;
        }

        return $this->normalizePhone($phone);
    }

    public function smsVerificationRequired(?string $phone): bool
    {
        return (bool) config('sms.enabled') && trim((string) $phone) !== '';
    }

    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^\d+]/', '', trim($phone)) ?? '';
        if ($phone === '') {
            throw new \InvalidArgumentException('Introduce un número de teléfono válido.');
        }

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        $country = (string) config('sms.default_country_code', '34');
        if (str_starts_with($phone, $country)) {
            return '+'.$phone;
        }

        if (strlen($phone) === 9 && in_array($phone[0], ['6', '7', '8', '9'], true)) {
            return '+'.$country.$phone;
        }

        return '+'.$phone;
    }

    private function assertCanSendTo(string $phone): void
    {
        if (Cache::has($this->cooldownCacheKey($phone))) {
            $seconds = (int) config('sms.resend_cooldown_seconds', 60);
            throw new \RuntimeException("Espera {$seconds} segundos antes de pedir otro código.");
        }

        $driver = config('sms.driver', 'log');
        if ($driver === 'log') {
            return;
        }

        if ($driver === 'twilio') {
            if (! config('sms.twilio.sid') || ! config('sms.twilio.token') || ! config('sms.twilio.from')) {
                throw new \RuntimeException('SMS no configurado: revisa TWILIO_SID, TWILIO_AUTH_TOKEN y TWILIO_FROM en .env');
            }

            return;
        }

        if ($driver === 'vonage') {
            if (! config('sms.vonage.key') || ! config('sms.vonage.secret') || ! config('sms.vonage.from')) {
                throw new \RuntimeException('SMS no configurado: revisa VONAGE_API_KEY, VONAGE_API_SECRET y VONAGE_FROM en .env');
            }

            return;
        }

        throw new \RuntimeException("Driver SMS no soportado: {$driver}");
    }

    private function dispatchSms(string $phone, string $message): void
    {
        match (config('sms.driver', 'log')) {
            'twilio' => $this->sendViaTwilio($phone, $message),
            'vonage' => $this->sendViaVonage($phone, $message),
            default => Log::info("[SMS verificación] {$phone}: {$message}"),
        };
    }

    private function sendViaTwilio(string $phone, string $message): void
    {
        $sid = (string) config('sms.twilio.sid');
        $token = (string) config('sms.twilio.token');
        $from = (string) config('sms.twilio.from');

        if ($sid === '' || $token === '' || $from === '') {
            throw new \RuntimeException('Configura TWILIO_SID, TWILIO_AUTH_TOKEN y TWILIO_FROM en .env');
        }

        try {
            if (class_exists(TwilioClient::class)) {
                $twilio = new TwilioClient($sid, $token);
                $twilio->messages->create($phone, [
                    'from' => $from,
                    'body' => $message,
                ]);

                return;
            }

            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->timeout(15)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'To' => $phone,
                    'From' => $from,
                    'Body' => $message,
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException($response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Twilio SMS error', [
                'message' => $e->getMessage(),
                'to' => $phone,
            ]);
            throw new \RuntimeException('No se pudo enviar el SMS. Comprueba que el número sea correcto.');
        }
    }

    private function sendViaVonage(string $phone, string $message): void
    {
        $response = Http::timeout(15)->post('https://rest.nexmo.com/sms/json', [
            'api_key' => config('sms.vonage.key'),
            'api_secret' => config('sms.vonage.secret'),
            'to' => ltrim($phone, '+'),
            'from' => config('sms.vonage.from'),
            'text' => $message,
        ]);

        $data = $response->json();
        $status = $data['messages'][0]['status'] ?? null;
        if (! $response->successful() || ($status !== null && $status !== '0')) {
            Log::error('Vonage SMS error', ['body' => $response->body(), 'to' => $phone]);
            throw new \RuntimeException('No se pudo enviar el SMS. Comprueba que el número sea correcto.');
        }
    }

    private function generateCode(): string
    {
        $length = max(4, (int) config('sms.code_length', 6));
        $max = (10 ** $length) - 1;

        return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
    }

    private function cooldownCacheKey(string $phone): string
    {
        return 'sms_cooldown:'.sha1($phone);
    }
}
