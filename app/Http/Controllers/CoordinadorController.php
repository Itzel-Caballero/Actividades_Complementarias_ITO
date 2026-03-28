<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActividadComplementaria;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Instructor;
use App\Models\Alumno;
use App\Models\Inscripcion;
use App\Models\Departamento;
use App\Models\Carrera;
use App\Models\Semestre;
use App\Models\Ubicacion;
use App\Models\DiaSemana;
use Carbon\Carbon;

class CoordinadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('coordinador')) {
                abort(403, 'Acceso denegado.');
            }
            return $next($request);
        });
    }

    /**
     * Determina el semestre activo según la fecha actual y devuelve
     * un array con id, etiqueta y clase CSS para el badge.
     */
    private function getSemestreActual(): array
    {
        $mes = Carbon::now()->month;
        $anio = Carbon::now()->year;

        // Enero–Junio  => periodo 1
        // Julio–Diciembre => periodo 2  (Agosto-Diciembre en la mayoría de TecNMs)
        $periodo = ($mes >= 1 && $mes <= 6) ? 1 : 2;

        $semestre = Semestre::where('año', $anio)
            ->where('periodo', $periodo)
            ->first();

        // Fallback: buscar el más reciente
        if (!$semestre) {
            $semestre = Semestre::orderByDesc('año')
                ->orderByDesc('periodo')
                ->first();
        }

        $etiqueta = $periodo === 1
            ? "Enero–Junio {$anio}"
            : "Agosto–Diciembre {$anio}";

        $clase = $periodo === 1 ? 'semestre-ene-jun' : 'semestre-ago-dic';

        return [
            'id'       => $semestre?->id_semestre,
            'etiqueta' => $etiqueta,
            'clase'    => $clase,
            'periodo'  => $periodo,
            'anio'     => $anio,
        ];
    }

    // ─── Dashboard ────────────────────────────────────────────────────────
    public function index()
    {
        $totalGrupos       = Grupo::count();
        $gruposSinDoc      = Grupo::whereNull('id_instructor')->count();
        $totalInstructores = Instructor::count();
        $totalAlumnos      = Alumno::count();
        $totalInscritos    = Inscripcion::whereIn('estatus', ['inscrito', 'cursando'])->count();

        $gruposRecientes = Grupo::with(['actividad', 'instructor.usuario', 'horarios.dia'])
            ->latest()->take(5)->get();

        return view('coordinador.index', compact(
            'totalGrupos', 'gruposSinDoc', 'totalInstructores',
            'totalAlumnos', 'totalInscritos', 'gruposRecientes'
        ));
    }

    // ─── GRUPOS ───────────────────────────────────────────────────────────
    public function grupos(Request $request)
    {
        $query = Grupo::with([
            'actividad.departamento',
            'instructor.usuario',
            'horarios.dia',
            'actividad.carreras'
        ]);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('actividad', fn($q) =>
                $q->where('nombre', 'like', "%{$buscar}%")
            );
        }
        if ($request->filled('id_departamento')) {
            $query->whereHas('actividad', fn($q) =>
                $q->where('id_departamento', $request->id_departamento)
            );
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        $grupos        = $query->paginate(12)->withQueryString();
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('coordinador.grupos', compact('grupos', 'departamentos'));
    }

    public function createGrupo()
    {
        $actividades      = ActividadComplementaria::with('departamento')->orderBy('nombre')->get();
        $instructores     = Instructor::with(['usuario', 'departamento'])->get();
        $semestres        = Semestre::orderByDesc('año')->orderByDesc('periodo')->get();
        $ubicaciones      = Ubicacion::orderBy('espacio')->get();
        $diasSemana       = DiaSemana::all();
        $carreras         = Carrera::orderBy('nombre')->get();
        $departamentos    = Departamento::orderBy('nombre')->get();
        $semestreActual   = $this->getSemestreActual();

        return view('coordinador.create_grupo', compact(
            'actividades', 'instructores', 'semestres',
            'ubicaciones', 'diasSemana', 'carreras',
            'departamentos', 'semestreActual'
        ));
    }

    public function storeGrupo(Request $request)
    {
        $request->validate([
            'id_actividad'  => 'required|exists:actividad_complementaria,id_actividad',
            'id_semestre'   => 'required|exists:semestre,id_semestre',
            'grupo'         => 'required|string|max:10',
            'cupo_maximo'   => 'required|integer|min:1',
            'modalidad'     => 'required|in:presencial,virtual,hibrida',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'required|date|after_or_equal:fecha_inicio',
            'id_instructor' => 'nullable|exists:instructor,id_instructor',
        ], [
            'id_actividad.required' => 'Selecciona una actividad.',
            'id_semestre.required'  => 'Selecciona un semestre.',
            'grupo.required'        => 'El identificador del grupo es obligatorio.',
            'cupo_maximo.required'  => 'El cupo máximo es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required'    => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior a la de inicio.',
        ]);

        $grupo = Grupo::create([
            'id_actividad'          => $request->id_actividad,
            'id_semestre'           => $request->id_semestre,
            'id_instructor'         => $request->id_instructor ?: null,
            'id_ubicacion'          => $request->id_ubicacion ?: null,
            'grupo'                 => strtoupper($request->grupo),
            'cupo_maximo'           => $request->cupo_maximo,
            'cupo_ocupado'          => 0,
            'modalidad'             => $request->modalidad,
            'materiales_requeridos' => $request->materiales_requeridos,
            'estatus'               => 'abierta',
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
        ]);

        if ($request->filled('horarios')) {
            foreach ($request->horarios as $h) {
                if (!empty($h['id_dia']) && !empty($h['hora_inicio']) && !empty($h['hora_fin'])) {
                    Horario::create([
                        'id_grupo'    => $grupo->id_grupo,
                        'id_dia'      => $h['id_dia'],
                        'hora_inicio' => $h['hora_inicio'],
                        'hora_fin'    => $h['hora_fin'],
                    ]);
                }
            }
        }

        return redirect()->route('coordinador.grupos')
            ->with('success', "Grupo {$grupo->grupo} creado correctamente.");
    }

    public function editGrupo($id)
    {
        $grupo            = Grupo::with(['actividad.carreras', 'instructor', 'horarios'])->findOrFail($id);
        $actividades      = ActividadComplementaria::with('departamento')->orderBy('nombre')->get();
        $instructores     = Instructor::with(['usuario', 'departamento'])->get();
        $semestres        = Semestre::orderByDesc('año')->orderByDesc('periodo')->get();
        $ubicaciones      = Ubicacion::orderBy('espacio')->get();
        $diasSemana       = DiaSemana::all();
        $carreras         = Carrera::orderBy('nombre')->get();
        $departamentos    = Departamento::orderBy('nombre')->get();
        $semestreActual   = $this->getSemestreActual();

        return view('coordinador.edit_grupo', compact(
            'grupo', 'actividades', 'instructores', 'semestres',
            'ubicaciones', 'diasSemana', 'carreras',
            'departamentos', 'semestreActual'
        ));
    }

    public function updateGrupo(Request $request, $id)
    {
        $request->validate([
            'id_actividad'  => 'required|exists:actividad_complementaria,id_actividad',
            'id_semestre'   => 'required|exists:semestre,id_semestre',
            'grupo'         => 'required|string|max:10',
            'cupo_maximo'   => 'required|integer|min:1',
            'modalidad'     => 'required|in:presencial,virtual,hibrida',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'required|date|after_or_equal:fecha_inicio',
            'id_instructor' => 'nullable|exists:instructor,id_instructor',
        ]);

        $grupo = Grupo::findOrFail($id);
        $grupo->update([
            'id_actividad'          => $request->id_actividad,
            'id_semestre'           => $request->id_semestre,
            'id_instructor'         => $request->id_instructor ?: null,
            'id_ubicacion'          => $request->id_ubicacion ?: null,
            'grupo'                 => strtoupper($request->grupo),
            'cupo_maximo'           => $request->cupo_maximo,
            'modalidad'             => $request->modalidad,
            'materiales_requeridos' => $request->materiales_requeridos,
            'estatus'               => $request->estatus ?? $grupo->estatus,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
        ]);

        $grupo->horarios()->delete();
        if ($request->filled('horarios')) {
            foreach ($request->horarios as $h) {
                if (!empty($h['id_dia']) && !empty($h['hora_inicio']) && !empty($h['hora_fin'])) {
                    Horario::create([
                        'id_grupo'    => $grupo->id_grupo,
                        'id_dia'      => $h['id_dia'],
                        'hora_inicio' => $h['hora_inicio'],
                        'hora_fin'    => $h['hora_fin'],
                    ]);
                }
            }
        }

        return redirect()->route('coordinador.grupos')
            ->with('success', "Grupo {$grupo->grupo} actualizado correctamente.");
    }

    public function destroyGrupo($id)
    {
        $grupo = Grupo::findOrFail($id);
        $grupo->horarios()->delete();
        foreach ($grupo->inscripciones as $insc) {
            $insc->calificaciones()->delete();
            $insc->delete();
        }
        $nombre = $grupo->grupo;
        $grupo->delete();

        return redirect()->route('coordinador.grupos')
            ->with('success', "Grupo {$nombre} eliminado.");
    }

    public function asignarInstructor(Request $request, $id)
    {
        $request->validate(['id_instructor' => 'nullable|exists:instructor,id_instructor']);
        Grupo::findOrFail($id)->update(['id_instructor' => $request->id_instructor ?: null]);
        return redirect()->back()->with('success', 'Instructor actualizado.');
    }

    // ─── ACTIVIDADES ──────────────────────────────────────────────────────
    public function actividades(Request $request)
    {
        $query = ActividadComplementaria::with(['departamento', 'grupos', 'carreras']);

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%'.$request->buscar.'%');
        }
        if ($request->filled('id_departamento')) {
            $query->where('id_departamento', $request->id_departamento);
        }
        if ($request->filled('disponible') && $request->disponible !== '') {
            $query->where('disponible', $request->disponible);
        }

        $actividades   = $query->orderBy('nombre')->paginate(12)->withQueryString();
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('coordinador.actividades', compact('actividades', 'departamentos'));
    }

    // ─── DOCENTES ─────────────────────────────────────────────────────────
    public function docentes(Request $request)
    {
        $query = Instructor::with(['usuario', 'departamento', 'grupos.actividad']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('usuario', fn($q) =>
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                  ->orWhere('apellido_materno', 'like', "%{$buscar}%")
            );
        }
        if ($request->filled('id_departamento')) {
            $query->where('id_departamento', $request->id_departamento);
        }
        if ($request->filled('especialidad')) {
            $query->where('especialidad', 'like', '%'.$request->especialidad.'%');
        }
        if ($request->filled('id_actividad')) {
            $idActividad = $request->id_actividad;
            $query->whereHas('grupos', fn($q) =>
                $q->where('id_actividad', $idActividad)
            );
        }

        $instructores  = $query->paginate(15)->withQueryString();
        $departamentos = Departamento::orderBy('nombre')->get();
        $actividades   = ActividadComplementaria::orderBy('nombre')->get();

        return view('coordinador.docentes', compact('instructores', 'departamentos', 'actividades'));
    }

    // ─── ALUMNOS ──────────────────────────────────────────────────────────
    public function alumnos(Request $request)
    {
        $query = Alumno::with(['usuario', 'carrera', 'inscripciones.grupo.actividad']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('usuario', fn($q) =>
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                  ->orWhere('num_control', 'like', "%{$buscar}%")
            );
        }
        if ($request->filled('id_carrera')) {
            $query->where('id_carrera', $request->id_carrera);
        }
        if ($request->filled('semestre')) {
            $query->where('semestre_cursando', $request->semestre);
        }
        if ($request->filled('inscripcion_activa')) {
            if ($request->inscripcion_activa === '1') {
                $query->whereHas('inscripciones', fn($q) =>
                    $q->whereIn('estatus', ['inscrito', 'cursando'])
                );
            } elseif ($request->inscripcion_activa === '0') {
                $query->whereDoesntHave('inscripciones', fn($q) =>
                    $q->whereIn('estatus', ['inscrito', 'cursando'])
                );
            }
        }

        $alumnos  = $query->paginate(15)->withQueryString();
        $carreras = Carrera::orderBy('nombre')->get();

        return view('coordinador.alumnos', compact('alumnos', 'carreras'));
    }

    // ─── AJAX ─────────────────────────────────────────────────────────────
    public function buscarInstructores(Request $request)
    {
        $q = $request->get('q', '');
        return response()->json(
            Instructor::with(['usuario', 'departamento'])
                ->whereHas('usuario', fn($query) =>
                    $query->where('nombre', 'like', "%{$q}%")
                          ->orWhere('apellido_paterno', 'like', "%{$q}%")
                )
                ->get()
                ->map(fn($i) => [
                    'id'     => $i->id_instructor,
                    'nombre' => $i->usuario->nombre_completo ?? 'Sin nombre',
                    'depto'  => $i->departamento->nombre ?? 'N/A',
                ])
        );
    }
}
