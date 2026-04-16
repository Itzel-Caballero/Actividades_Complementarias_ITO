@extends('layouts.app')
@section('title', 'Detalle del Periodo')

@section('content')
<section class="section">
    <div class="section-header d-flex align-items-center gap-3">
        <h3 class="page__heading mb-0">
            Periodo: {{ $semestre->periodo == 1 ? 'Enero–Junio' : 'Agosto–Diciembre' }} {{ $semestre->año }}
        </h3>
        <span class="badge badge-{{ $semestre->status == 'activo' ? 'success' : 'secondary' }} ml-2" style="font-size:13px;">
            {{ strtoupper($semestre->status) }}
        </span>
    </div>
    <div class="section-body">

        <a href="{{ route('admin.semestres.index') }}" class="btn btn-secondary btn-sm mb-3">
            <i class="fa fa-arrow-left"></i> Volver al listado
        </a>

        {{-- Datos del periodo --}}
        <div class="card mb-4">
            <div class="card-header"><h4>Información del Periodo</h4></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Inicio del semestre:</strong><br>
                        {{ \Carbon\Carbon::parse($semestre->fecha_inicio)->format('d/m/Y') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Fin del semestre:</strong><br>
                        {{ \Carbon\Carbon::parse($semestre->fecha_fin)->format('d/m/Y') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Inicio inscripciones:</strong><br>
                        {{ \Carbon\Carbon::parse($semestre->fecha_inicio_inscripciones)->format('d/m/Y') }}
                        @if($semestre->hora_inicio_inscripciones)
                            <span class="badge badge-light text-dark">{{ substr($semestre->hora_inicio_inscripciones,0,5) }}</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <strong>Fin inscripciones:</strong><br>
                        {{ \Carbon\Carbon::parse($semestre->fecha_fin_inscripciones)->format('d/m/Y') }}
                        @if($semestre->hora_fin_inscripciones)
                            <span class="badge badge-light text-dark">{{ substr($semestre->hora_fin_inscripciones,0,5) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Grupos del periodo --}}
        @forelse($semestre->grupos as $grupo)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <span class="badge badge-primary mr-2">{{ $grupo->grupo }}</span>
                    {{ $grupo->actividad->nombre ?? 'Sin actividad' }}
                </h5>
                <div>
                    <small class="text-muted mr-3">
                        <i class="fas fa-chalkboard-teacher"></i>
                        {{ $grupo->instructor->usuario->nombre_completo ?? 'Sin instructor' }}
                    </small>
                    <span class="badge badge-{{ $grupo->estatus == 'abierta' ? 'success' : 'secondary' }}">
                        {{ ucfirst($grupo->estatus) }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>No. Control</th>
                                <th>Alumno</th>
                                <th>Carrera</th>
                                <th>Fecha Inscripción</th>
                                <th>Estatus</th>
                                <th>Calificación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grupo->inscripciones as $insc)
                            @php
                                $cal = $insc->calificaciones->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><code>{{ $insc->alumno->usuario->num_control ?? '—' }}</code></td>
                                <td><strong>{{ $insc->alumno->usuario->nombre_completo ?? 'N/A' }}</strong></td>
                                <td>
                                    <span class="badge badge-info" style="font-size:10px;">
                                        {{ $insc->alumno->carrera->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        {{ $insc->fecha_inscripcion
                                            ? \Carbon\Carbon::parse($insc->fecha_inscripcion)->format('d/m/Y')
                                            : '—' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $insc->estatus == 'cursando' ? 'success' : ($insc->estatus == 'baja' ? 'danger' : 'info') }}">
                                        {{ ucfirst($insc->estatus) }}
                                    </span>
                                </td>
                                <td>
                                    @if($cal)
                                        <span class="badge badge-{{ $cal->calificacion >= 70 ? 'success' : 'danger' }}">
                                            {{ $cal->calificacion }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-2">Sin alumnos inscritos en este grupo.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info">No hay grupos registrados en este periodo.</div>
        @endforelse

    </div>
</section>
@endsection
