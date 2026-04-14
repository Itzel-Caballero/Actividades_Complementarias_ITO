<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\Inscripcion;
use App\Models\Calificacion;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    private function getInstructor()
    {
        $instructor = Instructor::where('id_instructor', auth()->id())
            ->with('grupos')
            ->first();

        abort_if(!$instructor, 403, 'No tienes un perfil de instructor.');

        return $instructor;
    }

    /**
     * Panel principal: grupos + alumnos inscritos en cada grupo.
     */
    public function misGrupos()
    {
        $instructor = $this->getInstructor();

        $grupos = $instructor->grupos()
            ->with([
                'actividad',
                'semestre',
                'ubicacion',
                'horarios.dia',
                // Alumnos con toda la info necesaria para mostrar y calificar
                'inscripciones.alumno.usuario',
                'inscripciones.alumno.carrera',
                'inscripciones.calificaciones',
            ])
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('instructor.mis-grupos', compact('grupos', 'instructor'));
    }

    /**
     * Muestra el formulario para calificar a un alumno.
     */
    public function calificar($id_inscripcion)
    {
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

        return view('instructor.calificar', compact('inscripcion', 'calificacion'));
    }

    /**
     * Guarda o actualiza la calificación de un alumno.
     */
    public function guardarCalificacion(Request $request, $id_inscripcion)
    {
        $instructor = $this->getInstructor();

        $request->validate([
            'desempenio'    => 'required|in:0,1',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $inscripcion = Inscripcion::with('calificaciones')->findOrFail($id_inscripcion);

        abort_unless(
            $instructor->grupos->contains('id_grupo', $inscripcion->id_grupo),
            403
        );

        Calificacion::updateOrCreate(
            ['id_inscripcion' => $id_inscripcion],
            [
                'desempenio'    => (int) $request->desempenio,
                'observaciones' => $request->observaciones,
            ]
        );

        $inscripcion->estatus = $request->desempenio == 1 ? 'aprobado' : 'reprobado';
        $inscripcion->save();

        return redirect()
            ->route('instructor.mis-grupos')
            ->with('success', 'Calificación guardada correctamente.');
    }
}
