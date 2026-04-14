@extends('layouts.app')

@section('content')
<section class="section">

    {{-- ── ENCABEZADO ─────────────────────────────────────────────────────── --}}
    <div class="section-header d-flex justify-content-between align-items-center">
        <h3 class="page__heading">
            <i class="fas fa-chalkboard-teacher mr-2"></i>Panel del Instructor
        </h3>
        <span class="text-muted small">
            {{ auth()->user()->nombre }} {{ auth()->user()->apellido_paterno }}
            &nbsp;·&nbsp;
            <i class="fas fa-building mr-1"></i>{{ $instructor->departamento->nombre ?? 'Sin departamento' }}
        </span>
    </div>

    <div class="section-body">

        {{-- Alertas --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- ── TARJETAS RESUMEN ────────────────────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-c-blue order-card">
                    <div class="card-block">
                        <h5><i class="fas fa-layer-group mr-2"></i>Total de Grupos</h5>
                        <h2 class="text-right">
                            <i class="fas fa-chalkboard f-left"></i>
                            <span>{{ $grupos->count() }}</span>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-c-green order-card">
                    <div class="card-block">
                        <h5><i class="fas fa-check-circle mr-2"></i>Grupos Activos</h5>
                        <h2 class="text-right">
                            <i class="fas fa-door-open f-left"></i>
                            <span>{{ $grupos->where('estatus', 'abierta')->count() }}</span>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-c-pink order-card">
                    <div class="card-block">
                        <h5><i class="fas fa-users mr-2"></i>Total de Alumnos</h5>
                        <h2 class="text-right">
                            <i class="fas fa-user-graduate f-left"></i>
                            <span>{{ $grupos->sum('cupo_ocupado') }}</span>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── GRUPOS + ALUMNOS ────────────────────────────────────────────── --}}
        @forelse ($grupos as $grupo)
        @php
            $pct = $grupo->cupo_maximo > 0
                ? round(($grupo->cupo_ocupado / $grupo->cupo_maximo) * 100)
                : 0;
            $colorBarra = $pct >= 90 ? 'danger' : ($pct >= 60 ? 'warning' : 'success');
            $calificados = $grupo->inscripciones->filter(function($i) {
                return $i->calificaciones->count() > 0;
            })->count();
            $pendientes = $grupo->inscripciones->count() - $calificados;
        @endphp

        <div class="card mb-4 shadow-sm">

            {{-- Cabecera del grupo (clic para expandir) --}}
            <div class="card-header d-flex justify-content-between align-items-center"
                 data-toggle="collapse"
                 data-target="#grupo-{{ $grupo->id_grupo }}"
                 style="cursor: pointer;">

                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-book-open text-primary mr-2"></i>
                        {{ $grupo->actividad->nombre ?? 'Sin actividad' }}
                        <span class="badge badge-secondary ml-2">Grupo {{ $grupo->grupo }}</span>
                        <span class="badge badge-{{ $grupo->estatus === 'abierta' ? 'success' : ($grupo->estatus === 'cancelada' ? 'danger' : 'warning') }} ml-1">
                            {{ ucfirst($grupo->estatus) }}
                        </span>
                    </h5>
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Semestre {{ $grupo->semestre->año ?? '—' }}-{{ $grupo->semestre->periodo ?? '—' }}
                        &nbsp;·&nbsp;
                        <i class="fas fa-laptop mr-1"></i>{{ ucfirst($grupo->modalidad) }}
                        &nbsp;·&nbsp;
                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $grupo->ubicacion->espacio ?? 'Virtual' }}
                    </small>
                </div>

                <div class="text-right ml-3" style="min-width: 160px;">
                    {{-- Barra de cupo --}}
                    <small class="text-muted">{{ $grupo->cupo_ocupado }}/{{ $grupo->cupo_maximo }} alumnos</small>
                    <div class="progress mt-1" style="height:8px;">
                        <div class="progress-bar bg-{{ $colorBarra }}" style="width:{{ $pct }}%"></div>
                    </div>
                    {{-- Indicador de calificaciones --}}
                    <small class="mt-1 d-block">
                        @if ($pendientes > 0)
                            <span class="text-warning">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $pendientes }} sin calificar
                            </span>
                        @else
                            <span class="text-success">
                                <i class="fas fa-check-circle mr-1"></i>Todos calificados
                            </span>
                        @endif
                    </small>
                    <i class="fas fa-chevron-down mt-1 text-muted"></i>
                </div>
            </div>

            {{-- ── CONTENIDO COLAPSABLE: horarios + tabla de alumnos ──────── --}}
            <div id="grupo-{{ $grupo->id_grupo }}" class="collapse">
                <div class="card-body pb-0">

                    {{-- Horarios del grupo --}}
                    <div class="mb-3">
                        <strong><i class="fas fa-clock text-info mr-1"></i> Horarios:</strong>
                        @forelse ($grupo->horarios as $horario)
                            <span class="badge badge-light border ml-1 px-2 py-1">
                                {{ ucfirst($horario->dia->nombre_dia ?? '') }}
                                {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} –
                                {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                            </span>
                        @empty
                            <span class="text-muted ml-2">Sin horario registrado</span>
                        @endforelse
                    </div>

                    {{-- Tabla de alumnos --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Alumno</th>
                                    <th>No. Control</th>
                                    <th>Carrera</th>
                                    <th>Estatus</th>
                                    <th>Calificación</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($grupo->inscripciones as $idx => $inscripcion)
                                @php
                                    $alumno      = $inscripcion->alumno;
                                    $usuario     = $alumno->usuario ?? null;
                                    $calificacion = $inscripcion->calificaciones->first();
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <strong>
                                            {{ $usuario->nombre ?? '—' }}
                                            {{ $usuario->apellido_paterno ?? '' }}
                                            {{ $usuario->apellido_materno ?? '' }}
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border">
                                            {{ $usuario->num_control ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $alumno->carrera->nombre ?? 'N/A' }}</td>
                                    <td>
                                        @switch($inscripcion->estatus)
                                            @case('inscrito')
                                                <span class="badge badge-secondary">Inscrito</span> @break
                                            @case('cursando')
                                                <span class="badge badge-info">Cursando</span> @break
                                            @case('aprobado')
                                                <span class="badge badge-success">Aprobado</span> @break
                                            @case('reprobado')
                                                <span class="badge badge-danger">Reprobado</span> @break
                                            @default
                                                <span class="badge badge-warning">{{ ucfirst($inscripcion->estatus) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if ($calificacion)
                                            @if ($calificacion->desempenio == 1)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-thumbs-up mr-1"></i>Bueno/Excelente
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-thumbs-down mr-1"></i>Malo
                                                </span>
                                            @endif
                                            @if ($calificacion->observaciones)
                                                <br><small class="text-muted">{{ $calificacion->observaciones }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted small">
                                                <i class="fas fa-minus-circle mr-1"></i>Sin calificar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('instructor.calificar', $inscripcion->id_inscripcion) }}"
                                           class="btn btn-sm btn-{{ $calificacion ? 'warning' : 'primary' }}"
                                           title="{{ $calificacion ? 'Editar calificación' : 'Calificar alumno' }}">
                                            <i class="fas fa-{{ $calificacion ? 'edit' : 'star' }} mr-1"></i>
                                            {{ $calificacion ? 'Editar' : 'Calificar' }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        No hay alumnos inscritos en este grupo.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pie del grupo --}}
                <div class="card-footer text-right bg-light">
                    <small class="text-muted mr-3">
                        <i class="fas fa-star text-warning mr-1"></i>
                        {{ $calificados }} de {{ $grupo->inscripciones->count() }} calificados
                    </small>
                </div>
            </div>

        </div>{{-- /card grupo --}}
        @empty
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle mr-2"></i>
            No tienes grupos asignados por el momento.
        </div>
        @endforelse

    </div>
</section>
@endsection

@section('scripts')
<script>
    // Rotar el chevron al expandir/colapsar
    document.querySelectorAll('[data-toggle="collapse"]').forEach(function (header) {
        const target = document.querySelector(header.dataset.target);
        if (!target) return;

        target.addEventListener('show.bs.collapse', function () {
            header.querySelector('.fa-chevron-down').style.transform = 'rotate(180deg)';
        });
        target.addEventListener('hide.bs.collapse', function () {
            header.querySelector('.fa-chevron-down').style.transform = 'rotate(0deg)';
        });
    });
</script>
@endsection
