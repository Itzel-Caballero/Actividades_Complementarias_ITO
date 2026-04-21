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
use App\Models\CoordinadorDepartamento;
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
            if (!auth()->user()->hasRole('coordinador')) abort(403, 'Acceso denegado.');
            return $next($request);
        });
    }

    /**
     * Obtiene el id_departamento asignado al coordinador autenticado.
     * Aborta con 403 si no tiene departamento asignado.
     */
    private function getDepartamentoCoordinador(): int
    {
        $asignacion = CoordinadorDepartamento::where('id_usuario', auth()->id())->first();
        if (!$asignacion) abort(403, 'No tienes un departamento asignado.');
        return $asignacion->id_departamento;
    }

    private function getSemestreActual(): array
    {
        $mes    = Carbon::now()->month;
        $anio   = Carbon::now()->year;
        $periodo = ($mes >= 1 && $mes <= 6) ? 1 : 2;

        $semestre = Semestre::where('año', $anio)->where('periodo', $periodo)->first()
            ?? Semestre::orderByDesc('año')->orderByDesc('periodo')->first();

        $etiqueta = $periodo === 1 ? "Enero–Junio {$anio}" : "Agosto–Diciembre {$anio}";
        $clase    = $periodo === 1 ? 'semestre-ene-jun' : 'semestre-ago-dic';



        return ['id' => $semestre?->id_semestre, 'etiqueta' => $etiqueta, 'clase' => $clase, 'periodo' => $periodo, 'anio' => $anio];
    }

    // ─── Dashboard ────────────────────────────────────────────────────────
    public function index()
    {
        $idDepto = $this->getDepartamentoCoordinador();

        $totalGrupos       = Grupo::whereHas('actividad', fn($q) => $q->where('id_departamento', $idDepto))->count();
        $gruposSinDoc      = Grupo::whereNull('id_instructor')
                                  ->whereHas('actividad', fn($q) => $q->where('id_departamento', $idDepto))->count();
        $totalInstructores = Instructor::where('id_departamento', $idDepto)->count();
        $totalAlumnos      = Alumno::whereHas('inscripciones', fn($q) =>
                                $q->whereIn('estatus', ['inscrito', 'cursando'])
                                  ->whereHas('grupo.actividad', fn($q2) => $q2->where('id_departamento', $idDepto))
                             )->count();
        $totalInscritos    = Inscripcion::whereIn('estatus', ['inscrito', 'cursando'])
                                        ->whereHas('grupo.actividad', fn($q) => $q->where('id_departamento', $idDepto))
                                        ->count();
        $gruposRecientes   = Grupo::with(['actividad', 'instructor.usuario', 'horarios.dia'])
                                  ->whereHas('actividad', fn($q) => $q->where('id_departamento', $idDepto))
                                  ->latest()->take(5)->get();

        return view('coordinador.index', compact('totalGrupos', 'gruposSinDoc', 'totalInstructores', 'totalAlumnos', 'totalInscritos', 'gruposRecientes'));
    }

    // ─── GRUPOS ───────────────────────────────────────────────────────────
    public function grupos(Request $request)
    {
        $idDepto = $this->getDepartamentoCoordinador();

        // Solo grupos cuya actividad pertenece al departamento del coordinador
        $query = Grupo::with(['actividad.departamento', 'instructor.usuario', 'horarios.dia', 'actividad.carreras', 'inscripciones'])
                      ->whereHas('actividad', fn($q) => $q->where('id_departamento', $idDepto));

        if ($request->filled('buscar'))
            $query->whereHas('actividad', fn($q) => $q->where('nombre', 'like', "%{$request->buscar}%"));
        if ($request->filled('estatus'))
            $query->where('estatus', $request->estatus);

        $grupos            = $query->paginate(12)->withQueryString();
        $departamentos     = Departamento::orderBy('nombre')->get();
        $semestreActivo    = Semestre::where('status', 'activo')->first();
        $hasActiveSemestre = (bool) $semestreActivo;

        return view('coordinador.grupos', compact('grupos', 'departamentos', 'hasActiveSemestre'));
    }

    public function createGrupo()
    {
        $idDepto = $this->getDepartamentoCoordinador();

        // Solo se puede crear grupo si hay un semestre activo
        $semestreActivo = Semestre::where('status', 'activo')->first();

        if (!$semestreActivo) {
            return redirect()->route('coordinador.grupos')
                ->with('error', 'No hay un periodo escolar activo. No es posible crear grupos en este momento.');
        }

        // Solo actividades del departamento del coordinador
        $actividades   = ActividadComplementaria::with(['departamento', 'carreras'])
                            ->where('disponible', true)
                            ->where('id_departamento', $idDepto)
                            ->orderBy('nombre')->get();
        // Solo instructores del departamento del coordinador
        $instructores  = Instructor::with(['usuario', 'departamento'])
                            ->where('id_departamento', $idDepto)->get();
        $ubicaciones   = Ubicacion::orderBy('espacio')->get();
        $diasSemana    = DiaSemana::all();
        $departamentos = Departamento::where('id_departamento', $idDepto)->get();

        // Badge del semestre activo
        $semestreActual = [
            'id'       => $semestreActivo->id_semestre,
            'etiqueta' => $semestreActivo->periodo == 1
                ? "Enero–Junio {$semestreActivo->año}"
                : "Agosto–Diciembre {$semestreActivo->año}",
            'clase'    => $semestreActivo->periodo == 1 ? 'semestre-ene-jun' : 'semestre-ago-dic',
        ];

        // JSON de instructores del departamento para el filtro dinámico
        $instructoresPorDepto = $instructores->map(fn($i) => [
            'id'       => $i->id_instructor,
            'nombre'   => $i->usuario->nombre_completo ?? 'Sin nombre',
            'id_depto' => $i->id_departamento,
            'depto'    => $i->departamento->nombre ?? 'N/A',
        ]);

        // JSON de carreras por actividad
        $carrerasPorActividad = new \stdClass();
        foreach ($actividades as $act) {
            $key = (string) $act->id_actividad;
            $carrerasPorActividad->$key = $act->carreras->map(fn($c) => [
                'id'     => $c->id_carrera,
                'nombre' => $c->nombre,
            ])->values()->toArray();
        }

        return view('coordinador.create_grupo', compact(
            'actividades', 'instructores', 'ubicaciones',
            'diasSemana', 'departamentos', 'semestreActual',
            'semestreActivo', 'instructoresPorDepto', 'carrerasPorActividad'
        ));
    }

    public function storeGrupo(Request $request)
    {
        $idDepto = $this->getDepartamentoCoordinador();

        // Verificar que sigue habiendo semestre activo al momento de guardar
        $semestreActivo = Semestre::where('status', 'activo')->first();
        if (!$semestreActivo) {
            return redirect()->route('coordinador.grupos')
                ->with('error', 'No hay un periodo escolar activo. No se puede crear el grupo.');
        }

        // Verificar que la actividad seleccionada pertenece al departamento del coordinador
        $actividad = ActividadComplementaria::where('id_actividad', $request->id_actividad)
                        ->where('id_departamento', $idDepto)->first();
        if (!$actividad) abort(403, 'No puedes crear grupos para actividades de otro departamento.');

        // Verificar que el instructor (si se asignó) pertenece al departamento
        if ($request->filled('id_instructor')) {
            $instructor = Instructor::where('id_instructor', $request->id_instructor)
                            ->where('id_departamento', $idDepto)->first();
            if (!$instructor) abort(403, 'No puedes asignar instructores de otro departamento.');
        }

        $request->validate([
            'id_actividad'  => 'required|exists:actividad_complementaria,id_actividad',
            'grupo'         => 'required|string|max:10',
            'cupo_minimo'   => 'required|integer|min:1',
            'cupo_maximo'   => 'required|integer|min:1|gte:cupo_minimo',
            'modalidad'     => 'required|in:presencial,virtual,hibrida',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'required|date|after_or_equal:fecha_inicio',
            'id_instructor' => 'nullable|exists:instructor,id_instructor',
        ]);

        $grupo = Grupo::create([
            'id_actividad'          => $request->id_actividad,
            'id_semestre'           => $semestreActivo->id_semestre,
            'id_instructor'         => $request->id_instructor ?: null,
            'id_ubicacion'          => $request->id_ubicacion ?: null,
            'grupo'                 => strtoupper($request->grupo),
            'cupo_minimo'           => $request->cupo_minimo,
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
                    Horario::create(['id_grupo' => $grupo->id_grupo, 'id_dia' => $h['id_dia'], 'hora_inicio' => $h['hora_inicio'], 'hora_fin' => $h['hora_fin']]);
                }
            }
        }

        return redirect()->route('coordinador.grupos')->with('success', "Grupo {$grupo->grupo} creado correctamente.");
    }

    public function editGrupo($id)
    {
        $idDepto = $this->getDepartamentoCoordinador();

        $grupo = Grupo::with(['actividad.carreras', 'instructor', 'horarios'])->findOrFail($id);

        // Verificar que el grupo pertenece al departamento del coordinador
        if ($grupo->actividad->id_departamento !== $idDepto) abort(403, 'No puedes editar grupos de otro departamento.');

        // Solo actividades y docentes del propio departamento
        $actividades    = ActividadComplementaria::with('departamento')
                            ->where('id_departamento', $idDepto)->orderBy('nombre')->get();
        $instructores   = Instructor::with(['usuario', 'departamento'])
                            ->where('id_departamento', $idDepto)->get();
        $semestres      = Semestre::orderByDesc('año')->orderByDesc('periodo')->get();
        $ubicaciones    = Ubicacion::orderBy('espacio')->get();
        $diasSemana     = DiaSemana::all();
        $carreras       = Carrera::orderBy('nombre')->get();
        $departamentos  = Departamento::where('id_departamento', $idDepto)->get();
        
        // Usar el semestre activo, igual que en createGrupo
        $semestreActivo = Semestre::where('status', 'activo')->first();
        $semestreActual = $semestreActivo ? [
            'id'       => $semestreActivo->id_semestre,
            'etiqueta' => $semestreActivo->periodo == 1
                ? "Enero–Junio {$semestreActivo->año}"
                : "Agosto–Diciembre {$semestreActivo->año}",
            'clase'    => $semestreActivo->periodo == 1 ? 'semestre-ene-jun' : 'semestre-ago-dic',
        ] : $this->getSemestreActual(); // fallback por si no hay activo

        $instructoresPorDepto = $instructores->map(fn($i) => [
            'id'       => $i->id_instructor,
            'nombre'   => $i->usuario->nombre_completo ?? 'Sin nombre',
            'id_depto' => $i->id_departamento,
            'depto'    => $i->departamento->nombre ?? 'N/A',
        ]);

        $horExistentes = $grupo->horarios->map(function($h) {
            return [
                'id_dia'      => $h->id_dia,
                'hora_inicio' => substr($h->hora_inicio, 0, 5),
            ];
        })->values();

        return view('coordinador.edit_grupo', compact(
            'grupo', 'actividades', 'instructores', 'semestres', 'ubicaciones',
            'diasSemana', 'carreras', 'departamentos', 'semestreActual', 'instructoresPorDepto', 'horExistentes'
        ));
    }

    public function updateGrupo(Request $request, $id)
    {
        $idDepto = $this->getDepartamentoCoordinador();

        $grupo = Grupo::findOrFail($id);
        if ($grupo->actividad->id_departamento !== $idDepto) abort(403, 'No puedes modificar grupos de otro departamento.');

        // Verificar que la nueva actividad también pertenece al departamento
        $actividad = ActividadComplementaria::where('id_actividad', $request->id_actividad)
                        ->where('id_departamento', $idDepto)->first();
        if (!$actividad) abort(403, 'No puedes asignar actividades de otro departamento.');

        // Verificar que el instructor (si se asignó) pertenece al departamento
        if ($request->filled('id_instructor')) {
            $instructor = Instructor::where('id_instructor', $request->id_instructor)
                            ->where('id_departamento', $idDepto)->first();
            if (!$instructor) abort(403, 'No puedes asignar instructores de otro departamento.');
        }

        $request->validate([
            'id_actividad'  => 'required|exists:actividad_complementaria,id_actividad',
            'id_semestre'   => 'required|exists:semestre,id_semestre',
            'grupo'         => 'required|string|max:10',
            'cupo_minimo'   => 'required|integer|min:1',
            'cupo_maximo'   => 'required|integer|min:1|gte:cupo_minimo',
            'modalidad'     => 'required|in:presencial,virtual,hibrida',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'required|date|after_or_equal:fecha_inicio',
            'id_instructor' => 'nullable|exists:instructor,id_instructor',
        ]);

        $grupoToUpdate = Grupo::findOrFail($id);
        $grupoToUpdate->update([
            'id_actividad'          => $request->id_actividad,
            'id_semestre'           => $request->id_semestre,
            'id_instructor'         => $request->id_instructor ?: null,
            'id_ubicacion'          => $request->id_ubicacion ?: null,
            'grupo'                 => strtoupper($request->grupo),
            'cupo_minimo'           => $request->cupo_minimo,
            'cupo_maximo'           => $request->cupo_maximo,
            'modalidad'             => $request->modalidad,
            'materiales_requeridos' => $request->materiales_requeridos,
            'estatus'               => $request->estatus ?? $grupo->estatus,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
        ]);

        $grupoToUpdate->horarios()->delete();
        if ($request->filled('horarios')) {
            foreach ($request->horarios as $h) {
                if (!empty($h['id_dia']) && !empty($h['hora_inicio']) && !empty($h['hora_fin'])) {
                    Horario::create(['id_grupo' => $grupoToUpdate->id_grupo, 'id_dia' => $h['id_dia'], 'hora_inicio' => $h['hora_inicio'], 'hora_fin' => $h['hora_fin']]);
                }
            }
        }

        return redirect()->route('coordinador.grupos')->with('success', "Grupo {$grupoToUpdate->grupo} actualizado correctamente.");
    }

    public function destroyGrupo($id)
    {
        $idDepto = $this->getDepartamentoCoordinador();
        $grupo   = Grupo::findOrFail($id);
        if ($grupo->actividad->id_departamento !== $idDepto) abort(403, 'No puedes eliminar grupos de otro departamento.');
        $grupo->horarios()->delete();
        foreach ($grupo->inscripciones as $insc) {
            $insc->calificaciones()->delete();
            $insc->delete();
        }
        $nombre = $grupo->grupo;
        $grupo->delete();
        return redirect()->route('coordinador.grupos')->with('success', "Grupo {$nombre} eliminado.");
    }

    public function asignarInstructor(Request $request, $id)
    {
        $idDepto = $this->getDepartamentoCoordinador();
        $request->validate(['id_instructor' => 'nullable|exists:instructor,id_instructor']);

        $grupo = Grupo::findOrFail($id);
        if ($grupo->actividad->id_departamento !== $idDepto) abort(403, 'No puedes asignar instructores a grupos de otro departamento.');

        // Verificar que el instructor pertenece al departamento
        if ($request->filled('id_instructor')) {
            $instructor = Instructor::where('id_instructor', $request->id_instructor)
                            ->where('id_departamento', $idDepto)->first();
            if (!$instructor) abort(403, 'No puedes asignar instructores de otro departamento.');
        }

        $grupo->update(['id_instructor' => $request->id_instructor ?: null]);
        return redirect()->back()->with('success', 'Instructor actualizado.');
    }

    // ─── ACTIVIDADES ──────────────────────────────────────────────────────
    public function actividades(Request $request)
    {
        $idDepto = $this->getDepartamentoCoordinador();

        // Solo actividades del propio departamento
        $query = ActividadComplementaria::with(['departamento', 'grupos', 'carreras'])
                    ->where('id_departamento', $idDepto);

        if ($request->filled('buscar'))
            $query->where('nombre', 'like', '%'.$request->buscar.'%');
        if ($request->filled('disponible') && $request->disponible !== '')
            $query->where('disponible', $request->disponible);

        $actividades   = $query->orderBy('nombre')->paginate(12)->withQueryString();
        $departamentos = Departamento::where('id_departamento', $idDepto)->get();

        return view('coordinador.actividades', compact('actividades', 'departamentos'));
    }

    // ─── DOCENTES ─────────────────────────────────────────────────────────
    public function docentes(Request $request)
    {
        $idDepto = $this->getDepartamentoCoordinador();

        // Solo docentes del propio departamento
        $query = Instructor::with(['usuario', 'departamento', 'grupos.actividad'])
                    ->where('id_departamento', $idDepto);

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->whereHas('usuario', fn($q) =>
                $q->where('nombre', 'like', "%{$b}%")
                  ->orWhere('apellido_paterno', 'like', "%{$b}%")
                  ->orWhere('apellido_materno', 'like', "%{$b}%")
            );
        }
        if ($request->filled('especialidad'))
            $query->where('especialidad', 'like', '%'.$request->especialidad.'%');
        if ($request->filled('id_actividad'))
            $query->whereHas('grupos', fn($q) => $q->where('id_actividad', $request->id_actividad));

        $instructores  = $query->paginate(15)->withQueryString();
        $departamentos = Departamento::where('id_departamento', $idDepto)->get();
        // Solo actividades del propio departamento para el filtro
        $actividades   = ActividadComplementaria::where('id_departamento', $idDepto)->orderBy('nombre')->get();

        return view('coordinador.docentes', compact('instructores', 'departamentos', 'actividades'));
    }

    // ─── ALUMNOS ──────────────────────────────────────────────────────────
    public function alumnos(Request $request)
    {
        $idDepto = $this->getDepartamentoCoordinador();

        // Alumnos de cualquier carrera, pero SOLO si están inscritos en
        // una actividad que pertenece al departamento del coordinador
        $query = Alumno::with(['usuario', 'carrera', 'inscripciones.grupo.actividad.departamento'])
            ->whereHas('inscripciones', fn($q) =>
                $q->whereIn('estatus', ['inscrito', 'cursando'])
                  ->whereHas('grupo.actividad', fn($q2) => $q2->where('id_departamento', $idDepto))
            );

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->whereHas('usuario', fn($q) =>
                $q->where('nombre', 'like', "%{$b}%")
                  ->orWhere('apellido_paterno', 'like', "%{$b}%")
                  ->orWhere('num_control', 'like', "%{$b}%")
            );
        }
        if ($request->filled('id_carrera'))
            $query->where('id_carrera', $request->id_carrera);
        if ($request->filled('id_actividad'))
            $query->whereHas('inscripciones.grupo', fn($q) =>
                $q->where('id_actividad', $request->id_actividad)
                  ->whereHas('actividad', fn($q2) => $q2->where('id_departamento', $idDepto))
            );

        $alumnos       = $query->paginate(15)->withQueryString();
        $carreras      = Carrera::orderBy('nombre')->get();
        // Solo actividades del departamento para el filtro
        $actividades   = ActividadComplementaria::where('id_departamento', $idDepto)->orderBy('nombre')->get();
        $departamentos = Departamento::where('id_departamento', $idDepto)->get();

        return view('coordinador.alumnos', compact('alumnos', 'carreras', 'actividades', 'departamentos'));
    }

    // ─── DAR DE BAJA a alumno ─────────────────────────────────────────────
    public function darBajaAlumno(Request $request, $id_inscripcion)
    {
        $idDepto     = $this->getDepartamentoCoordinador();
        $inscripcion = Inscripcion::with(['grupo.actividad', 'alumno'])->findOrFail($id_inscripcion);

        // Verificar que la inscripción corresponde a una actividad del departamento
        if ($inscripcion->grupo->actividad->id_departamento !== $idDepto)
            abort(403, 'No puedes dar de baja alumnos de otros departamentos.');

        // Actualizar cupo del grupo
        $grupo = $inscripcion->grupo;
        if ($grupo && $grupo->cupo_ocupado > 0) {
            $grupo->decrement('cupo_ocupado');
        }

        // Dar de baja (no eliminamos, cambiamos estatus para preservar historial)
        $inscripcion->update(['estatus' => 'baja']);

        return redirect()->route('coordinador.alumnos', $request->query())
            ->with('success', 'Alumno dado de baja correctamente.');
    }

    // ─── AJAX: instructores por departamento de actividad ─────────────────
    public function instructoresPorActividad(Request $request)
    {
        $idDepto     = $this->getDepartamentoCoordinador();
        $idActividad = $request->get('id_actividad');

        // Verificar que la actividad pertenece al departamento del coordinador
        $actividad = ActividadComplementaria::where('id_actividad', $idActividad)
                        ->where('id_departamento', $idDepto)->first();

        if (!$actividad) return response()->json([]);

        return response()->json(
            Instructor::with(['usuario', 'departamento'])
                ->where('id_departamento', $idDepto)
                ->get()
                ->map(fn($i) => [
                    'id'     => $i->id_instructor,
                    'nombre' => $i->usuario->nombre_completo ?? 'Sin nombre',
                    'depto'  => $i->departamento->nombre ?? 'N/A',
                ])
        );
    }

    // ─── AJAX: buscar instructores (búsqueda libre) ───────────────────────
    public function buscarInstructores(Request $request)
    {
        $idDepto = $this->getDepartamentoCoordinador();
        $q       = $request->get('q', '');

        return response()->json(
            Instructor::with(['usuario', 'departamento'])
                ->where('id_departamento', $idDepto)  // solo del propio departamento
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
