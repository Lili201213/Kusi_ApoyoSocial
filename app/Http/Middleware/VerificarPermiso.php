<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    /**
     * Uso en rutas: ->middleware('permiso:beneficiarios.crear')
     * Se puede pasar más de un permiso: 'permiso:a,b' (basta con tener uno).
     */
    public function handle(Request $request, Closure $next, string ...$permisos): Response
    {
        $usuario = $request->user();

        if (! $usuario) {
            return redirect()->route('login');
        }

        foreach ($permisos as $permiso) {
            if ($usuario->tienePermiso($permiso)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
