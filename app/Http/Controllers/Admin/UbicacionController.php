<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ubicacion;

class UbicacionController extends Controller
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
        $buscar     = trim($request->get('buscar', ''));
        $ubicaciones = Ubicacion::when($buscar, fn($q) =>
                          $q->where('espacio', 'LIKE', "%{$buscar}%")
                       )
                       ->orderBy('espacio')
                       ->paginate(10);

        return view('admin.ubicaciones.index', compact('ubicaciones', 'buscar'));
    }

    public function create()
    {
        return view('admin.ubicaciones.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'espacio'   => 'required|string|max:100|unique:ubicacion,espacio',
            'capacidad' => 'required|integer|min:1|max:9999',
        ], [
            'espacio.required'   => 'El nombre del espacio es obligatorio.',
            'espacio.unique'     => 'Ya existe una ubicación con ese nombre.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.min'      => 'La capacidad debe ser al menos 1.',
        ]);

        Ubicacion::create($request->only(['espacio', 'capacidad']));

        return redirect()->route('admin.ubicaciones.index')
                         ->with('success', 'Ubicación creada correctamente.');
    }

    public function edit($id)
    {
        $ubicacion = Ubicacion::findOrFail($id);
        return view('admin.ubicaciones.editar', compact('ubicacion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'espacio'   => "required|string|max:100|unique:ubicacion,espacio,{$id},id_ubicacion",
            'capacidad' => 'required|integer|min:1|max:9999',
        ]);

        Ubicacion::findOrFail($id)->update($request->only(['espacio', 'capacidad']));

        return redirect()->route('admin.ubicaciones.index')
                         ->with('success', 'Ubicación actualizada correctamente.');
    }

    public function destroy($id)
    {
        Ubicacion::findOrFail($id)->delete();

        return redirect()->route('admin.ubicaciones.index')
                         ->with('success', 'Ubicación eliminada correctamente.');
    }
}
