@extends('layouts.app')
@section('title', 'Dashboard Coordinador')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Informacion</h3>
    </div>
    <div class="section-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Tarjetas de resumen --}}
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="card bg-c-blue order-card">
                    <div class="card-block">
                        <h5>Total de Grupos</h5>
                        <h2 class="text-right">
                            <i class="fas fa-layer-group f-left"></i>
                            <span>{{ $totalGrupos }}</span>
                        </h2>
                        <p class="m-b-0 text-right">
                            <a href="{{ route('coordinador.grupos') }}" class="text-white">Ver grupos</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-c-pink order-card">
                    <div class="card-block">
                        <h5>Sin Docente Asignado</h5>
                        <h2 class="text-right">
                            <i class="fas fa-user-slash f-left"></i>
                            <span>{{ $gruposSinDoc }}</span>
                        </h2>
                        <p class="m-b-0 text-right">
                            <a href="{{ route('coordinador.grupos') }}" class="text-white">Revisar</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-c-green order-card">
                    <div class="card-block">
                        <h5>Docentes Registrados</h5>
                        <h2 class="text-right">
                            <i class="fas fa-chalkboard-teacher f-left"></i>
                            <span>{{ $totalInstructores }}</span>
                        </h2>
                        <p class="m-b-0 text-right">
                            <a href="{{ route('coordinador.docentes') }}" class="text-white">Ver docentes</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-c-yellow order-card">
                    <div class="card-block">
                        <h5>Alumnos Inscritos</h5>
                        <h2 class="text-right">
                            <i class="fas fa-user-graduate f-left"></i>
                            <span>{{ $totalInscritos }}</span>
                        </h2>
                        <p class="m-b-0 text-right">
                            <a href="{{ route('coordinador.alumnos') }}" class="text-white">Ver alumnos</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grupos recientes --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Grupos Recientes</h4>
                        <a href="{{ route('coordinador.grupos.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Nuevo Grupo
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Actividad</th>
                                        <th>Grupo</th>
                                        <th>Docente</th>
                                        <th>Horario</th>
                                        <th>Cupo</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($gruposRecientes as $grupo)
                                    <tr>
                                        <td><strong>{{ $grupo->actividad->nombre ?? 'N/A' }}</strong></td>
                                        <td><span class="badge badge-primary">{{ $grupo->grupo }}</span></td>
                                        <td>
                                            @if($grupo->instructor)
                                                {{ $grupo->instructor->usuario->nombre_completo ?? 'N/A' }}
                                            @else
                                                <span class="badge badge-warning">Sin asignar</span>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach($grupo->horarios as $h)
                                                <small class="d-block">
                                                    {{ $h->dia->nombre_dia ?? '' }}
                                                    {{ substr($h->hora_inicio, 0, 5) }}–{{ substr($h->hora_fin, 0, 5) }}
                                                </small>
                                            @endforeach
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $grupo->cupo_ocupado >= $grupo->cupo_maximo ? 'danger' : 'success' }}">
                                                {{ $grupo->cupo_ocupado }}/{{ $grupo->cupo_maximo }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $grupo->estatus == 'abierta' ? 'success' : ($grupo->estatus == 'cancelada' ? 'danger' : 'secondary') }}">
                                                {{ ucfirst($grupo->estatus) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('coordinador.grupos.edit', $grupo->id_grupo) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i> Editar
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay grupos registrados.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
