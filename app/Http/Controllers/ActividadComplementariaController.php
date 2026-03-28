<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActividadComplementaria;
use App\Models\Departamento;
use App\Models\Carrera;
use App\Models\Alumno;
use App\Models\Inscripcion;
use App\Models\Grupo;
use App\Models\Instructor;

class ActividadComplementariaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin ve tabla de gestión de actividades
        if ($user->hasRole('admin')) {
            $actividades = ActividadComplementaria::with(['departamento', 'grupos', 'carreras'])
                ->paginate(10);
            return view('actividades.admin_index', compact('actividades'));
        }

        // Instructor ve solo los grupos que le corresponden
        if ($user->hasRole('instructor')) {
            $instructor = Instructor::where('id_instructor', $user->id)->first();

            if (!$instructor) {
                return view('actividades.instructor_grupos', ['grupos' => collect()]);
            }

            $grupos = Grupo::with(['actividad.departamento', 'ubicacion', 'horarios.dia', 'inscripciones'])
                ->where('id_instructor', $instructor->id_instructor)
                ->get();

            return view('actividades.instructor_grupos', compact('grupos'));
        }

        // Alumno ve catálogo de actividades disponibles
        $actividades = ActividadComplementaria::with(['departamento', 'grupos', 'carreras'])
            ->where('disponible', true)
            ->paginate(9);

        $inscripcionActiva = null;
        $alumno = Alumno::where('id_alumno', $user->id)->first();
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
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('coordinador')) abort(403);
        $departamentos = Departamento::all();
        $carreras      = Carrera::all();
        // Coordinador vuelve a su vista, admin a la suya
        return view('actividades.create', compact('departamentos', 'carreras'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('coordinador')) abort(403);

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

        $redirectRoute = auth()->user()->hasRole('coordinador')
            ? 'coordinador.actividades'
            : 'actividades.index';

        return redirect()->route($redirectRoute)
                         ->with('success', 'Actividad creada correctamente.');
    }

    public function edit($id)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('coordinador')) abort(403);
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

        $redirectRoute = auth()->user()->hasRole('coordinador')
            ? 'coordinador.actividades'
            : 'actividades.index';

        return redirect()->route($redirectRoute)
                         ->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('coordinador')) abort(403);
    
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
    
        $redirectRoute = auth()->user()->hasRole('coordinador')
            ? 'coordinador.actividades'
            : 'actividades.index';

        return redirect()->route($redirectRoute)
                         ->with('success', 'Actividad eliminada correctamente.');
    }
}