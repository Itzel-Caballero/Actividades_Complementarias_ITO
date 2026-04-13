<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Inscripcion;
use App\Models\Carrera;

class ReporteController extends Controller
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

    /**
     * Padrón de Alumnos (solo lectura).
     */
    public function alumnos(Request $request)
    {
        $buscar     = trim($request->get('buscar', ''));
        $id_carrera = $request->get('id_carrera', '');

        $alumnos = Alumno::with(['usuario', 'carrera'])
            ->when($buscar, function ($q) use ($buscar) {
                $q->whereHas('usuario', fn($u) =>
                    $u->where('nombre', 'LIKE', "%{$buscar}%")
                      ->orWhere('apellido_paterno', 'LIKE', "%{$buscar}%")
                      ->orWhere('num_control', 'LIKE', "%{$buscar}%")
                      ->orWhere('email', 'LIKE', "%{$buscar}%")
                );
            })
            ->when($id_carrera, fn($q) => $q->where('id_carrera', $id_carrera))
            ->orderBy('id_alumno')
            ->paginate(15)
            ->withQueryString();

        $carreras = Carrera::orderBy('nombre')->get();

        return view('admin.reportes.alumnos', compact('alumnos', 'carreras', 'buscar', 'id_carrera'));
    }

    /**
     * Monitor de Inscripciones (solo lectura).
     */
    public function inscripciones(Request $request)
    {
        $estatus    = $request->get('estatus', '');
        $id_carrera = $request->get('id_carrera', '');
        $buscar     = trim($request->get('buscar', ''));

        $inscripciones = Inscripcion::with([
                'alumno.usuario',
                'alumno.carrera',
                'grupo.actividad',
                'grupo.instructor.usuario',
            ])
            ->when($estatus, fn($q) => $q->where('estatus', $estatus))
            ->when($id_carrera, fn($q) =>
                $q->whereHas('alumno', fn($a) => $a->where('id_carrera', $id_carrera))
            )
            ->when($buscar, function ($q) use ($buscar) {
                $q->whereHas('alumno.usuario', fn($u) =>
                    $u->where('nombre', 'LIKE', "%{$buscar}%")
                      ->orWhere('apellido_paterno', 'LIKE', "%{$buscar}%")
                      ->orWhere('num_control', 'LIKE', "%{$buscar}%")
                );
            })
            ->orderByDesc('fecha_inscripcion')
            ->paginate(15)
            ->withQueryString();

        // Contadores por estatus para tarjetas resumen
        $totales = Inscripcion::selectRaw('estatus, count(*) as total')
                              ->groupBy('estatus')
                              ->pluck('total', 'estatus');

        $carreras = Carrera::orderBy('nombre')->get();

        return view('admin.reportes.inscripciones',
            compact('inscripciones', 'totales', 'carreras', 'estatus', 'id_carrera', 'buscar'));
    }

    /**
     * Log de Accesos (último acceso de cada usuario).
     */
    public function accesos(Request $request)
    {
        $buscar = trim($request->get('buscar', ''));
        $rol    = $request->get('rol', '');

        $usuarios = User::with('roles')
            ->when($buscar, fn($q) =>
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido_paterno', 'LIKE', "%{$buscar}%")
                  ->orWhere('email', 'LIKE', "%{$buscar}%")
            )
            ->when($rol, fn($q) =>
                $q->whereHas('roles', fn($r) => $r->where('name', $rol))
            )
            ->orderByDesc('ultimo_acceso')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reportes.accesos', compact('usuarios', 'buscar', 'rol'));
    }
}
