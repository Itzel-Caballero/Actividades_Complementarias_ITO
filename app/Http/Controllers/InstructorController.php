<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\Inscripcion;
use App\Models\Calificacion;
use App\Models\Semestre;
use App\Models\Alumno;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    private function getInstructor()
    {
        $instructor = Instructor::where('id_instructor', auth()->id())
            ->with('grupos')
            ->first();

        abort_if(!$instructor, 403, 'No tienes un perfil de instructor.');
        return $instructor;
    }

    /**
     * Retorna el semestre activo o null si no existe.
     */
    private function getSemestreActivo(): ?Semestre
    {
        return Semestre::where('status', 'activo')->first();
    }

    // ── Mis Grupos ────────────────────────────────────────────────────────────

    public function misGrupos()
    {
        $instructor      = $this->getInstructor();
        $semestreActivo  = $this->getSemestreActivo();

        $grupos = $instructor->grupos()
            ->with([
                'actividad',
                'semestre',
                'ubicacion',
                'horarios.dia',
                'inscripciones.alumno.usuario',
                'inscripciones.alumno.carrera',
                'inscripciones.calificaciones',
            ])
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('instructor.mis-grupos', compact('grupos', 'instructor', 'semestreActivo'));
    }

    // ── Calificar ─────────────────────────────────────────────────────────────

    public function calificar($id_inscripcion)
    {
        // Verificar periodo activo
        $semestreActivo = $this->getSemestreActivo();
        abort_if(!$semestreActivo, 403, 'No hay un periodo activo. No puedes calificar en este momento.');

        $instructor = $this->getInstructor();

        $inscripcion = Inscripcion::with([
            'alumno.usuario',
            'alumno.carrera',
            'grupo.actividad',
            'calificaciones',
        ])->findOrFail($id_inscripcion);

        abort_unless(
            $instructor->grupos->contains('id_grupo', $inscripcion->id_grupo),
            403,
            'No tienes permiso para calificar este alumno.'
        );

        $calificacion = $inscripcion->calificaciones->first();

        return view('instructor.calificar', compact('inscripcion', 'calificacion', 'semestreActivo'));
    }

    public function guardarCalificacion(Request $request, $id_inscripcion)
    {
        // Verificar periodo activo
        abort_if(!$this->getSemestreActivo(), 403, 'No hay un periodo activo.');

        $instructor = $this->getInstructor();

        $request->validate([
            'desempenio'    => 'required|in:malo,bueno,excelente',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $inscripcion = Inscripcion::with(['calificaciones', 'grupo.actividad'])->findOrFail($id_inscripcion);

        abort_unless(
            $instructor->grupos->contains('id_grupo', $inscripcion->id_grupo),
            403
        );

        $estatusAnterior = $inscripcion->estatus;

        Calificacion::updateOrCreate(
            ['id_inscripcion' => $id_inscripcion],
            [
                'desempenio'    => $request->desempenio,
                'observaciones' => $request->observaciones,
            ]
        );

        // Aprobado si bueno o excelente; reprobado si malo
        $nuevoEstatus = in_array($request->desempenio, ['bueno', 'excelente'])
            ? 'aprobado'
            : 'reprobado';

        $inscripcion->estatus = $nuevoEstatus;
        $inscripcion->save();

        // Sumar créditos al alumno solo cuando pasa a aprobado por primera vez
        if ($nuevoEstatus === 'aprobado' && $estatusAnterior !== 'aprobado') {
            $creditos = optional($inscripcion->grupo->actividad)->creditos ?? 0;
            if ($creditos > 0) {
                Alumno::where('id_alumno', $inscripcion->id_alumno)
                    ->increment('creditos_acumulados', $creditos);
            }
        }

        // Si se revierte de aprobado a reprobado, descontar créditos
        if ($nuevoEstatus === 'reprobado' && $estatusAnterior === 'aprobado') {
            $creditos = optional($inscripcion->grupo->actividad)->creditos ?? 0;
            if ($creditos > 0) {
                Alumno::where('id_alumno', $inscripcion->id_alumno)
                    ->decrement('creditos_acumulados', $creditos);
            }
        }

        return redirect()
            ->route('instructor.mis-grupos')
            ->with('success', 'Calificación guardada correctamente.');
    }

    // ── Editar perfil del instructor ──────────────────────────────────────────

    public function editarPerfil()
    {
        $instructor = $this->getInstructor();
        $user       = auth()->user();
        return view('instructor.perfil', compact('instructor', 'user'));
    }

    public function actualizarPerfil(Request $request)
    {
        $user       = auth()->user();
        $instructor = $this->getInstructor();

        $request->validate([
            'nombre'           => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono'         => 'nullable|string|max:20',
            'especialidad'     => 'nullable|string|max:100',
        ]);

        $user->update([
            'nombre'           => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'telefono'         => $request->telefono,
        ]);

        $instructor->update([
            'especialidad' => $request->especialidad,
        ]);

        return redirect()->route('home')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
