@extends('layouts.app')
@section('title', 'Alumnos')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Alumnos Inscritos</h3>
    </div>
    <div class="section-body">

        {{-- Filtros --}}
        <div class="card">
            <div class="card-body py-2">
                <form method="GET" class="form-row align-items-end">
                    <div class="col-auto mb-2">
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                               class="form-control form-control-sm"
                               placeholder="Nombre o No. de control...">
                    </div>
                    <div class="col-auto mb-2">
                        <select name="id_carrera" class="form-control form-control-sm">
                            <option value="">Todas las carreras</option>
                            @foreach($carreras as $car)
                                <option value="{{ $car->id_carrera }}"
                                    {{ request('id_carrera') == $car->id_carrera ? 'selected' : '' }}>
                                    {{ $car->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto mb-2">
                        <select name="semestre" class="form-control form-control-sm">
                            <option value="">Todos los semestres</option>
                            @for($s = 1; $s <= 12; $s++)
                                <option value="{{ $s }}" {{ request('semestre') == $s ? 'selected' : '' }}>
                                    {{ $s }}° semestre
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto mb-2">
                        <select name="inscripcion_activa" class="form-control form-control-sm">
                            <option value="">Inscripción: Todos</option>
                            <option value="1" {{ request('inscripcion_activa') === '1' ? 'selected' : '' }}>Con inscripción activa</option>
                            <option value="0" {{ request('inscripcion_activa') === '0' ? 'selected' : '' }}>Sin inscripción activa</option>
                        </select>
                    </div>
                    <div class="col-auto mb-2">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('coordinador.alumnos') }}" class="btn btn-light btn-sm ml-1">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>No. Control</th>
                                <th>Nombre Completo</th>
                                <th>Carrera</th>
                                <th>Semestre Cursando</th>
                                <th>Inscripción Activa</th>
                                <th>Créditos Acumulados</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alumnos as $alumno)
                            @php
                                $inscActiva = $alumno->inscripciones
                                    ->whereIn('estatus', ['inscrito', 'cursando'])
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <code>{{ $alumno->usuario->num_control ?? '—' }}</code>
                                </td>
                                <td>
                                    <strong>{{ $alumno->usuario->nombre_completo ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $alumno->carrera->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $alumno->semestre_cursando ? $alumno->semestre_cursando . '°' : '—' }}
                                </td>
                                <td>
                                    @if($inscActiva)
                                        <span class="badge badge-success">
                                            {{ $inscActiva->grupo->actividad->nombre ?? 'N/A' }}
                                        </span>
                                        <small class="text-muted d-block">
                                            Grupo {{ $inscActiva->grupo->grupo ?? '' }}
                                            — {{ ucfirst($inscActiva->estatus) }}
                                        </small>
                                    @else
                                        <span class="badge badge-secondary">Sin inscripción activa</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $alumno->creditos_acumulados > 0 ? 'primary' : 'light' }}">
                                        {{ $alumno->creditos_acumulados ?? 0 }} crédito(s)
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-graduate fa-2x mb-2 d-block"></i>
                                    No hay alumnos registrados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($alumnos->hasPages())
                    <div class="card-footer">{{ $alumnos->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection
