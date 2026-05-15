<?php

namespace App\Http\Controllers;

use App\Services\PhoneVerificationService;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    public function sendCode(Request $request, PhoneVerificationService $sms)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ], [
            'phone.required' => 'El teléfono es obligatorio.',
        ]);

        if (! config('sms.enabled')) {
            return response()->json([
                'success' => false,
                'message' => 'La verificación por SMS no está activa.',
            ], 400);
        }

        try {
            $sms->sendVerificationCode($request->phone);

            return response()->json([
                'success' => true,
                'message' => 'Te hemos enviado un SMS con el código de verificación.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function config()
    {
        return response()->json([
            'enabled' => (bool) config('sms.enabled'),
            'code_length' => (int) config('sms.code_length', 6),
            'resend_cooldown_seconds' => (int) config('sms.resend_cooldown_seconds', 60),
        ]);
    }
}
