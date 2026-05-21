<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envío de SMS vía httpSMS (móvil Android como pasarela).
 *
 * @see https://docs.httpsms.com/
 */
class HttpSmsClient
{
    private const API_URL = 'https://api.httpsms.com/v1/messages/send';

    public function isConfigured(): bool
    {
        return $this->apiKey() !== '' && $this->fromNumber() !== '';
    }

    public function apiKey(): string
    {
        return trim((string) config('services.httpsms.key'));
    }

    public function fromNumber(): string
    {
        $from = trim((string) config('services.httpsms.from'));
        if ($from === '') {
            return '';
        }

        if (! str_starts_with($from, '+')) {
            $from = '+'.$from;
        }

        return $from;
    }

    /**
     * @return string ID del mensaje en httpSMS
     */
    public function send(string $to, string $content, ?string $requestId = null): string
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('httpSMS no configurado: revisa HTTPSMS_API_KEY y HTTPSMS_FROM_NUMBER en .env');
        }

        $to = $this->ensureE164($to);
        if ($to === null) {
            throw new \InvalidArgumentException('Teléfono destino no válido para SMS.');
        }

        $payload = [
            'from' => $this->fromNumber(),
            'to' => $to,
            'content' => $content,
        ];

        if ($requestId !== null && $requestId !== '') {
            $payload['request_id'] = $requestId;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
                ->timeout(25)
                ->post(self::API_URL, $payload);

            if ($response->status() === 401) {
                throw new \RuntimeException('API key de httpSMS no válida (401).');
            }

            if (! $response->successful()) {
                $body = $response->json();
                $msg = is_array($body) ? ($body['message'] ?? $response->body()) : $response->body();
                throw new \RuntimeException(is_string($msg) ? $msg : 'Error HTTP '.$response->status());
            }

            $json = $response->json();
            if (! is_array($json) || ($json['status'] ?? '') !== 'success') {
                throw new \RuntimeException('Respuesta inesperada de httpSMS.');
            }

            $data = $json['data'] ?? [];
            $id = is_array($data) ? (string) ($data['id'] ?? '') : '';

            return $id !== '' ? $id : 'httpsms-'.uniqid('', true);
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('httpSMS send error', [
                'message' => $e->getMessage(),
                'to' => $to,
            ]);
            throw new \RuntimeException('No se pudo enviar el SMS. Comprueba httpSMS (app Android en línea y número remitente).');
        }
    }

    public function ensureE164(string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) < 8 || strlen($digits) > 15) {
            return null;
        }

        return '+'.$digits;
    }
}
