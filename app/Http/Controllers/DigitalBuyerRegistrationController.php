<?php

namespace App\Http\Controllers;

use App\Mail\UserWelcomeMail;
use App\Models\PendingDigitalSale;
use App\Models\User;
use App\Rules\MinimumAge;
use App\Services\CommunicationEmailService;
use App\Services\PendingDigitalSaleService;
use App\Services\PhoneVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DigitalBuyerRegistrationController extends Controller
{
    public function show(string $token, PendingDigitalSaleService $service)
    {
        $pending = $service->findValidByToken($token);
        if (! $pending) {
            return view('auth.digital-buyer-register-expired');
        }

        if (User::where('email', $pending->email)->exists()) {
            return view('auth.digital-buyer-register-exists', compact('pending'));
        }

        $pending->ensureLinkCode();
        $linkCodePrefill = (string) request()->query('codigo', $pending->link_code);

        return view('auth.digital-buyer-register', compact('pending', 'token', 'linkCodePrefill'));
    }

    public function store(Request $request, string $token, PendingDigitalSaleService $service)
    {
        $pending = $service->findValidByToken($token);
        if (! $pending) {
            return back()->withErrors(['token' => 'El enlace ha caducado o ya no es válido.']);
        }

        if (User::where('email', $pending->email)->exists()) {
            return redirect()
                ->route('digital-buyer.register', ['token' => $token])
                ->with('info', 'Ya existe una cuenta con este correo. Inicia sesión en la app Partilot.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'birthday' => ['required', 'date', 'before:today', new MinimumAge(18)],
            'password' => 'required|string|min:6|confirmed',
            'aceptar_condiciones' => 'required|accepted',
            'link_code' => 'nullable|string|min:5|max:12',
            'sms_code' => [
                Rule::requiredIf(fn () => app(PhoneVerificationService::class)
                    ->smsVerificationRequired($request->input('phone'))),
                'nullable',
                'string',
                'size:'.config('sms.code_length', 6),
            ],
        ], [
            'sms_code.required' => 'Si indicas teléfono, debes verificarlo con el código SMS.',
            'aceptar_condiciones.accepted' => 'Debes aceptar las condiciones de uso.',
        ]);

        $phoneVerification = app(PhoneVerificationService::class);
        try {
            $phone = $phoneVerification->resolveOptionalPhone($request->phone);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['phone' => $e->getMessage()]);
        }

        if ($phoneVerification->smsVerificationRequired($request->phone)) {
            if (! $phoneVerification->verifyCode($phone, (string) $request->sms_code)) {
                return back()->withInput()->withErrors(['sms_code' => 'Código SMS incorrecto o caducado. Solicita uno nuevo.']);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'last_name2' => $request->last_name2,
            'email' => $pending->email,
            'phone' => $phone,
            'password' => Hash::make($request->password),
            'birthday' => $request->birthday,
            'role' => User::ROLE_CLIENT,
            'status' => true,
        ]);

        if ($request->filled('link_code')) {
            try {
                $service->claimByLinkCode($user, (string) $request->link_code);
            } catch (\InvalidArgumentException $e) {
                return back()->withInput()->withErrors(['link_code' => $e->getMessage()]);
            }
        }
        $service->completePendingSalesForUser($user);

        try {
            app(CommunicationEmailService::class)->sendAndLog(
                recipientEmail: (string) $user->email,
                recipientRole: 'usuario',
                recipientUser: $user,
                messageType: 'user_welcome',
                templateKey: null,
                mailClass: UserWelcomeMail::class,
                mailPayload: ['user_id' => $user->id],
                context: ['user_id' => $user->id, 'source' => 'digital_buyer_register'],
            );
        } catch (\Throwable $e) {
            \Log::warning('Bienvenida tras registro comprador digital: '.$e->getMessage());
        }

        return view('auth.digital-buyer-register-success', [
            'pending' => $pending->fresh(),
            'user' => $user,
        ]);
    }
}
