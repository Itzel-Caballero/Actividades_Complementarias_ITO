@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Catálogo de Actividades Complementarias</h3>
    </div>
    <div class="section-body">

        {{-- Banner: ya está inscrito en una actividad --}}
        @if (!auth()->user()->hasRole('admin') && isset($inscripcionActiva) && $inscripcionActiva)
            <div class="alert alert-warning d-flex align-items-center justify-content-between mb-3">
                <div>
                    <i class="fas fa-info-circle"></i>
                    <strong>Ya estás inscrito</strong> en
                    <strong>{{ $inscripcionActiva->grupo->actividad->nombre }}</strong>.
                    Para inscribirte a otra actividad primero debes darte de baja.
                </div>
                <a href="{{ route('inscripciones.index') }}" class="btn btn-sm btn-warning ml-3">
                    <i class="fas fa-clipboard-list"></i> Ver mi inscripción
                </a>
            </div>
        @endif

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ $message }}</strong>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            @forelse ($actividades as $actividad)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $actividad->nombre }}</h5>
                        <span class="badge badge-{{ $actividad->creditos == 2 ? 'success' : 'info' }}">
                            {{ $actividad->creditos }} crédito(s)
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">{{ Str::limit($actividad->descripcion, 100) }}</p>

                        <ul class="list-unstyled">
                            <li>
                                <i class="fa fa-building"></i>
                                <strong>Departamento:</strong>
                                {{ $actividad->departamento->nombre ?? 'N/A' }}
                            </li>
                            <li>
                                <i class="fa fa-layer-group"></i>
                                <strong>Nivel:</strong>
                                {{ $actividad->nivel_actividad ?? 'N/A' }}
                            </li>
                            <li>
                                <i class="fa fa-users"></i>
                                <strong>Grupos disponibles:</strong>
                                {{ $actividad->grupos->where('estatus', 'abierta')->count() }}
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="badge badge-{{ $actividad->disponible ? 'success' : 'danger' }}">
                            {{ $actividad->disponible ? 'Disponible' : 'No disponible' }}
                        </span>
                        @if ($actividad->disponible && $actividad->grupos->where('estatus', 'abierta')->count() > 0)
                            <a href="{{ route('actividades.show', $actividad->id_actividad) }}"
                               class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> Ver grupos
                            </a>
                        @else
                            <span class="btn btn-secondary btn-sm disabled">Sin grupos</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    No hay actividades disponibles por el momento.
                </div>
            </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {!! $actividades->links() !!}
        </div>

    </div>
</section>
@endsection