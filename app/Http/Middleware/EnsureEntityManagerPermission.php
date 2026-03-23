<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEntityManagerPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        // Superadmin y administración mantienen acceso completo.
        if ($user->isSuperAdmin() || $user->isAdministration()) {
            return $next($request);
        }

        // Cuenta panel de entidad: misma UI que el gestor en modo supervisión (solo GET/HEAD).
        if ($user->isEntityPanelReadOnly() && ($request->isMethod('GET') || $request->isMethod('HEAD'))) {
            return $next($request);
        }

        // Todo usuario de entidad (incluye gestor con/sin panel) respeta permisos de managers.
        if ($user->isEntity() && ! $user->hasEntityManagerPermission($permission)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}

