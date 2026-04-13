<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Semestre;

class SemestreController extends Controller
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

    public function index()
    {
        $semestres = Semestre::withCount('grupos')
                             ->orderByDesc('año')
                             ->orderByDesc('periodo')
                             ->paginate(10);

        return view('admin.semestres.index', compact('semestres'));
    }

    public function create()
    {
        return view('admin.semestres.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'año'                        => 'required|integer|min:2000|max:2100',
            'periodo'                    => 'required|in:1,2',
            'fecha_inicio'               => 'required|date',
            'fecha_fin'                  => 'required|date|after_or_equal:fecha_inicio',
            'fecha_inicio_inscripciones' => 'required|date',
            'fecha_fin_inscripciones'    => 'required|date|after_or_equal:fecha_inicio_inscripciones',
        ], [
            'año.required'     => 'El año es obligatorio.',
            'periodo.required' => 'El periodo es obligatorio.',
            'fecha_fin.after_or_equal'               => 'La fecha fin debe ser igual o posterior a la de inicio.',
            'fecha_fin_inscripciones.after_or_equal'  => 'La fecha fin de inscripciones debe ser igual o posterior a la de inicio.',
        ]);

        // Evitar duplicado año+periodo
        $existe = Semestre::where('año', $request->año)
                          ->where('periodo', $request->periodo)
                          ->exists();
        if ($existe) {
            return back()->withInput()
                         ->with('error', 'Ya existe un semestre con ese año y periodo.');
        }

        Semestre::create($request->only([
            'año', 'periodo', 'fecha_inicio', 'fecha_fin',
            'fecha_inicio_inscripciones', 'fecha_fin_inscripciones',
        ]));

        return redirect()->route('admin.semestres.index')
                         ->with('success', 'Semestre creado correctamente.');
    }

    public function edit($id)
    {
        $semestre = Semestre::findOrFail($id);
        return view('admin.semestres.editar', compact('semestre'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'año'                        => 'required|integer|min:2000|max:2100',
            'periodo'                    => 'required|in:1,2',
            'fecha_inicio'               => 'required|date',
            'fecha_fin'                  => 'required|date|after_or_equal:fecha_inicio',
            'fecha_inicio_inscripciones' => 'required|date',
            'fecha_fin_inscripciones'    => 'required|date|after_or_equal:fecha_inicio_inscripciones',
        ]);

        $semestre = Semestre::findOrFail($id);

        // Verificar duplicado excluyendo el actual
        $existe = Semestre::where('año', $request->año)
                          ->where('periodo', $request->periodo)
                          ->where('id_semestre', '!=', $id)
                          ->exists();
        if ($existe) {
            return back()->withInput()
                         ->with('error', 'Ya existe un semestre con ese año y periodo.');
        }

        $semestre->update($request->only([
            'año', 'periodo', 'fecha_inicio', 'fecha_fin',
            'fecha_inicio_inscripciones', 'fecha_fin_inscripciones',
        ]));

        return redirect()->route('admin.semestres.index')
                         ->with('success', 'Semestre actualizado correctamente.');
    }

    public function destroy($id)
    {
        $semestre = Semestre::withCount('grupos')->findOrFail($id);

        if ($semestre->grupos_count > 0) {
            return redirect()->route('admin.semestres.index')
                             ->with('error', "No se puede eliminar: el semestre tiene {$semestre->grupos_count} grupo(s) asociado(s).");
        }

        $semestre->delete();
        return redirect()->route('admin.semestres.index')
                         ->with('success', 'Semestre eliminado correctamente.');
    }
}
