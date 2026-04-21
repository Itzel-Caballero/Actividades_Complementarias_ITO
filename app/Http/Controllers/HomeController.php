<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Semestre;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // ── ALUMNO ────────────────────────────────────────────────────────
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

        // ── INSTRUCTOR ────────────────────────────────────────────────────
        if ($user->hasRole('instructor')) {
            $semestreActivo = Semestre::where('status', 'activo')->first();

            $instructor = Instructor::where('id_instructor', $user->id)
                ->with([
                    'departamento',
                    'grupos.actividad',
                    'grupos.semestre',
                    'grupos.inscripciones.calificaciones',
                    'grupos.inscripciones.alumno.usuario',
                    'grupos.inscripciones.alumno.carrera',
                ])
                ->first();

            $totalGrupos      = $instructor ? $instructor->grupos->count() : 0;
            $gruposActivos    = $instructor ? $instructor->grupos->where('estatus', 'abierta')->count() : 0;
            $totalAlumnos     = $instructor ? $instructor->grupos->sum('cupo_ocupado') : 0;
            $totalCalificados = 0;
            $totalPendientes  = 0;

            // Alumnos sin calificar para mostrar en la tabla del dashboard
            $alumnosPendientes = collect();

            if ($instructor) {
                foreach ($instructor->grupos as $grupo) {
                    foreach ($grupo->inscripciones as $inscripcion) {
                        if ($inscripcion->calificaciones->count() > 0) {
                            $totalCalificados++;
                        } else {
                            $totalPendientes++;
                            // Agregar datos mínimos para la tabla
                            $alumnosPendientes->push([
                                'id_inscripcion' => $inscripcion->id_inscripcion,
                                'nombre'         => optional($inscripcion->alumno->usuario)->nombre . ' '
                                                  . optional($inscripcion->alumno->usuario)->apellido_paterno,
                                'num_control'    => optional($inscripcion->alumno->usuario)->num_control,
                                'actividad'      => optional($grupo->actividad)->nombre,
                                'grupo'          => $grupo->grupo,
                            ]);
                        }
                    }
                }
            }

            return view('instructor.dashboard', compact(
                'instructor',
                'semestreActivo',
                'totalGrupos',
                'gruposActivos',
                'totalAlumnos',
                'totalCalificados',
                'totalPendientes',
                'alumnosPendientes'
            ));
        }

        // ── ADMIN / COORDINADOR / etc. ────────────────────────────────────
        return view('home');
    }
}
