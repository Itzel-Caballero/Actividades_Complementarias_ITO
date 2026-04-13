<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carrera;

class CarreraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('admin')) {
                abort(403, 'Acceso denegado.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $buscar   = trim($request->get('buscar', ''));
        $carreras = Carrera::when($buscar, fn($q) =>
                        $q->where('nombre', 'LIKE', "%{$buscar}%")
                    )
                    ->withCount('alumnos')
                    ->orderBy('nombre')
                    ->paginate(10);

        return view('admin.carreras.index', compact('carreras', 'buscar'));
    }

    public function create()
    {
        return view('admin.carreras.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:carrera,nombre',
        ], [
            'nombre.required' => 'El nombre de la carrera es obligatorio.',
            'nombre.unique'   => 'Ya existe una carrera con ese nombre.',
        ]);

        Carrera::create(['nombre' => $request->nombre]);

        return redirect()->route('admin.carreras.index')
                         ->with('success', 'Carrera creada correctamente.');
    }

    public function edit($id)
    {
        $carrera = Carrera::findOrFail($id);
        return view('admin.carreras.editar', compact('carrera'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => "required|string|max:150|unique:carrera,nombre,{$id},id_carrera",
        ], [
            'nombre.required' => 'El nombre de la carrera es obligatorio.',
            'nombre.unique'   => 'Ya existe una carrera con ese nombre.',
        ]);

        Carrera::findOrFail($id)->update(['nombre' => $request->nombre]);

        return redirect()->route('admin.carreras.index')
                         ->with('success', 'Carrera actualizada correctamente.');
    }

    public function destroy($id)
    {
        $carrera = Carrera::withCount('alumnos')->findOrFail($id);

        if ($carrera->alumnos_count > 0) {
            return redirect()->route('admin.carreras.index')
                             ->with('error', "No se puede eliminar: la carrera tiene {$carrera->alumnos_count} alumno(s) registrado(s).");
        }

        $carrera->delete();
        return redirect()->route('admin.carreras.index')
                         ->with('success', 'Carrera eliminada correctamente.');
    }
}
