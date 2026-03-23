<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Obliga a cambiar la contraseña por defecto (12345678) en cuentas de gestor de entidad.
 */
class RedirectIfEntityManagerLegacyPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->mustChangeEntityManagerLegacyPassword()) {
            return $next($request);
        }

        if ($request->routeIs('entity-manager.legacy-password.*')) {
            return $next($request);
        }

        return redirect()->route('entity-manager.legacy-password.show');
    }
}
