@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Mis Inscripciones</h3>
    </div>
    <div class="section-body">

        {{-- Mensajes de éxito / error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>{{ session('error') }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- ══════════════════════════════════════════
             SECCIÓN 1: ACTIVIDAD ACTUAL
        ══════════════════════════════════════════ --}}
        <h5 class="mb-3">Actividad Actual</h5>

        @if ($inscripcionActiva)
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fa fa-check-circle"></i>
                        {{ $inscripcionActiva->grupo->actividad->nombre ?? 'N/A' }}
                    </h5>
                    <span class="badge badge-light text-success">
                        {{ ucfirst($inscripcionActiva->estatus) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fa fa-users"></i> <strong>Grupo:</strong> {{ $inscripcionActiva->grupo->grupo }}</p>
                            <p><i class="fa fa-map-marker-alt"></i> <strong>Lugar:</strong>
                                {{ $inscripcionActiva->grupo->ubicacion->espacio ?? 'N/A' }}
                            </p>
                            <p><i class="fa fa-laptop"></i> <strong>Modalidad:</strong>
                                {{ ucfirst($inscripcionActiva->grupo->modalidad) }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fa fa-calendar"></i> <strong>Periodo:</strong>
                                {{ $inscripcionActiva->grupo->fecha_inicio }} al {{ $inscripcionActiva->grupo->fecha_fin }}
                            </p>
                            @if ($inscripcionActiva->grupo->horarios->count() > 0)
                                <p><i class="fa fa-clock"></i> <strong>Horario:</strong>
                                    @foreach ($inscripcionActiva->grupo->horarios as $horario)
                                        {{ ucfirst($horario->dia->nombre_dia ?? '') }}
                                        {{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}
                                        @if (!$loop->last) | @endif
                                    @endforeach
                                </p>
                            @endif
                            <p><i class="fa fa-star"></i> <strong>Créditos:</strong>
                                {{ $inscripcionActiva->grupo->actividad->creditos ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('inscripciones.baja', $inscripcionActiva->id_inscripcion) }}"
                          method="POST"
                          onsubmit="return confirm('¿Estás seguro de que deseas darte de baja? Podrás inscribirte a otra actividad.')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fa fa-sign-out-alt"></i> Darme de baja
                        </button>
                    </form>
                </div>
            </div>

        @else
            <div class="card mb-4 border-0 bg-light">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-plus fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No tienes ninguna actividad activa</h5>
                    <p class="text-muted mb-4">
                        Explora el catálogo y elige la actividad complementaria que más te interese.
                    </p>
                    <a href="{{ route('actividades.index') }}" class="btn btn-primary">
                        <i class="fas fa-search"></i> Ir al Catálogo de Actividades
                    </a>
                </div>
            </div>
        @endif

        {{-- ══════════════════════════════════════════
             SECCIÓN 2: OTRAS ACTIVIDADES DISPONIBLES
             (solo se muestra si NO tiene inscripción activa)
        ══════════════════════════════════════════ --}}
        @if ($otrasActividades)
            <h5 class="mb-3">Otras Actividades (si deseas cambiarte)</h5>
            <div class="row mb-4">
                @forelse ($otrasActividades as $actividad)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $actividad->nombre }}</h6>
                                <span class="badge badge-{{ $actividad->creditos == 2 ? 'success' : 'info' }}">
                                    {{ $actividad->creditos }} crédito(s)
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">{{ Str::limit($actividad->descripcion, 80) }}</p>
                                <p class="mb-1 small">
                                    <strong>Departamento:</strong>
                                    {{ $actividad->departamento->nombre ?? 'N/A' }}
                                </p>
                                <p class="mb-1 small">
                                    <strong>Grupos abiertos:</strong>
                                    {{ $actividad->grupos->where('estatus', 'abierta')->count() }}
                                </p>
                            </div>
                            <div class="card-footer">
                                @if ($actividad->grupos->where('estatus', 'abierta')->count() > 0)
                                    <a href="{{ route('actividades.show', $actividad->id_actividad) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> Ver grupos e inscribirme
                                    </a>
                                @else
                                    <span class="btn btn-secondary btn-sm disabled">Sin grupos abiertos</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning">No hay actividades disponibles por el momento.</div>
                    </div>
                @endforelse
            </div>
            <div class="d-flex justify-content-center mb-4">
                {!! $otrasActividades->links() !!}
            </div>
        @endif

        {{-- ══════════════════════════════════════════
             SECCIÓN 3: HISTORIAL
        ══════════════════════════════════════════ --}}
        <h5 class="mb-3">Historial de Inscripciones</h5>

        @if ($historial->count() > 0)
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Actividad</th>
                                <th>Grupo</th>
                                <th>Créditos</th>
                                <th>Estatus</th>
                                <th>Desempeño</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($historial as $item)
                                @php
                                    $calificacion = $item->calificaciones->first();
                                    $colores = [
                                        'aprobado'     => 'success',
                                        'reprobado'    => 'danger',
                                        'dado_de_baja' => 'secondary',
                                    ];
                                    $color = $colores[$item->estatus] ?? 'secondary';
                                    $iconosDesempenio = [
                                        'excelente' => ['color' => 'success', 'icon' => 'fa-star',        'label' => 'Excelente'],
                                        'bueno'     => ['color' => 'info',    'icon' => 'fa-thumbs-up',   'label' => 'Bueno'],
                                        'malo'      => ['color' => 'danger',  'icon' => 'fa-thumbs-down', 'label' => 'Malo'],
                                    ];
                                    $desempenioInfo = $calificacion ? ($iconosDesempenio[$calificacion->desempenio] ?? null) : null;
                                @endphp
                                <tr>
                                    <td>{{ $item->grupo->actividad->nombre ?? 'N/A' }}</td>
                                    <td>{{ $item->grupo->grupo ?? 'N/A' }}</td>
                                    <td>{{ $item->grupo->actividad->creditos ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $color }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->estatus)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($desempenioInfo)
                                            <span class="text-{{ $desempenioInfo['color'] }}">
                                                <i class="fas {{ $desempenioInfo['icon'] }}"></i>
                                                {{ $desempenioInfo['label'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $calificacion->observaciones ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-secondary">
                <i class="fa fa-history"></i> Aún no tienes historial de inscripciones.
            </div>
        @endif

    </div>
</section>
@endsection
