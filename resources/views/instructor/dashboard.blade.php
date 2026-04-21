@extends('layouts.app')

@section('content')
<section class="section">

    <div class="section-header">
        <h3 class="page__heading">Dashboard</h3>
    </div>

    <div class="section-body">

        {{-- ── ALERTA PERIODO ──────────────────────────────────────────────── --}}
        @if (!$semestreActivo)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Sin periodo activo.</strong>
                Actualmente no hay un semestre activo en el sistema. Podrás ver tu información pero no podrás calificar alumnos.
            </div>
        @else
            <div class="alert alert-success py-2">
                <i class="fas fa-calendar-check mr-2"></i>
                Periodo activo: <strong>{{ $semestreActivo->label }}</strong>
                &nbsp;·&nbsp;
                {{ \Carbon\Carbon::parse($semestreActivo->fecha_inicio)->format('d/m/Y') }} –
                {{ \Carbon\Carbon::parse($semestreActivo->fecha_fin)->format('d/m/Y') }}
            </div>
        @endif

        {{-- ── PERFIL ───────────────────────────────────────────────────────── --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-4 flex-shrink-0"
                                 style="width:72px;height:72px;background:#6777ef;font-size:1.8rem;font-weight:700;color:#fff;">
                                {{ strtoupper(substr(auth()->user()->nombre ?? 'I', 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellido_paterno ?? '', 0, 1)) }}
                            </div>
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
                            <div class="text-right ml-4 flex-shrink-0">
                                <p class="mb-1">
                                    <span class="badge badge-primary">
                                        <i class="fas fa-building mr-1"></i>
                                        {{ $instructor->departamento->nombre ?? 'Sin departamento' }}
                                    </span>
                                </p>
                                <p class="mb-2 text-muted small">
                                    <i class="fas fa-star text-warning mr-1"></i>
                                    {{ $instructor->especialidad ?? 'Sin especialidad' }}
                                </p>
                                <a href="{{ route('instructor.perfil') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-user-edit mr-1"></i> Editar perfil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── MÉTRICAS ─────────────────────────────────────────────────────── --}}
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

        {{-- ── TABLA: ALUMNOS PENDIENTES DE CALIFICAR ───────────────────────── --}}
        <p class="text-muted mb-1 mt-2">
            <small><i class="fas fa-exclamation-circle mr-1 text-warning"></i> Alumnos pendientes de calificar</small>
        </p>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Pendientes de calificación</h6>
                        <a href="{{ route('instructor.mis-grupos') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-list mr-1"></i> Ver lista completa
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if ($alumnosPendientes->isEmpty())
                            <div class="text-center text-success py-4">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p class="mb-0">¡Todos los alumnos han sido calificados!</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Alumno</th>
                                        <th>No. Control</th>
                                        <th>Actividad</th>
                                        <th>Grupo</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alumnosPendientes as $i => $pendiente)
                                    <tr>
                                        <td class="text-muted">{{ $i + 1 }}</td>
                                        <td><strong>{{ $pendiente['nombre'] }}</strong></td>
                                        <td>
                                            <span class="badge badge-light border">
                                                {{ $pendiente['num_control'] ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $pendiente['actividad'] }}</td>
                                        <td><span class="badge badge-secondary">{{ $pendiente['grupo'] }}</span></td>
                                        <td class="text-center">
                                            @if ($semestreActivo)
                                                <a href="{{ route('instructor.calificar', $pendiente['id_inscripcion']) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-star mr-1"></i> Calificar
                                                </a>
                                            @else
                                                <span class="btn btn-sm btn-secondary disabled">Sin periodo activo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
