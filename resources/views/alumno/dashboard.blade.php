@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">
            Bienvenido, {{ auth()->user()->nombre }} {{ auth()->user()->apellido_paterno }}
        </h3>
    </div>
    <div class="section-body">

        {{-- ── Tarjetas de info del alumno ────────────────────── --}}
        <div class="row mb-4">

            <div class="col-md-4 col-xl-4 d-flex">
                <div class="card bg-c-blue order-card w-100">
                    <div class="card-block" style="min-height:130px; display:flex; flex-direction:column; justify-content:center;">
                        <h6 class="text-white mb-2">
                            <i class="fas fa-graduation-cap"></i> Carrera
                        </h6>
                        <h5 class="text-white mb-2">
                            {{ $alumno->carrera->nombre ?? 'Sin asignar' }}
                        </h5>
                        <p class="m-b-0 text-white small mb-0">
                            Semestre: <strong>{{ $alumno->semestre_cursando ?? '-' }}°</strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xl-4 d-flex">
                <div class="card bg-c-green order-card w-100">
                    <div class="card-block" style="min-height:130px; display:flex; flex-direction:column; justify-content:center;">
                        <h6 class="text-white mb-2">
                            <i class="fas fa-star"></i> Créditos Acumulados
                        </h6>
                        <h2 class="text-white mb-2">
                            {{ $alumno->creditos_acumulados ?? 0 }}
                        </h2>
                        <p class="m-b-0 text-white small mb-0">créditos complementarios</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xl-4 d-flex">
                <div class="card bg-c-pink order-card w-100">
                    <div class="card-block" style="min-height:130px; display:flex; flex-direction:column; justify-content:center;">
                        <h6 class="text-white mb-2">
                            <i class="fas fa-id-card"></i> No. Control
                        </h6>
                        <h2 class="text-white mb-2">
                            {{ auth()->user()->num_control ?? 'N/A' }}
                        </h2>
                        <p class="m-b-0 text-white small mb-0">número de control</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Actividad actual ────────────────────────────────── --}}
        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-clipboard-check"></i> Mi Actividad Actual</h4>
                    </div>
                    <div class="card-body">
                        @if ($inscripcionActiva)
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge badge-success mr-2" style="font-size:0.9rem;">
                                    {{ ucfirst($inscripcionActiva->estatus) }}
                                </span>
                                <h5 class="mb-0">
                                    {{ $inscripcionActiva->grupo->actividad->nombre ?? 'N/A' }}
                                </h5>
                            </div>
                            <ul class="list-unstyled mb-3">
                                <li class="mb-1">
                                    <i class="fas fa-users text-primary"></i>
                                    <strong>Grupo:</strong> {{ $inscripcionActiva->grupo->grupo }}
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <strong>Lugar:</strong>
                                    {{ $inscripcionActiva->grupo->ubicacion->espacio ?? 'N/A' }}
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-laptop text-info"></i>
                                    <strong>Modalidad:</strong>
                                    {{ ucfirst($inscripcionActiva->grupo->modalidad) }}
                                </li>
                                @if ($inscripcionActiva->grupo->horarios->count() > 0)
                                <li class="mb-1">
                                    <i class="fas fa-clock text-warning"></i>
                                    <strong>Horario:</strong>
                                    @foreach ($inscripcionActiva->grupo->horarios as $h)
                                        {{ ucfirst($h->dia->nombre_dia ?? '') }}
                                        {{ substr($h->hora_inicio, 0, 5) }}-{{ substr($h->hora_fin, 0, 5) }}
                                        @if (!$loop->last) &nbsp;|&nbsp; @endif
                                    @endforeach
                                </li>
                                @endif
                                <li class="mb-1">
                                    <i class="fas fa-star text-success"></i>
                                    <strong>Créditos:</strong>
                                    {{ $inscripcionActiva->grupo->actividad->creditos ?? 'N/A' }}
                                </li>
                            </ul>
                            <a href="{{ route('inscripciones.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-clipboard-list"></i> Ver mis inscripciones
                            </a>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No estás inscrito en ninguna actividad complementaria.</p>
                                <a href="{{ route('actividades.index') }}" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Ver catálogo de actividades
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Accesos rápidos ─────────────────────────────── --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-bolt"></i> Accesos Rápidos</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('actividades.index') }}"
                               class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-th-list fa-lg text-primary mr-3"></i>
                                <div>
                                    <strong>Catálogo de Actividades</strong>
                                    <p class="mb-0 small text-muted">Explora todas las actividades disponibles</p>
                                </div>
                            </a>
                            <a href="{{ route('inscripciones.index') }}"
                               class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-clipboard-list fa-lg text-success mr-3"></i>
                                <div>
                                    <strong>Mis Inscripciones</strong>
                                    <p class="mb-0 small text-muted">Ve tu actividad actual e historial</p>
                                </div>
                            </a>
                            <a href="{{ route('alumno.perfil') }}"
                               class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-user-edit fa-lg text-warning mr-3"></i>
                                <div>
                                    <strong>Mi Perfil</strong>
                                    <p class="mb-0 small text-muted">Edita tus datos y contraseña</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
