@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">{{ $actividad->nombre }}</h3>
    </div>
    <div class="section-body">
        <div class="row">

            {{-- Info de la actividad --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Información</h4>
                    </div>
                    <div class="card-body">
                        <p>{{ $actividad->descripcion }}</p>
                        <hr>
                        <p><strong>Departamento:</strong> {{ $actividad->departamento->nombre ?? 'N/A' }}</p>
                        <p><strong>Créditos:</strong>
                            <span class="badge badge-success">{{ $actividad->creditos }}</span>
                        </p>
                        <p><strong>Nivel:</strong> {{ $actividad->nivel_actividad ?? 'N/A' }}</p>
                        @if ($actividad->requisitos)
                            <p><strong>Requisitos:</strong> {{ $actividad->requisitos }}</p>
                        @endif
                        <p><strong>Carreras:</strong><br>
                            @foreach ($actividad->carreras as $carrera)
                                <span class="badge badge-info">{{ $carrera->nombre }}</span>
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>

            {{-- Grupos disponibles --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Grupos Disponibles</h4>
                    </div>
                    <div class="card-body">
                        @forelse ($actividad->grupos->where('estatus', 'abierta') as $grupo)
                        <div class="card mb-3 border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5>Grupo {{ $grupo->grupo }}</h5>
                                        <p class="mb-1">
                                            <i class="fa fa-chalkboard-teacher"></i>
                                            <strong>Instructor:</strong>
                                            {{ $grupo->instructor->usuario->nombre_completo ?? 'N/A' }}
                                        </p>
                                        <p class="mb-1">
                                            <i class="fa fa-map-marker-alt"></i>
                                            <strong>Modalidad:</strong>
                                            <span class="badge badge-secondary">{{ $grupo->modalidad }}</span>
                                        </p>
                                        @if ($grupo->ubicacion)
                                        <p class="mb-1">
                                            <i class="fa fa-map-pin"></i>
                                            <strong>Lugar:</strong> {{ $grupo->ubicacion->espacio }}
                                        </p>
                                        @endif
                                        <p class="mb-1">
                                            <i class="fa fa-calendar"></i>
                                            <strong>Periodo:</strong>
                                            {{ $grupo->fecha_inicio }} al {{ $grupo->fecha_fin }}
                                        </p>
                                        {{-- Horarios --}}
                                        @if ($grupo->horarios->count() > 0)
                                        <p class="mb-1">
                                            <i class="fa fa-clock"></i>
                                            <strong>Horario:</strong>
                                            @foreach ($grupo->horarios as $horario)
                                                {{ $horario->dia->nombre_dia ?? '' }}
                                                {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}
                                            @endforeach
                                        </p>
                                        @endif
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <p class="mb-1">
                                            <strong>Cupo</strong><br>
                                            <span class="h4">{{ $grupo->cupo_ocupado }}/{{ $grupo->cupo_maximo }}</span>
                                        </p>

                                        @php
    $alumno = auth()->user() ? \App\Models\Alumno::where('id_alumno', auth()->user()->id)->first() : null;
    $yaInscrito = $alumno ? \App\Models\Inscripcion::where('id_alumno', $alumno->id_alumno)->where('id_grupo', $grupo->id_grupo)->exists() : false;
@endphp

@if ($yaInscrito)
    <span class="btn btn-success btn-sm disabled">
        <i class="fa fa-check"></i> Inscrito
    </span>
@elseif ($grupo->cupo_ocupado < $grupo->cupo_maximo)
    <form action="{{ route('inscripciones.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_grupo" value="{{ $grupo->id_grupo }}">
        <button type="submit" class="btn btn-primary btn-sm"
            onclick="return confirm('¿Confirmas tu inscripción a este grupo?')">
            <i class="fa fa-check"></i> Inscribirme
        </button>
    </form>
@else
    <span class="btn btn-secondary btn-sm disabled">Sin cupo</span>
@endif
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-warning">
                            No hay grupos abiertos para esta actividad.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <a href="{{ route('actividades.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Volver al catálogo
        </a>
    </div>
</section>
@endsection