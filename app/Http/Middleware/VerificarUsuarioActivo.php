<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarUsuarioActivo
{
    /**
     * Bloquea el acceso a cualquier ruta protegida si el usuario
     * autenticado tiene activo = false (fue deshabilitado por el admin).
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->activo) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Tu cuenta ha sido deshabilitada. Contacta al administrador.');
        }

        return $next($request);
    }
}
