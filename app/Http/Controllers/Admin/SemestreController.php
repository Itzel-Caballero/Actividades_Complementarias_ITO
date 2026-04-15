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
    // Obtener el periodo marcado como activo
    $periodoActual = Semestre::withCount('grupos')
        ->where('status', 'activo')
        ->first();

    // Obtener todos los periodos inactivos para el historial
    $historial = Semestre::withCount('grupos')
        ->where('status', 'inactivo')
        ->paginate(10); // Usamos paginate porque en tu index tienes {!! $historial->links() !!}

    return view('admin.semestres.index', compact('periodoActual', 'historial'));
}

    public function create()
    {
        // Validamos si ya existe un periodo activo antes de mostrar la vista
        if (Semestre::where('status', 'activo')->exists()) {
            return redirect()->route('admin.semestres.index')
                             ->with('error', 'No se puede crear un nuevo periodo mientras exista uno activo.');
        }

        return view('admin.semestres.crear');
    }

    public function store(Request $request)
{
    // 1. Validaciones de formato y año
    $año = $request->año;
    $periodo = $request->periodo;

    $request->validate([
        'año'                        => 'required|integer|min:2000|max:2100',
        'periodo'                    => 'required|in:1,2',
        'fecha_inicio'               => "required|date|after_or_equal:$año-01-01|before_or_equal:$año-12-31",
        'fecha_fin'                  => "required|date|after:fecha_inicio|before_or_equal:$año-12-31",
        'fecha_inicio_inscripciones' => "required|date|after_or_equal:$año-01-01|before_or_equal:$año-12-31",
        'fecha_fin_inscripciones'    => "required|date|after:fecha_inicio_inscripciones|before_or_equal:$año-12-31",
        'status'                     => 'required|in:activo,inactivo'
    ]);

    // 2. Validación lógica de Meses por Periodo
    $mesMin = ($periodo == 1) ? 1 : 8; // Ene o Ago
    $mesMax = ($periodo == 1) ? 6 : 12; // Jun o Dic
    $nombrePeriodo = ($periodo == 1) ? "Enero y Junio" : "Agosto y Diciembre";

    $fechasCheck = [
        'inicio del semestre' => $request->fecha_inicio,
        'fin del semestre' => $request->fecha_fin,
        'inicio de inscripciones' => $request->fecha_inicio_inscripciones,
        'fin de inscripciones' => $request->fecha_fin_inscripciones,
    ];

    foreach ($fechasCheck as $label => $fecha) {
        $mes = date('n', strtotime($fecha));
        if ($mes < $mesMin || $mes > $mesMax) {
            return back()->withInput()->with('error', "Error: La fecha de $label debe estar entre $nombrePeriodo.");
        }
    }

    // 3. Regla: Solo un periodo activo a la vez
    if ($request->status == 'activo') {
        $existeActivo = Semestre::where('status', 'activo')->exists();
        if ($existeActivo) {
            return back()->withInput()->with('error', 'Ya existe un periodo activo. Debes ponerlo como inactivo primero.');
        }
    }

    // 4. Evitar duplicados Año + Periodo
    $existe = Semestre::where('año', $request->año)
                      ->where('periodo', $request->periodo)
                      ->exists();
    if ($existe) {
        return back()->withInput()->with('error', 'Ya existe ese año y periodo registrado.');
    }

    // 5. Guardado
    Semestre::create($request->all());

    return redirect()->route('admin.semestres.index')
                     ->with('success', 'Semestre creado correctamente.');
}

    public function edit($id)
{
    $semestre = Semestre::findOrFail($id);

    // REGLA: Si no está activo, no se puede editar
    if ($semestre->status !== 'activo') {
        return redirect()->route('admin.semestres.index')
                         ->with('error', 'Los periodos inactivos no pueden ser editados.');
    }

    return view('admin.semestres.editar', compact('semestre'));
}

public function update(Request $request, $id)
{
    $semestre = Semestre::findOrFail($id);

    // REGLA: Bloqueo de seguridad
    if ($semestre->status !== 'activo') {
        return redirect()->route('admin.semestres.index')
                         ->with('error', 'Acción no permitida para periodos inactivos.');
    }

    // Validación similar al store...
    $request->validate([
        'año'    => 'required|integer',
        'status' => 'required|in:activo,inactivo', // Aquí el usuario puede cambiarlo a inactivo
        // ... resto de validaciones de fechas
    ]);

    $semestre->update($request->all());

    return redirect()->route('admin.semestres.index')
                     ->with('success', 'Periodo actualizado correctamente.');
}

public function destroy($id)
{
    // REGLA: No se puede eliminar nada que esté inactivo (historial) 
    // y tampoco activo (por seguridad de integridad). Básicamente, bloqueo total.
    return redirect()->route('admin.semestres.index')
                     ->with('error', 'Por seguridad, los periodos registrados no pueden ser eliminados.');
}
}