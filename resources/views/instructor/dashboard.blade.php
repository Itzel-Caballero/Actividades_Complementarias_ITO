@extends('layouts.app')

@section('content')
<section class="section">

    <div class="section-header">
        <h3 class="page__heading">Dashboard</h3>
    </div>

    <div class="section-body">

        {{-- ── PERFIL DEL INSTRUCTOR ───────────────────────────────────────── --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">

                            {{-- Avatar con iniciales --}}
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-4 flex-shrink-0"
                                 style="width:72px; height:72px; background:#6777ef; font-size:1.8rem; font-weight:700; color:#fff;">
                                {{ strtoupper(substr(auth()->user()->nombre ?? 'I', 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellido_paterno ?? '', 0, 1)) }}
                            </div>

                            {{-- Datos personales --}}
                            <div class="flex-grow-1">
                                <h5 class="mb-1 font-weight-bold">
                                    {{ auth()->user()->nombre }}
                                    {{ auth()->user()->apellido_paterno }}
                                    {{ auth()->user()->apellido_materno }}
                                </h5>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-envelope mr-1"></i>{{ auth()->user()->email }}
                                    &nbsp;&nbsp;
                                    <i class="fas fa-phone mr-1"></i>{{ auth()->user()->telefono ?? 'Sin teléfono' }}
                                </p>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-clock mr-1"></i>Último acceso:
                                    {{ auth()->user()->ultimo_acceso
                                        ? \Carbon\Carbon::parse(auth()->user()->ultimo_acceso)->format('d/m/Y H:i')
                                        : 'N/A' }}
                                </p>
                            </div>

                            {{-- Departamento y especialidad --}}
                            <div class="text-right ml-4 flex-shrink-0">
                                <p class="mb-1">
                                    <span class="badge badge-primary">
                                        <i class="fas fa-building mr-1"></i>
                                        {{ $instructor->departamento->nombre ?? 'Sin departamento' }}
                                    </span>
                                </p>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-star text-warning mr-1"></i>
                                    {{ $instructor->especialidad ?? 'Sin especialidad' }}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TARJETAS DE MÉTRICAS (mismo estilo que admin/alumno) ─────────── --}}
        <p class="text-muted mb-1">
            <small><i class="fas fa-chart-bar mr-1"></i> Resumen del semestre</small>
        </p>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="card bg-c-blue order-card">
                    <div class="card-block">
                        <h5>Total de Grupos</h5>
                        <h2 class="text-right">
                            <i class="fas fa-layer-group f-left"></i>
                            <span>{{ $totalGrupos }}</span>
                        </h2>
                        <p class="m-b-0 text-right">
                            <a href="{{ route('instructor.mis-grupos') }}" class="text-white">Ver grupos</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card bg-c-green order-card">
                    <div class="card-block">
                        <h5>Grupos Activos</h5>
                        <h2 class="text-right">
                            <i class="fas fa-door-open f-left"></i>
                            <span>{{ $gruposActivos }}</span>
                        </h2>
                        <p class="m-b-0 text-right">
                            <span class="text-white">Semestre en curso</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card bg-c-pink order-card">
                    <div class="card-block">
                        <h5>Total de Alumnos</h5>
                        <h2 class="text-right">
                            <i class="fas fa-user-graduate f-left"></i>
                            <span>{{ $totalAlumnos }}</span>
                        </h2>
                        <p class="m-b-0 text-right">
                            <span class="text-white">Inscritos en tus grupos</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card {{ $totalPendientes > 0 ? 'bg-c-pink' : 'bg-c-green' }} order-card">
                    <div class="card-block">
                        <h5>Por Calificar</h5>
                        <h2 class="text-right">
                            <i class="fas fa-{{ $totalPendientes > 0 ? 'exclamation-circle' : 'check-circle' }} f-left"></i>
                            <span>{{ $totalPendientes }}</span>
                        </h2>
                        <p class="m-b-0 text-right">
                            <a href="{{ route('instructor.mis-grupos') }}" class="text-white">
                                {{ $totalCalificados }} ya calificados
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TABLA RESUMEN DE GRUPOS ─────────────────────────────────────── --}}
        @if ($instructor && $instructor->grupos->count() > 0)
        <p class="text-muted mb-1 mt-2">
            <small><i class="fas fa-chalkboard mr-1"></i> Mis grupos</small>
        </p>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Grupos asignados</h6>
                        <a href="{{ route('instructor.mis-grupos') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye mr-1"></i> Ver con alumnos
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Actividad</th>
                                        <th>Grupo</th>
                                        <th>Semestre</th>
                                        <th>Modalidad</th>
                                        <th>Alumnos</th>
                                        <th>Calificados</th>
                                        <th>Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($instructor->grupos as $grupo)
                                    @php
                                        $calificadosGrupo = $grupo->inscripciones->filter(
                                            fn($i) => $i->calificaciones->count() > 0
                                        )->count();
                                        $pct = $grupo->cupo_maximo > 0
                                            ? round(($grupo->cupo_ocupado / $grupo->cupo_maximo) * 100)
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $grupo->actividad->nombre ?? 'N/A' }}</strong></td>
                                        <td><span class="badge badge-secondary">{{ $grupo->grupo }}</span></td>
                                        <td>{{ $grupo->semestre->año ?? '—' }}-{{ $grupo->semestre->periodo ?? '—' }}</td>
                                        <td>{{ ucfirst($grupo->modalidad) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2">{{ $grupo->cupo_ocupado }}/{{ $grupo->cupo_maximo }}</span>
                                                <div class="progress flex-grow-1" style="height:6px; min-width:50px;">
                                                    <div class="progress-bar bg-{{ $pct >= 90 ? 'danger' : ($pct >= 60 ? 'warning' : 'success') }}"
                                                         style="width:{{ $pct }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($grupo->inscripciones->count() > 0)
                                                <span class="{{ $calificadosGrupo < $grupo->inscripciones->count() ? 'text-warning' : 'text-success' }}">
                                                    <i class="fas fa-{{ $calificadosGrupo < $grupo->inscripciones->count() ? 'exclamation-circle' : 'check-circle' }} mr-1"></i>
                                                    {{ $calificadosGrupo }}/{{ $grupo->inscripciones->count() }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $grupo->estatus === 'abierta' ? 'success' : ($grupo->estatus === 'cancelada' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($grupo->estatus) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle mr-2"></i>
            Aún no tienes grupos asignados. Contacta al coordinador.
        </div>
        @endif

    </div>
</section>
@endsection
