<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamento;

class DepartamentoController extends Controller
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
        $buscar       = trim($request->get('buscar', ''));
        $departamentos = Departamento::when($buscar, fn($q) =>
                            $q->where('nombre', 'LIKE', "%{$buscar}%")
                              ->orWhere('edificio', 'LIKE', "%{$buscar}%")
                         )
                         ->orderBy('nombre')
                         ->paginate(10);

        return view('admin.departamentos.index', compact('departamentos', 'buscar'));
    }

    public function create()
    {
        return view('admin.departamentos.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100|unique:departamento,nombre',
            'edificio' => 'nullable|string|max:100',
        ], [
            'nombre.required' => 'El nombre del departamento es obligatorio.',
            'nombre.unique'   => 'Ya existe un departamento con ese nombre.',
        ]);

        Departamento::create($request->only(['nombre', 'edificio']));

        return redirect()->route('admin.departamentos.index')
                         ->with('success', 'Departamento creado correctamente.');
    }

    public function edit($id)
    {
        $departamento = Departamento::findOrFail($id);
        return view('admin.departamentos.editar', compact('departamento'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'   => "required|string|max:100|unique:departamento,nombre,{$id},id_departamento",
            'edificio' => 'nullable|string|max:100',
        ]);

        Departamento::findOrFail($id)->update($request->only(['nombre', 'edificio']));

        return redirect()->route('admin.departamentos.index')
                         ->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy($id)
    {
        Departamento::findOrFail($id)->delete();

        return redirect()->route('admin.departamentos.index')
                         ->with('success', 'Departamento eliminado correctamente.');
    }
}
