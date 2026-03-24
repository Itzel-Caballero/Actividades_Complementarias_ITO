<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActividadComplementaria;
use App\Models\Departamento;
use App\Models\Carrera;
use App\Models\Alumno;
use App\Models\Inscripcion;

class ActividadComplementariaController extends Controller
{
    public function index()
    {
        // Admin ve tabla de gestión, alumno ve catálogo
        if (auth()->user()->hasRole('admin')) {
            $actividades = ActividadComplementaria::with(['departamento', 'grupos', 'carreras'])
                ->paginate(10);
            return view('actividades.admin_index', compact('actividades'));
        }

        $actividades = ActividadComplementaria::with(['departamento', 'grupos', 'carreras'])
            ->where('disponible', true)
            ->paginate(9);

        // Verificar si el alumno ya tiene una inscripción activa
        $inscripcionActiva = null;
        $alumno = Alumno::where('id_alumno', auth()->id())->first();
        if ($alumno) {
            $inscripcionActiva = Inscripcion::with('grupo.actividad')
                ->where('id_alumno', $alumno->id_alumno)
                ->whereIn('estatus', ['inscrito', 'cursando'])
                ->first();
        }

        return view('actividades.index', compact('actividades', 'inscripcionActiva'));
    }

    public function show($id)
    {
        $actividad = ActividadComplementaria::with([
            'departamento',
            'carreras',
            'grupos.instructor.usuario',
            'grupos.ubicacion',
            'grupos.horarios.dia',
        ])->findOrFail($id);

        return view('actividades.show', compact('actividad'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('admin')) abort(403);
        $departamentos = Departamento::all();
        $carreras      = Carrera::all();
        return view('actividades.create', compact('departamentos', 'carreras'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) abort(403);

        $request->validate([
            'nombre'          => 'required|string|max:150',
            'id_departamento' => 'required',
            'creditos'        => 'required|in:1,2',
        ]);

        $actividad = ActividadComplementaria::create([
            'nombre'          => $request->nombre,
            'descripcion'     => $request->descripcion,
            'id_departamento' => $request->id_departamento,
            'creditos'        => $request->creditos,
            'nivel_actividad' => $request->nivel_actividad,
            'disponible'      => $request->disponible ?? 1,
            'requisitos'      => $request->requisitos,
        ]);

        if ($request->carreras) {
            $actividad->carreras()->sync($request->carreras);
        }

        return redirect()->route('actividades.index')
                         ->with('success', 'Actividad creada correctamente.');
    }

    public function edit($id)
    {
        if (!auth()->user()->hasRole('admin')) abort(403);
        $actividad        = ActividadComplementaria::with('carreras')->findOrFail($id);
        $departamentos    = Departamento::all();
        $carreras         = Carrera::all();
        $carrerasAsignadas = $actividad->carreras->pluck('id_carrera')->toArray();
        return view('actividades.edit', compact('actividad', 'departamentos', 'carreras', 'carrerasAsignadas'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) abort(403);

        $request->validate([
            'nombre'          => 'required|string|max:150',
            'id_departamento' => 'required',
            'creditos'        => 'required|in:1,2',
        ]);

        $actividad = ActividadComplementaria::findOrFail($id);
        $actividad->update([
            'nombre'          => $request->nombre,
            'descripcion'     => $request->descripcion,
            'id_departamento' => $request->id_departamento,
            'creditos'        => $request->creditos,
            'nivel_actividad' => $request->nivel_actividad,
            'disponible'      => $request->disponible ?? 1,
            'requisitos'      => $request->requisitos,
        ]);

        $actividad->carreras()->sync($request->carreras ?? []);

        return redirect()->route('actividades.index')
                         ->with('success', 'Actividad actualizada correctamente.');
    }

  public function destroy($id)
{
    if (!auth()->user()->hasRole('admin')) abort(403);
    
    $actividad = ActividadComplementaria::findOrFail($id);
    
    // Eliminar en cascada todas las relaciones
    $actividad->carreras()->detach();
    
    foreach ($actividad->grupos as $grupo) {
        // Eliminar horarios del grupo
        $grupo->horarios()->delete();
        
        // Eliminar inscripciones del grupo
        foreach ($grupo->inscripciones as $inscripcion) {
            $inscripcion->calificaciones()->delete();
            $inscripcion->delete();
        }
        
        $grupo->delete();
    }
    
    $actividad->delete();
    
    return redirect()->route('actividades.index')
                     ->with('success', 'Actividad eliminada correctamente.');
}
}