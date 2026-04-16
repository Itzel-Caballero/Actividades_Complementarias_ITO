<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Semestre;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Carbon\Carbon;

class SemestreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('admin')) abort(403, 'Acceso denegado.');
            return $next($request);
        });
    }

    // ─── Cierre automático de periodos vencidos ───────────────────────────
    private function cerrarVencidos(): void
    {
        Semestre::where('status', 'activo')
            ->where('fecha_fin', '<', Carbon::today()->toDateString())
            ->update(['status' => 'inactivo']);
    }

    // ─── INDEX ────────────────────────────────────────────────────────────
    public function index()
    {
        // Cerrar automáticamente los que ya vencieron
        $this->cerrarVencidos();

        $periodoActual = Semestre::withCount('grupos')->where('status', 'activo')->first();

        // Historial: todos los inactivos ordenados por recientes
        $historial = Semestre::withCount('grupos')
            ->where('status', 'inactivo')
            ->orderByDesc('año')
            ->orderByDesc('periodo')
            ->paginate(10);

        // El más reciente del historial (puede reactivarse si no hay activo)
        $ultimoInactivo = Semestre::where('status', 'inactivo')
            ->orderByDesc('año')
            ->orderByDesc('periodo')
            ->first();

        return view('admin.semestres.index', compact('periodoActual', 'historial', 'ultimoInactivo'));
    }

    // ─── CREATE ───────────────────────────────────────────────────────────
    public function create()
    {
        $this->cerrarVencidos();

        if (Semestre::where('status', 'activo')->exists()) {
            return redirect()->route('admin.semestres.index')
                ->with('error', 'No se puede crear un nuevo periodo mientras exista uno activo.');
        }

        return view('admin.semestres.crear');
    }

    // ─── STORE ────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->cerrarVencidos();

        $año     = (int) $request->año;
        $periodo = (int) $request->periodo;

        // ── Validación básica de formato ──────────────────────────────────
        $request->validate([
            'año'                        => 'required|integer|min:2000|max:2100',
            'periodo'                    => 'required|in:1,2',
            'fecha_inicio'               => 'required|date',
            'fecha_fin'                  => 'required|date|after:fecha_inicio',
            'fecha_inicio_inscripciones' => 'required|date',
            'hora_inicio_inscripciones'  => 'required|date_format:H:i',
            'fecha_fin_inscripciones'    => 'required|date|after:fecha_inicio_inscripciones',
            'hora_fin_inscripciones'     => 'required|date_format:H:i',
            'status'                     => 'required|in:activo,inactivo',
        ], [
            'hora_inicio_inscripciones.required' => 'La hora de inicio de inscripciones es obligatoria.',
            'hora_fin_inscripciones.required'    => 'La hora de fin de inscripciones es obligatoria.',
            'hora_inicio_inscripciones.date_format' => 'Formato de hora inválido (HH:MM).',
            'hora_fin_inscripciones.date_format'    => 'Formato de hora inválido (HH:MM).',
        ]);

        // ── Diferencia entre inicio y fin del semestre: 3 a 5 meses ──────
        $inicio = Carbon::parse($request->fecha_inicio);
        $fin    = Carbon::parse($request->fecha_fin);
        $meses  = $inicio->diffInMonths($fin);

        if ($meses < 3) {
            return back()->withInput()
                ->with('error', 'La duración del semestre debe ser de al menos 3 meses.');
        }
        if ($meses > 5) {
            return back()->withInput()
                ->with('error', 'La duración del semestre no puede exceder los 5 meses.');
        }

        // ── Diferencia entre inicio y fin de inscripciones: 5 a 10 días ──
        $iniInsc = Carbon::parse($request->fecha_inicio_inscripciones);
        $finInsc = Carbon::parse($request->fecha_fin_inscripciones);
        $dias    = $iniInsc->diffInDays($finInsc);

        if ($dias < 5) {
            return back()->withInput()
                ->with('error', 'El período de inscripciones debe durar al menos 5 días.');
        }
        if ($dias > 10) {
            return back()->withInput()
                ->with('error', 'El período de inscripciones no puede exceder los 10 días.');
        }

        // ── Las fechas de inscripción deben caer dentro del período ───────
        if ($iniInsc->lt($inicio) || $finInsc->gt($fin)) {
            return back()->withInput()
                ->with('error', 'Las fechas de inscripción deben estar dentro del rango del semestre.');
        }

        // ── Solo un periodo activo a la vez ───────────────────────────────
        if ($request->status === 'activo' && Semestre::where('status', 'activo')->exists()) {
            return back()->withInput()
                ->with('error', 'Ya existe un periodo activo. Ponlo como inactivo antes de crear uno nuevo.');
        }

        // ── Evitar duplicado Año+Periodo ──────────────────────────────────
        if (Semestre::where('año', $año)->where('periodo', $periodo)->exists()) {
            return back()->withInput()
                ->with('error', 'Ya existe un semestre registrado para ese año y periodo.');
        }

        Semestre::create([
            'año'                        => $año,
            'periodo'                    => $periodo,
            'fecha_inicio'               => $request->fecha_inicio,
            'fecha_fin'                  => $request->fecha_fin,
            'fecha_inicio_inscripciones' => $request->fecha_inicio_inscripciones,
            'hora_inicio_inscripciones'  => $request->hora_inicio_inscripciones,
            'fecha_fin_inscripciones'    => $request->fecha_fin_inscripciones,
            'hora_fin_inscripciones'     => $request->hora_fin_inscripciones,
            'status'                     => $request->status,
        ]);

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre creado correctamente.');
    }

    // ─── SHOW: ver grupos, alumnos y calificaciones de un periodo ─────────
    public function show($id)
    {
        $semestre = Semestre::with([
            'grupos.actividad',
            'grupos.instructor.usuario',
            'grupos.inscripciones.alumno.usuario',
            'grupos.inscripciones.calificaciones',
        ])->findOrFail($id);

        return view('admin.semestres.show', compact('semestre'));
    }

    // ─── EDIT ─────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $this->cerrarVencidos();

        $semestre = Semestre::findOrFail($id);

        // Solo se puede editar el activo O el más reciente del historial
        // cuando no hay ningún activo
        $hayActivo = Semestre::where('status', 'activo')->exists();

        if ($semestre->status === 'activo') {
            // OK, puede editar el activo
        } elseif (!$hayActivo) {
            // Verificar que sea el más reciente inactivo
            $ultimoInactivo = Semestre::where('status', 'inactivo')
                ->orderByDesc('año')->orderByDesc('periodo')->first();

            if (!$ultimoInactivo || $ultimoInactivo->id_semestre !== $semestre->id_semestre) {
                return redirect()->route('admin.semestres.index')
                    ->with('error', 'Solo el periodo más reciente puede reactivarse cuando no hay ningún activo.');
            }
        } else {
            return redirect()->route('admin.semestres.index')
                ->with('error', 'Los periodos inactivos no pueden editarse mientras haya uno activo.');
        }

        return view('admin.semestres.editar', compact('semestre'));
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $this->cerrarVencidos();
        $semestre = Semestre::findOrFail($id);

        // Misma verificación de permisos que en edit()
        $hayActivo      = Semestre::where('status', 'activo')->exists();
        $esteEsElActivo = $semestre->status === 'activo';

        if (!$esteEsElActivo && $hayActivo) {
            return redirect()->route('admin.semestres.index')
                ->with('error', 'Acción no permitida.');
        }

        if (!$esteEsElActivo && !$hayActivo) {
            $ultimoInactivo = Semestre::where('status', 'inactivo')
                ->orderByDesc('año')->orderByDesc('periodo')->first();
            if (!$ultimoInactivo || $ultimoInactivo->id_semestre !== $semestre->id_semestre) {
                return redirect()->route('admin.semestres.index')
                    ->with('error', 'Solo el periodo más reciente puede reactivarse.');
            }
        }

        $request->validate([
            'año'                        => 'required|integer|min:2000|max:2100',
            'periodo'                    => 'required|in:1,2',
            'fecha_inicio'               => 'required|date',
            'fecha_fin'                  => 'required|date|after:fecha_inicio',
            'fecha_inicio_inscripciones' => 'required|date',
            'hora_inicio_inscripciones'  => 'required|date_format:H:i',
            'fecha_fin_inscripciones'    => 'required|date|after:fecha_inicio_inscripciones',
            'hora_fin_inscripciones'     => 'required|date_format:H:i',
            'status'                     => 'required|in:activo,inactivo',
        ]);

        // ── Diferencia semestre: 3–5 meses ────────────────────────────────
        $inicio = Carbon::parse($request->fecha_inicio);
        $fin    = Carbon::parse($request->fecha_fin);
        $meses  = $inicio->diffInMonths($fin);

        if ($meses < 3) {
            return back()->withInput()->with('error', 'La duración del semestre debe ser de al menos 3 meses.');
        }
        if ($meses > 5) {
            return back()->withInput()->with('error', 'La duración del semestre no puede exceder los 5 meses.');
        }

        // ── Diferencia inscripciones: 5–10 días ───────────────────────────
        $iniInsc = Carbon::parse($request->fecha_inicio_inscripciones);
        $finInsc = Carbon::parse($request->fecha_fin_inscripciones);
        $dias    = $iniInsc->diffInDays($finInsc);

        if ($dias < 5) {
            return back()->withInput()->with('error', 'El período de inscripciones debe durar al menos 5 días.');
        }
        if ($dias > 10) {
            return back()->withInput()->with('error', 'El período de inscripciones no puede exceder los 10 días.');
        }

        if ($iniInsc->lt($inicio) || $finInsc->gt($fin)) {
            return back()->withInput()
                ->with('error', 'Las fechas de inscripción deben estar dentro del rango del semestre.');
        }

        // Si se activa, no puede haber otro activo (excepto este mismo)
        if ($request->status === 'activo') {
            $otroActivo = Semestre::where('status', 'activo')
                ->where('id_semestre', '!=', $id)
                ->exists();
            if ($otroActivo) {
                return back()->withInput()
                    ->with('error', 'Ya existe otro periodo activo.');
            }
        }

        $semestre->update([
            'año'                        => $request->año,
            'periodo'                    => $request->periodo,
            'fecha_inicio'               => $request->fecha_inicio,
            'fecha_fin'                  => $request->fecha_fin,
            'fecha_inicio_inscripciones' => $request->fecha_inicio_inscripciones,
            'hora_inicio_inscripciones'  => $request->hora_inicio_inscripciones,
            'fecha_fin_inscripciones'    => $request->fecha_fin_inscripciones,
            'hora_fin_inscripciones'     => $request->hora_fin_inscripciones,
            'status'                     => $request->status,
        ]);

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Periodo actualizado correctamente.');
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────
    public function destroy($id)
    {
        return redirect()->route('admin.semestres.index')
            ->with('error', 'Los periodos registrados no pueden eliminarse por integridad de datos.');
    }
}
