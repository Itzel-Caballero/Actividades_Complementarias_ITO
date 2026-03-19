<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscripcion;
use App\Models\Alumno;
use App\Models\Grupo;

class InscripcionController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        // Verificar que el usuario es alumno
        $alumno = Alumno::where('id_alumno', $user->id)->first();

        if (!$alumno) {
            return redirect()->back()
                ->with('error', 'Solo los alumnos pueden inscribirse a actividades.');
        }

        $grupo = Grupo::findOrFail($request->id_grupo);

        // Verificar que hay cupo
        if ($grupo->cupo_ocupado >= $grupo->cupo_maximo) {
            return redirect()->back()
                ->with('error', 'El grupo ya no tiene cupo disponible.');
        }

        // Verificar que no esté ya inscrito
        $yaInscrito = Inscripcion::where('id_alumno', $alumno->id_alumno)
            ->where('id_grupo', $grupo->id_grupo)
            ->exists();

        if ($yaInscrito) {
            return redirect()->back()
                ->with('error', 'Ya estás inscrito en este grupo.');
        }

        // Crear inscripción
        Inscripcion::create([
            'id_alumno'         => $alumno->id_alumno,
            'id_grupo'          => $grupo->id_grupo,
            'fecha_inscripcion' => now(),
            'estatus'           => 'inscrito',
        ]);

        // Actualizar cupo ocupado
        $grupo->increment('cupo_ocupado');

        return redirect()->back()
            ->with('success', '¡Inscripción exitosa! Ahora estás inscrito en este grupo.');
    }

    public function index()
    {
        $user    = auth()->user();
        $alumno  = Alumno::where('id_alumno', $user->id)->first();

        if (!$alumno) {
            return redirect()->route('actividades.index')
                ->with('error', 'No tienes un perfil de alumno.');
        }

        $inscripciones = Inscripcion::with([
            'grupo.actividad',
            'grupo.horarios.dia',
            'grupo.ubicacion',
        ])->where('id_alumno', $alumno->id_alumno)->get();

        return view('inscripciones.index', compact('inscripciones', 'alumno'));
    }
}