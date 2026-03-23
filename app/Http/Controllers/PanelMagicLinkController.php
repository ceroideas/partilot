<?php

namespace App\Http\Controllers;

use App\Models\Administration;
use App\Models\PanelAccessToken;
use App\Models\User;
use Illuminate\Http\Request;

class PanelMagicLinkController extends Controller
{
    public function show(string $token)
    {
        $record = PanelAccessToken::findValidForPlain($token);
        if (! $record) {
            return view('auth.panel-magic-link-invalid');
        }

        $user = $record->user;
        if (! $user || ! $user->isPanelAccount() || $user->panel_account_type !== 'administration') {
            return view('auth.panel-magic-link-invalid');
        }

        return view('auth.panel-set-password', [
            'token' => $token,
            'panelUsername' => $user->panel_login_username ?? '',
        ]);
    }

    public function update(Request $request, string $token)
    {
        $record = PanelAccessToken::findValidForPlain($token);
        if (! $record) {
            return redirect()->route('login')->withErrors(['email' => 'El enlace no es válido o ha caducado. Solicite uno nuevo al administrador.']);
        }

        $user = $record->user;
        if (! $user || ! $user->isPanelAccount() || $user->panel_account_type !== 'administration') {
            return redirect()->route('login')->withErrors(['email' => 'El enlace no es válido.']);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Indique una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        // El modelo User aplica cast "hashed" a password (no usar Hash::make aquí).
        $user->password = $request->input('password');
        $user->save();

        $record->markUsed();

        // Al definir contraseña del panel, pasar la administración de pendiente a activa.
        $administration = Administration::query()->find($user->panel_account_id);
        if ($administration && ($administration->status === null || $administration->status === -1)) {
            $administration->update(['status' => 1]);
        }

        return redirect()->route('login')->with('success', 'Contraseña establecida. Ya puede iniciar sesión con su usuario y la nueva contraseña.');
    }
}
