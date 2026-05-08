<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Semestre;

class VerificarSemestreActivo
{
    /**
     * Si el usuario es alumno y NO hay un semestre activo,
     * redirige al dashboard con un mensaje informativo.
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->hasRole('alumno')) {
            $semestreActivo = Semestre::where('status', 'activo')->exists();

            if (!$semestreActivo) {
                return redirect()->route('home')
                    ->with('error', 'El período de inscripciones no está disponible. No hay un semestre activo en este momento.');
            }
        }

        return $next($request);
    }
}
