<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\ActividadComplementaria;
use App\Models\Instructor;
use App\Models\Semestre;
use App\Models\Ubicacion;

class GrupoController extends Controller
{
    private function soloAdmin()
    {
        if (!auth()->user()->hasRole('admin')) abort(403);
    }

    public function index()
    {
        $this->soloAdmin();

        $grupos = Grupo::with(['actividad', 'instructor.usuario', 'semestre', 'ubicacion', 'inscripciones'])
            ->orderBy('id_grupo', 'desc')
            ->paginate(15);

        return view('grupos.index', compact('grupos'));
    }

    public function create()
    {
        $this->soloAdmin();

        $actividades  = ActividadComplementaria::where('disponible', true)->orderBy('nombre')->get();
        $instructores = Instructor::with('usuario')->get();
        $semestres    = Semestre::orderBy('año', 'desc')->get();
        $ubicaciones  = Ubicacion::orderBy('espacio')->get();

        return view('grupos.create', compact('actividades', 'instructores', 'semestres', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $this->soloAdmin();

        $request->validate([
            'id_actividad'  => 'required|exists:actividad_complementaria,id_actividad',
            'id_semestre'   => 'required|exists:semestre,id_semestre',
            'grupo'         => 'required|string|max:10',
            'cupo_maximo'   => 'required|integer|min:1',
            'modalidad'     => 'required|in:presencial,virtual,hibrida',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'required|date|after:fecha_inicio',
            'id_instructor' => 'nullable|exists:instructor,id_instructor',
            'id_ubicacion'  => 'nullable|exists:ubicacion,id_ubicacion',
        ]);

        Grupo::create([
            'id_actividad'        => $request->id_actividad,
            'id_semestre'         => $request->id_semestre,
            'grupo'               => $request->grupo,
            'cupo_maximo'         => $request->cupo_maximo,
            'cupo_ocupado'        => 0,
            'modalidad'           => $request->modalidad,
            'estatus'             => $request->estatus ?? 'abierta',
            'fecha_inicio'        => $request->fecha_inicio,
            'fecha_fin'           => $request->fecha_fin,
            'id_instructor'       => $request->id_instructor,
            'id_ubicacion'        => $request->id_ubicacion,
            'materiales_requeridos' => $request->materiales_requeridos,
        ]);

        return redirect()->route('grupos.index')
                         ->with('success', 'Grupo creado correctamente.');
    }

    public function edit($id)
    {
        $this->soloAdmin();

        $grupo        = Grupo::with('instructor')->findOrFail($id);
        $actividades  = ActividadComplementaria::where('disponible', true)->orderBy('nombre')->get();
        $instructores = Instructor::with('usuario')->get();
        $semestres    = Semestre::orderBy('año', 'desc')->get();
        $ubicaciones  = Ubicacion::orderBy('espacio')->get();

        return view('grupos.edit', compact('grupo', 'actividades', 'instructores', 'semestres', 'ubicaciones'));
    }

    public function update(Request $request, $id)
    {
        $this->soloAdmin();

        $request->validate([
            'id_actividad'  => 'required|exists:actividad_complementaria,id_actividad',
            'id_semestre'   => 'required|exists:semestre,id_semestre',
            'grupo'         => 'required|string|max:10',
            'cupo_maximo'   => 'required|integer|min:1',
            'modalidad'     => 'required|in:presencial,virtual,hibrida',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'required|date|after:fecha_inicio',
            'id_instructor' => 'nullable|exists:instructor,id_instructor',
            'id_ubicacion'  => 'nullable|exists:ubicacion,id_ubicacion',
        ]);

        $grupo = Grupo::findOrFail($id);
        $grupo->update([
            'id_actividad'          => $request->id_actividad,
            'id_semestre'           => $request->id_semestre,
            'grupo'                  => $request->grupo,
            'cupo_maximo'           => $request->cupo_maximo,
            'modalidad'             => $request->modalidad,
            'estatus'               => $request->estatus,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
            'id_instructor'         => $request->id_instructor,
            'id_ubicacion'          => $request->id_ubicacion,
            'materiales_requeridos' => $request->materiales_requeridos,
        ]);

        return redirect()->route('grupos.index')
                         ->with('success', 'Grupo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $this->soloAdmin();

        $grupo = Grupo::findOrFail($id);

        foreach ($grupo->inscripciones as $inscripcion) {
            $inscripcion->calificaciones()->delete();
            $inscripcion->delete();
        }
        $grupo->horarios()->delete();
        $grupo->delete();

        return redirect()->route('grupos.index')
                         ->with('success', 'Grupo eliminado correctamente.');
    }

    // Asignar instructor rápido desde el listado
    public function asignarInstructor(Request $request, $id)
    {
        $this->soloAdmin();

        $request->validate([
            'id_instructor' => 'nullable|exists:instructor,id_instructor',
        ]);

        $grupo = Grupo::findOrFail($id);
        $grupo->update(['id_instructor' => $request->id_instructor]);

        return redirect()->back()->with('success', 'Instructor asignado correctamente.');
    }
}
