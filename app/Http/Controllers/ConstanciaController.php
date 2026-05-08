<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ConstanciaController extends Controller
{
    public function descargar($id_inscripcion)
    {
        $inscripcion = Inscripcion::with([
            'alumno.usuario',
            'alumno.carrera',
            'grupo.actividad.departamento',
            'grupo.instructor.usuario',
            'grupo.instructor.departamento',
            'grupo.horarios.dia',
            'grupo.ubicacion',
            'grupo.semestre',
            'calificaciones',
        ])->findOrFail($id_inscripcion);

        // Solo el alumno dueño puede descargar su constancia
        if ($inscripcion->id_alumno !== Auth::id()) {
            abort(403);
        }

        // Solo inscripciones aprobadas generan constancia
        if ($inscripcion->estatus !== 'aprobado') {
            return back()->with('error', 'Solo puedes descargar la constancia de actividades aprobadas.');
        }

        $pdf = Pdf::loadView('constancia.pdf', compact('inscripcion'))
            ->setPaper('letter', 'portrait');

        $nombreArchivo = 'Constancia_' . str_replace(' ', '_', $inscripcion->alumno->usuario->nombre) . '_' . $id_inscripcion . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
