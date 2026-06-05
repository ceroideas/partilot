<?php

namespace App\Http\Middleware;

use App\Support\ActiveEntityContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveEntityContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user) {
            ActiveEntityContext::ensureValidSession($request, $user);
        }

        return $next($request);
    }
}
