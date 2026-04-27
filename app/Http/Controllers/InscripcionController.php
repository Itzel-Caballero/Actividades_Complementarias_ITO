<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscripcion;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\ActividadComplementaria;

class InscripcionController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $alumno = Alumno::where('id_alumno', $user->id)->first();

        if (!$alumno) {
            return redirect()->route('actividades.index')
                ->with('error', 'No tienes un perfil de alumno.');
        }

        // Inscripción activa actual (inscrito o cursando)
        $inscripcionActiva = Inscripcion::with([
            'grupo.actividad',
            'grupo.horarios.dia',
            'grupo.ubicacion',
        ])->where('id_alumno', $alumno->id_alumno)
          ->whereIn('estatus', ['inscrito', 'cursando'])
          ->first();

        // Historial (aprobado, reprobado, dado_de_baja)
        $historial = Inscripcion::with([
            'grupo.actividad',
            'calificaciones',
        ])->where('id_alumno', $alumno->id_alumno)
          ->whereIn('estatus', ['aprobado', 'reprobado', 'dado_de_baja'])
          ->orderByDesc('updated_at')
          ->get();

        // Otras actividades disponibles (solo si YA tiene inscripción activa, para ver opciones de cambio)
        $otrasActividades = null;
        if ($inscripcionActiva) {
            $otrasActividades = ActividadComplementaria::with(['departamento', 'grupos'])
                ->where('disponible', true)
                ->where('id_actividad', '!=', $inscripcionActiva->grupo->id_actividad)
                ->paginate(6);
        }

        return view('inscripciones.index', compact(
            'alumno',
            'inscripcionActiva',
            'historial',
            'otrasActividades'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_grupo' => 'required|integer|exists:grupo,id_grupo',
        ], [
            'id_grupo.required' => 'Debes seleccionar un grupo.',
            'id_grupo.exists'   => 'El grupo seleccionado no existe.',
        ]);

        $user   = auth()->user();
        $alumno = Alumno::where('id_alumno', $user->id)->first();

        if (!$alumno) {
            return redirect()->back()
                ->with('error', 'Solo los alumnos pueden inscribirse a actividades.');
        }

        // Verificar que NO tenga ya una inscripción activa
        $inscripcionActiva = Inscripcion::where('id_alumno', $alumno->id_alumno)
            ->whereIn('estatus', ['inscrito', 'cursando'])
            ->exists();

        if ($inscripcionActiva) {
            return redirect()->back()
                ->with('error', 'Ya tienes una actividad complementaria activa. Debes darte de baja antes de inscribirte a otra.');
        }

        $grupo = Grupo::findOrFail($request->id_grupo);

        // Verificar que hay cupo
        if ($grupo->cupo_ocupado >= $grupo->cupo_maximo) {
            return redirect()->back()
                ->with('error', 'El grupo ya no tiene cupo disponible.');
        }

        // Verificar que no esté ya inscrito en ese grupo específico
        $yaInscrito = Inscripcion::where('id_alumno', $alumno->id_alumno)
            ->where('id_grupo', $grupo->id_grupo)
            ->exists();

        if ($yaInscrito) {
            return redirect()->back()
                ->with('error', 'Ya estuviste inscrito en este grupo.');
        }

        // Crear inscripción
        Inscripcion::create([
            'id_alumno'         => $alumno->id_alumno,
            'id_grupo'          => $grupo->id_grupo,
            'fecha_inscripcion' => now(),
            'estatus'           => 'inscrito',
        ]);

        $grupo->increment('cupo_ocupado');

        return redirect()->route('inscripciones.index')
            ->with('success', '¡Inscripción exitosa! Ahora estás inscrito en el grupo ' . $grupo->grupo . '.');
    }

public function darBaja(Inscripcion $inscripcion)
    {
        $user   = auth()->user();
        $alumno = Alumno::where('id_alumno', $user->id)->first();

        // Verificar que la inscripción pertenece al alumno
        if (!$alumno || $inscripcion->id_alumno !== $alumno->id_alumno) {
            return redirect()->back()
                ->with('error', 'No tienes permiso para realizar esta acción.');
        }

        // Solo se puede dar de baja si está inscrito o cursando
        if (!in_array($inscripcion->estatus, ['inscrito', 'cursando'])) {
            return redirect()->back()
                ->with('error', 'No puedes darte de baja de esta inscripción.');
        }

        // Cambiar estatus y liberar cupo
        $inscripcion->update(['estatus' => 'dado_de_baja']);
        $inscripcion->grupo->decrement('cupo_ocupado');

        return redirect()->route('inscripciones.index')
            ->with('success', 'Te has dado de baja correctamente. Ahora puedes inscribirte a otra actividad.');
    }
}
