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

        // Todo usuario de entidad (incluye gestor con/sin panel) respeta permisos de managers.
        if ($user->isEntity() && !$user->hasEntityManagerPermission($permission)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}

