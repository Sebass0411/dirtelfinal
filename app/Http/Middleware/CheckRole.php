<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verificar si el usuario está autenticado
        if (! $request->user()) {
            Log::info('Usuario no autenticado');
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Verificar si el usuario tiene alguno de los roles especificados
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                Log::info('Usuario tiene el rol: ' . $role);
                return $next($request);
            }
        }

        // Si el usuario no tiene ninguno de los roles especificados, devolver un error 403 (Forbidden)
        Log::info('Usuario no tiene los roles necesarios');
        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
