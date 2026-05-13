<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\Inscripcion;
use App\Models\Calificacion;
use App\Models\Semestre;
use App\Models\Alumno;
use Barryvdh\DomPDF\Facade\Pdf;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    private function getInstructor()
    {
        $instructor = Instructor::where('id_instructor', auth()->id())
            ->with('grupos')
            ->first();

        abort_if(!$instructor, 403, 'No tienes un perfil de instructor.');
        return $instructor;
    }

    /**
     * Retorna el semestre activo o null si no existe.
     */
    private function getSemestreActivo(): ?Semestre
    {
        return Semestre::where('status', 'activo')->first();
    }

    // ── Mis Grupos ────────────────────────────────────────────────────────────

    public function misGrupos()
    {
        $instructor      = $this->getInstructor();
        $semestreActivo  = $this->getSemestreActivo();

        $grupos = $instructor->grupos()
            ->with([
                'actividad',
                'semestre',
                'ubicacion',
                'horarios.dia',
                'inscripciones.alumno.usuario',
                'inscripciones.alumno.carrera',
                'inscripciones.calificaciones',
            ])
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('instructor.mis-grupos', compact('grupos', 'instructor', 'semestreActivo'));
    }

    // ── Detalle de un grupo (vista dedicada) ──────────────────────────────────

    public function verGrupo($id_grupo)
    {
        $instructor     = $this->getInstructor();
        $semestreActivo = $this->getSemestreActivo();

        $grupo = $instructor->grupos()
            ->with([
                'actividad',
                'semestre',
                'ubicacion',
                'horarios.dia',
                'inscripciones.alumno.usuario',
                'inscripciones.alumno.carrera',
                'inscripciones.calificaciones',
            ])
            ->where('id_grupo', $id_grupo)
            ->firstOrFail();

        abort_unless(
            $instructor->grupos->contains('id_grupo', $grupo->id_grupo),
            403,
            'No tienes permiso para ver este grupo.'
        );

        return view('instructor.grupo-detalle', compact('grupo', 'instructor', 'semestreActivo'));
    }

    // ── Calificar ─────────────────────────────────────────────────────────────

    public function calificar($id_inscripcion)
    {
        // Verificar periodo activo
        $semestreActivo = $this->getSemestreActivo();
        abort_if(!$semestreActivo, 403, 'No hay un periodo activo. No puedes calificar en este momento.');

        $instructor = $this->getInstructor();

        $inscripcion = Inscripcion::with([
            'alumno.usuario',
            'alumno.carrera',
            'grupo.actividad',
            'calificaciones',
        ])->findOrFail($id_inscripcion);

        abort_unless(
            $instructor->grupos->contains('id_grupo', $inscripcion->id_grupo),
            403,
            'No tienes permiso para calificar este alumno.'
        );

        $calificacion = $inscripcion->calificaciones->first();

        return view('instructor.calificar', compact('inscripcion', 'calificacion', 'semestreActivo'));
    }

    public function guardarCalificacion(Request $request, $id_inscripcion)
    {
        // Verificar periodo activo
        abort_if(!$this->getSemestreActivo(), 403, 'No hay un periodo activo.');

        $instructor = $this->getInstructor();

        $request->validate([
            'desempenio'    => 'required|in:malo,bueno,excelente',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $inscripcion = Inscripcion::with(['calificaciones', 'grupo.actividad'])->findOrFail($id_inscripcion);

        abort_unless(
            $instructor->grupos->contains('id_grupo', $inscripcion->id_grupo),
            403
        );

        $estatusAnterior = $inscripcion->estatus;

        Calificacion::updateOrCreate(
            ['id_inscripcion' => $id_inscripcion],
            [
                'desempenio'    => $request->desempenio,
                'observaciones' => $request->observaciones,
            ]
        );

        // Aprobado si bueno o excelente; reprobado si malo
        $nuevoEstatus = in_array($request->desempenio, ['bueno', 'excelente'])
            ? 'aprobado'
            : 'reprobado';

        $inscripcion->estatus = $nuevoEstatus;
        $inscripcion->save();

        // Sumar créditos al alumno solo cuando pasa a aprobado por primera vez
        if ($nuevoEstatus === 'aprobado' && $estatusAnterior !== 'aprobado') {
            $creditos = optional($inscripcion->grupo->actividad)->creditos ?? 0;
            if ($creditos > 0) {
                Alumno::where('id_alumno', $inscripcion->id_alumno)
                    ->increment('creditos_acumulados', $creditos);
            }
        }

        // Si se revierte de aprobado a reprobado, descontar créditos
        if ($nuevoEstatus === 'reprobado' && $estatusAnterior === 'aprobado') {
            $creditos = optional($inscripcion->grupo->actividad)->creditos ?? 0;
            if ($creditos > 0) {
                Alumno::where('id_alumno', $inscripcion->id_alumno)
                    ->decrement('creditos_acumulados', $creditos);
            }
        }

        return redirect()
            ->route('instructor.mis-grupos')
            ->with('success', 'Calificación guardada correctamente.');
    }

    // ── Descargar lista de alumnos de un grupo ──────────────────────────────

    public function descargarLista($id_grupo)
    {
        $instructor = $this->getInstructor();

        // Verificar que el grupo pertenece al instructor
        $grupo = $instructor->grupos()
            ->with([
                'actividad',
                'semestre',
                'inscripciones.alumno.usuario',
                'inscripciones.alumno.carrera',
                'inscripciones.calificaciones',
            ])
            ->where('id_grupo', $id_grupo)
            ->firstOrFail();

        abort_unless(
            $instructor->grupos->contains('id_grupo', $grupo->id_grupo),
            403,
            'No tienes permiso para descargar esta lista.'
        );

        $actividad  = $grupo->actividad->nombre  ?? 'Grupo';
        $grupoNum   = $grupo->grupo;
        $semestre   = ($grupo->semestre->año ?? '') . '-' . ($grupo->semestre->periodo ?? '');
        $nombreArchivo = 'Lista_' . str_replace(' ', '_', $actividad) . '_Grupo' . $grupoNum . '_' . $semestre . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ];

        $callback = function () use ($grupo, $actividad, $grupoNum, $semestre) {
            $handle = fopen('php://output', 'w');

            // BOM para que Excel abra bien UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Encabezado informativo
            fputcsv($handle, ['Actividad Complementaria:', $actividad]);
            fputcsv($handle, ['Grupo:', $grupoNum]);
            fputcsv($handle, ['Semestre:', $semestre]);
            fputcsv($handle, ['Instructor:', auth()->user()->nombre . ' ' . auth()->user()->apellido_paterno]);
            fputcsv($handle, ['Fecha de descarga:', now()->format('d/m/Y H:i')]);
            fputcsv($handle, []);

            // Cabecera de columnas
            fputcsv($handle, [
                '#',
                'Nombre',
                'Apellido Paterno',
                'Apellido Materno',
                'No. Control',
                'Carrera',
                'Semestre que cursa',
                'Fecha Inscripción',
                'Estatus Inscripción',
                'Calificación',
                'Observaciones',
            ]);

            $idx = 1;
            foreach ($grupo->inscripciones as $inscripcion) {
                $alumno      = $inscripcion->alumno;
                $usuario     = $alumno->usuario ?? null;
                $calificacion = $inscripcion->calificaciones->first();

                fputcsv($handle, [
                    $idx++,
                    $usuario->nombre           ?? '',
                    $usuario->apellido_paterno  ?? '',
                    $usuario->apellido_materno  ?? '',
                    $usuario->num_control       ?? '',
                    $alumno->carrera->nombre    ?? '',
                    $alumno->semestre_cursando  ?? '',
                    \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->format('d/m/Y'),
                    ucfirst($inscripcion->estatus ?? ''),
                    $calificacion ? ucfirst($calificacion->desempenio) : 'Sin calificar',
                    $calificacion->observaciones ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Descargar lista de asistencia en PDF ────────────────────────────────

    public function listaAsistenciaPDF($id_grupo)
    {
        $instructor = $this->getInstructor();

        $grupo = $instructor->grupos()
            ->with([
                'actividad',
                'semestre',
                'ubicacion',
                'horarios.dia',
                'inscripciones.alumno.usuario',
                'inscripciones.alumno.carrera',
            ])
            ->where('id_grupo', $id_grupo)
            ->firstOrFail();

        abort_unless(
            $instructor->grupos->contains('id_grupo', $grupo->id_grupo),
            403,
            'No tienes permiso para descargar esta lista.'
        );

        $instructorUser = auth()->user();

        $pdf = Pdf::loadView('instructor.lista-asistencia-pdf', compact('grupo', 'instructorUser'))
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'defaultFont'     => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $actividad     = $grupo->actividad->nombre ?? 'Grupo';
        $grupoNum      = $grupo->grupo;
        $semestre      = ($grupo->semestre->año ?? '') . '-' . ($grupo->semestre->periodo ?? '');
        $nombreArchivo = 'Asistencia_' . str_replace(' ', '_', $actividad) . '_Grupo' . $grupoNum . '_' . $semestre . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    // ── Editar perfil del instructor ──────────────────────────────────────────

    public function editarPerfil()
    {
        $instructor = $this->getInstructor();
        $user       = auth()->user();
        return view('instructor.perfil', compact('instructor', 'user'));
    }

    public function actualizarPerfil(Request $request)
    {
        $user       = auth()->user();
        $instructor = $this->getInstructor();

        $request->validate([
            'nombre'           => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/u'],
            'apellido_paterno' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/u'],
            'apellido_materno' => ['nullable', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/u'],
            'telefono'         => ['nullable', 'regex:/^[0-9]{10}$/'],
            'especialidad'     => ['nullable', 'string', 'min:3', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s\-\.]+$/u'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nombre.required'           => 'El nombre es obligatorio.',
            'nombre.min'                => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max'                => 'El nombre no puede exceder 50 caracteres.',
            'nombre.regex'              => 'El nombre solo puede contener letras y espacios.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.min'      => 'El apellido paterno debe tener al menos 2 caracteres.',
            'apellido_paterno.max'      => 'El apellido paterno no puede exceder 50 caracteres.',
            'apellido_paterno.regex'    => 'El apellido paterno solo puede contener letras y espacios.',
            'apellido_materno.min'      => 'El apellido materno debe tener al menos 2 caracteres.',
            'apellido_materno.max'      => 'El apellido materno no puede exceder 50 caracteres.',
            'apellido_materno.regex'    => 'El apellido materno solo puede contener letras y espacios.',
            'telefono.regex'            => 'El teléfono debe contener exactamente 10 dígitos numéricos.',
            'especialidad.min'          => 'La especialidad debe tener al menos 3 caracteres.',
            'especialidad.max'          => 'La especialidad no puede exceder 100 caracteres.',
            'especialidad.regex'        => 'La especialidad contiene caracteres no permitidos.',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ]);

        $datosUsuario = [
            'nombre'           => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'telefono'         => $request->telefono,
        ];

        if ($request->filled('password')) {
            $datosUsuario['password'] = bcrypt($request->password);
        }

        $user->update($datosUsuario);

        $instructor->update([
            'especialidad' => $request->especialidad,
        ]);

        return redirect()->route('instructor.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
