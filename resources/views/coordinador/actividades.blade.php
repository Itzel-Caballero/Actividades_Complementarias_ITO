@extends('layouts.app')
@section('title', 'Actividades Complementarias')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Gestión de Actividades</h3>
    </div>
    <div class="section-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

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
                        <select name="disponible" class="form-control form-control-sm">
                            <option value="">Disponibilidad: Todas</option>
                            <option value="1" {{ request('disponible') === '1' ? 'selected' : '' }}>Disponible</option>
                            <option value="0" {{ request('disponible') === '0' ? 'selected' : '' }}>No disponible</option>
                        </select>
                    </div>
                    <div class="col-auto mb-2">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('coordinador.actividades') }}" class="btn btn-light btn-sm ml-1">Limpiar</a>
                    </div>
                    <div class="col-auto mb-2 ml-auto">
                        <a href="{{ route('actividades.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Nueva Actividad
                        </a>
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
                                <th>Nombre</th>
                                <th>Departamento</th>
                                <th>Créditos</th>
                                <th>Nivel</th>
                                <th>Disponible</th>
                                <th>Grupos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($actividades as $actividad)
                            <tr>
                                <td>{{ $actividades->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $actividad->nombre }}</strong>
                                    @if($actividad->requisitos)
                                        <br><small class="text-muted">{{ Str::limit($actividad->requisitos, 60) }}</small>
                                    @endif
                                </td>
                                <td>{{ $actividad->departamento->nombre ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $actividad->creditos == 2 ? 'success' : 'info' }}">
                                        {{ $actividad->creditos }} crédito(s)
                                    </span>
                                </td>
                                <td>{{ $actividad->nivel_actividad ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-{{ $actividad->disponible ? 'success' : 'danger' }}">
                                        {{ $actividad->disponible ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $actividad->grupos->count() }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('actividades.edit', $actividad->id_actividad) }}"
                                       class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                                    <form action="{{ route('actividades.destroy', $actividad->id_actividad) }}"
                                          method="POST" style="display:inline-block"
                                          onsubmit="return confirm('¿Eliminar esta actividad?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No hay actividades registradas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($actividades->hasPages())
                    <div class="card-footer">{{ $actividades->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection