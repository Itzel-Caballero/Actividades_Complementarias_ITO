<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Inscripcion;
use App\Models\Instructor;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // ── Dashboard para ALUMNO ─────────────────────────────────────────
        if ($user->hasRole('alumno')) {
            $alumno = Alumno::with('carrera')
                ->where('id_alumno', $user->id)
                ->first();

            $inscripcionActiva = null;
            if ($alumno) {
                $inscripcionActiva = Inscripcion::with([
                    'grupo.actividad',
                    'grupo.horarios.dia',
                    'grupo.ubicacion',
                ])->where('id_alumno', $alumno->id_alumno)
                  ->whereIn('estatus', ['inscrito', 'cursando'])
                  ->first();
            }

            return view('alumno.dashboard', compact('alumno', 'inscripcionActiva'));
        }

        // ── Dashboard para INSTRUCTOR ─────────────────────────────────────
        if ($user->hasRole('instructor')) {
            $instructor = Instructor::where('id_instructor', $user->id)
                ->with([
                    'departamento',
                    'grupos.actividad',
                    'grupos.semestre',
                    'grupos.inscripciones.calificaciones',
                ])
                ->first();

            // Métricas calculadas
            $totalGrupos     = $instructor ? $instructor->grupos->count() : 0;
            $gruposActivos   = $instructor ? $instructor->grupos->where('estatus', 'abierta')->count() : 0;
            $totalAlumnos    = $instructor ? $instructor->grupos->sum('cupo_ocupado') : 0;

            $totalCalificados = 0;
            $totalPendientes  = 0;
            if ($instructor) {
                foreach ($instructor->grupos as $grupo) {
                    foreach ($grupo->inscripciones as $inscripcion) {
                        if ($inscripcion->calificaciones->count() > 0) {
                            $totalCalificados++;
                        } else {
                            $totalPendientes++;
                        }
                    }
                }
            }

            return view('instructor.dashboard', compact(
                'instructor',
                'totalGrupos',
                'gruposActivos',
                'totalAlumnos',
                'totalCalificados',
                'totalPendientes'
            ));
        }

        // ── Dashboard por defecto (admin, coordinador, etc.) ──────────────
        return view('home');
    }
}
