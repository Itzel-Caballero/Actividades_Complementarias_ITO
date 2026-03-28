@extends('layouts.app')
@section('title', 'Docentes')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Gestión de Docentes</h3>
    </div>
    <div class="section-body">

        {{-- Filtros --}}
        <div class="card">
            <div class="card-body py-2">
                <form method="GET" class="form-row align-items-end">
                    <div class="col-auto mb-2">
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                               class="form-control form-control-sm" placeholder="Buscar por nombre...">
                    </div>
                    <div class="col-auto mb-2">
                        <select name="id_departamento" class="form-control form-control-sm">
                            <option value="">Todos los departamentos</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}"
                                    {{ request('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                    {{ $dep->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto mb-2">
                        <input type="text" name="especialidad" value="{{ request('especialidad') }}"
                               class="form-control form-control-sm" placeholder="Especialidad...">
                    </div>
                    <div class="col-auto mb-2">
                        <select name="id_actividad" class="form-control form-control-sm">
                            <option value="">Todas las actividades</option>
                            @foreach($actividades as $act)
                                <option value="{{ $act->id_actividad }}"
                                    {{ request('id_actividad') == $act->id_actividad ? 'selected' : '' }}>
                                    {{ $act->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto mb-2">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('coordinador.docentes') }}" class="btn btn-light btn-sm ml-1">Limpiar</a>
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
                                <th>Nombre Completo</th>
                                <th>Correo</th>
                                <th>Departamento</th>
                                <th>Especialidad</th>
                                <th>Grupos Asignados</th>
                                <th>Actividades</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($instructores as $ins)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $ins->usuario->nombre_completo ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $ins->usuario->email ?? '—' }}</small>
                                </td>
                                <td>{{ $ins->departamento->nombre ?? 'N/A' }}</td>
                                <td>{{ $ins->especialidad ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $ins->grupos->count() }}
                                    </span>
                                </td>
                                <td>
                                    @foreach($ins->grupos->unique('id_actividad')->take(3) as $g)
                                        <span class="badge badge-info" style="font-size:10px;">
                                            {{ $g->actividad->nombre ?? '' }}
                                        </span>
                                    @endforeach
                                    @if($ins->grupos->unique('id_actividad')->count() > 3)
                                        <small class="text-muted">
                                            +{{ $ins->grupos->unique('id_actividad')->count() - 3 }} más
                                        </small>
                                    @endif
                                    @if($ins->grupos->isEmpty())
                                        <small class="text-muted">Sin grupos</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-chalkboard-teacher fa-2x mb-2 d-block"></i>
                                    No hay docentes registrados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($instructores->hasPages())
                    <div class="card-footer">{{ $instructores->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection
