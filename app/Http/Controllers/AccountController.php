<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Datos de la cuenta de panel de administración (usuario fijo + cambio de contraseña).
     */
    public function myData()
    {
        $user = Auth::user();
        if (! $user || ! $user->isPanelAccount() || $user->panel_account_type !== 'administration') {
            abort(403, 'Esta sección solo está disponible para el acceso al panel de una administración.');
        }

        return view('account.my-data', ['user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (! $user || ! $user->isPanelAccount() || $user->panel_account_type !== 'administration') {
            abort(403);
        }

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Indique su contraseña actual.',
            'password.required' => 'Indique la nueva contraseña.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ]);

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual no es correcta.',
            ])->withInput($request->except('password', 'password_confirmation', 'current_password'));
        }

        $user->password = $request->input('password');
        $user->save();

        return redirect()->route('account.my-data')->with('success', 'Contraseña actualizada correctamente.');
    }
}
