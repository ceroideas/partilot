<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        
        return view('login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El formato del email no es válido.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isClient()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tu cuenta no tiene acceso al panel.',
                ])->withInput($request->only('email'));
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->only('email'));
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Mostrar el dashboard
     */
    public function dashboard()
    {
        return view('welcome');
    }

    /**
     * Crear un usuario administrador por defecto
     */
    public function createDefaultAdmin()
    {
        // Verificar si ya existe un usuario administrador
        $adminExists = User::where('email', 'admin@partilot.com')->exists();
        
        if (!$adminExists) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@partilot.com',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_SUPER_ADMIN,
            ]);
            
            return 'Usuario administrador creado exitosamente. Email: admin@partilot.com, Contraseña: admin123';
        }
        
        return 'El usuario administrador ya existe.';
    }
} 