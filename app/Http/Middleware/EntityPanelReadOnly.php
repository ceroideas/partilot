<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * La cuenta de panel de la entidad puede navegar el mismo panel que el gestor (solo supervisión),
 * pero no realizar cambios (POST/PUT/PATCH/DELETE). También bloquea GET "delete" legados.
 */
class EntityPanelReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->isEntityPanelReadOnly()) {
            return $next($request);
        }

        $method = $request->method();

        if ($method === 'GET') {
            $name = $request->route()?->getName();
            if ($name && str_ends_with($name, '.delete')) {
                return $this->deny($request);
            }
            $path = trim($request->path(), '/');
            if (str_starts_with($path, 'entities/delete/')) {
                return $this->deny($request);
            }
        }

        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        return $this->deny($request);
    }

    private function deny(Request $request): Response
    {
        $message = 'Modo solo consulta: la cuenta de panel de la entidad no puede realizar cambios.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        }

        return redirect()->route('dashboard')->with('error', $message);
    }
}
