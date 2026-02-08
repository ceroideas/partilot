<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Seller;

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

            if (Auth::user()->isClient() || Auth::user()->isSeller()) {
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

    /**
     * API: Login para aplicación móvil (solo vendedores activos con usuario vinculado)
     */
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Las credenciales proporcionadas no coinciden con nuestros registros.'
            ], 401);
        }

        // Solo vendedores pueden acceder a la app móvil para marcar ventas
        if (!$user->isSeller()) {
            return response()->json([
                'success' => false,
                'message' => 'Solo los vendedores pueden acceder a esta aplicación.'
            ], 403);
        }

        // Obtener el vendedor vinculado al usuario
        $seller = Seller::where('user_id', $user->id)->first();

        // Debe tener usuario vinculado (seller con user_id)
        if (!$seller) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un perfil de vendedor asociado. Contacta con tu administrador.'
            ], 403);
        }

        // Verificar que el vendedor esté activo y no bloqueado
        if ((int) $seller->status !== Seller::STATUS_ACTIVE) {
            $message = match ((int) $seller->status) {
                Seller::STATUS_BLOCKED => 'Tu cuenta de vendedor está bloqueada. Contacta con tu administrador.',
                Seller::STATUS_INACTIVE => 'Tu cuenta de vendedor está inactiva. Contacta con tu administrador.',
                Seller::STATUS_PENDING => 'Tu cuenta de vendedor está pendiente de confirmación.',
                default => 'Tu cuenta de vendedor no está habilitada.',
            };
            return response()->json([
                'success' => false,
                'message' => $message
            ], 403);
        }

        // Crear token cifrado (sin tabla personal_access_tokens)
        $payload = [
            'user_id' => $user->id,
            'exp' => now()->addDays(30)->timestamp,
        ];
        $token = Crypt::encrypt($payload);

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user,
            'seller' => $seller->load('entities'),
            'message' => 'Login exitoso'
        ]);
    }

    /**
     * API: Registro (si aplica)
     */
    public function apiRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_CLIENT, // Por defecto cliente
        ]);

        $payload = ['user_id' => $user->id, 'exp' => now()->addDays(30)->timestamp];
        $token = Crypt::encrypt($payload);

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user,
            'message' => 'Registro exitoso'
        ], 201);
    }

    /**
     * API: Logout
     */
    public function apiLogout(Request $request)
    {
        // Token cifrado es stateless - no hay nada que revocar
        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    /**
     * API: Refresh token (crear nuevo token)
     */
    public function apiRefresh(Request $request)
    {
        $user = $request->user();

        // Crear nuevo token cifrado
        $payload = ['user_id' => $user->id, 'exp' => now()->addDays(30)->timestamp];
        $token = Crypt::encrypt($payload);
        $response = [
            'success' => true,
            'token' => $token,
            'user' => $user
        ];
        if ($user->isSeller()) {
            $seller = Seller::where('user_id', $user->id)->with('entities')->first();
            if ($seller) {
                $response['seller'] = $seller;
            }
        }
        return response()->json($response);
    }
} 