@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Mis Grupos</h3>
    </div>
    <div class="section-body">

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ $message }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4>Grupos que imparto</h4>
            </div>
            <div class="card-body">

                @if ($grupos->isEmpty())
                    <div class="alert alert-info">
                        No tienes grupos asignados actualmente.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Actividad</th>
                                    <th>Grupo</th>
                                    <th>Modalidad</th>
                                    <th>Inscritos / Cupo</th>
                                    <th>Estatus</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Ubicación</th>
                                    <th>Horarios</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grupos as $grupo)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $grupo->actividad->nombre ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">
                                            {{ $grupo->actividad->departamento->nombre ?? '' }}
                                        </small>
                                    </td>
                                    <td>{{ $grupo->grupo }}</td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $grupo->modalidad ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $grupo->inscripciones->count() >= $grupo->cupo_maximo ? 'danger' : 'success' }}">
                                            {{ $grupo->inscripciones->count() }} / {{ $grupo->cupo_maximo }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $grupo->estatus === 'activo' ? 'success' : 'warning' }}">
                                            {{ ucfirst($grupo->estatus) }}
                                        </span>
                                    </td>
                                    <td>{{ $grupo->fecha_inicio ? \Carbon\Carbon::parse($grupo->fecha_inicio)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $grupo->fecha_fin ? \Carbon\Carbon::parse($grupo->fecha_fin)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $grupo->ubicacion->nombre ?? 'N/A' }}</td>
                                    <td>
                                        @forelse ($grupo->horarios as $horario)
                                            <small>
                                                {{ $horario->dia->nombre ?? '' }}
                                                {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}
                                            </small><br>
                                        @empty
                                            <small class="text-muted">Sin horario</small>
                                        @endforelse
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
</section>
@endsection
