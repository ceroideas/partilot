<?php

namespace App\Http\Controllers;

use App\Models\PrintConfiguration;
use App\Models\PrintOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = (string) $request->header('Stripe-Signature', '');
        $cfg = PrintConfiguration::first();
        $webhookSecret = trim((string) ($cfg->stripe_webhook_secret ?? ''));
        if ($webhookSecret === '') {
            $webhookSecret = (string) config('services.stripe.webhook_secret');
        }

        if ($webhookSecret !== '' && !$this->isValidSignature($payload, $sigHeader, $webhookSecret)) {
            return response()->json(['ok' => false, 'message' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);
        if (!is_array($event) || !isset($event['type'])) {
            return response()->json(['ok' => false, 'message' => 'Invalid payload'], 400);
        }

        $type = (string) $event['type'];
        $object = $event['data']['object'] ?? [];

        if ($type === 'payment_intent.succeeded') {
            $paymentIntentId = (string) ($object['id'] ?? '');
            if ($paymentIntentId !== '') {
                PrintOrder::where('payment_intent_id', $paymentIntentId)->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
        } elseif ($type === 'payment_intent.payment_failed') {
            $paymentIntentId = (string) ($object['id'] ?? '');
            if ($paymentIntentId !== '') {
                PrintOrder::where('payment_intent_id', $paymentIntentId)->update([
                    'payment_status' => PrintOrder::PAYMENT_STATUS_FAILED,
                ]);
            }
        }

        Log::info('Stripe webhook received', ['type' => $type]);

        return response()->json(['ok' => true]);
    }

    private function isValidSignature(string $payload, string $sigHeader, string $secret): bool
    {
        if ($sigHeader === '' || $secret === '') {
            return false;
        }

        $parts = [];
        foreach (explode(',', $sigHeader) as $pair) {
            $tuple = explode('=', trim($pair), 2);
            if (count($tuple) === 2) {
                $parts[$tuple[0]] = $tuple[1];
            }
        }

        $timestamp = $parts['t'] ?? null;
        $signature = $parts['v1'] ?? null;
        if (!$timestamp || !$signature) {
            return false;
        }

        $signedPayload = $timestamp . '.' . $payload;
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        return hash_equals($expected, $signature);
    }
}

