@extends('layouts.app')
@section('title', 'Grupos y Horarios')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Grupos y Horarios</h3>
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
                <form method="GET" class="form-inline flex-wrap" style="gap: 8px;">
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           class="form-control form-control-sm" placeholder="Buscar actividad...">

                    <select name="id_departamento" class="form-control form-control-sm">
                        <option value="">Todos los departamentos</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}"
                                {{ request('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                {{ $dep->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <select name="estatus" class="form-control form-control-sm">
                        <option value="">Todos los estatus</option>
                        <option value="abierta"    {{ request('estatus') == 'abierta'    ? 'selected' : '' }}>Abierta</option>
                        <option value="cerrada"    {{ request('estatus') == 'cerrada'    ? 'selected' : '' }}>Cerrada</option>
                        <option value="cancelada"  {{ request('estatus') == 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
                        <option value="finalizada" {{ request('estatus') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                    </select>

                    <button type="submit" class="btn btn-secondary btn-sm">
                        <i class="fa fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('coordinador.grupos') }}" class="btn btn-light btn-sm">Limpiar</a>

                    <a href="{{ route('coordinador.grupos.create') }}" class="btn btn-primary btn-sm ml-auto">
                        <i class="fa fa-plus"></i> Nuevo Grupo
                    </a>
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
                                <th>Actividad</th>
                                <th>Grupo</th>
                                <th>Departamento</th>
                                <th>Docente</th>
                                <th>Horario</th>
                                <th>Cupo</th>
                                <th>Carreras</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grupos as $grupo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $grupo->actividad->nombre ?? 'N/A' }}</strong></td>
                                <td><span class="badge badge-primary">{{ $grupo->grupo }}</span></td>
                                <td><small>{{ $grupo->actividad->departamento->nombre ?? 'N/A' }}</small></td>
                                <td>
                                    @if($grupo->instructor)
                                        {{ $grupo->instructor->usuario->nombre_completo ?? 'N/A' }}
                                    @else
                                        <span class="badge badge-warning">Sin asignar</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($grupo->horarios as $h)
                                        <small class="d-block text-nowrap">
                                            <i class="fa fa-clock text-muted"></i>
                                            {{ $h->dia->nombre_dia ?? '' }}
                                            {{ substr($h->hora_inicio, 0, 5) }}–{{ substr($h->hora_fin, 0, 5) }}
                                        </small>
                                    @endforeach
                                    @if($grupo->horarios->isEmpty())
                                        <small class="text-muted">Sin horario</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $grupo->cupo_ocupado >= $grupo->cupo_maximo ? 'danger' : 'success' }}">
                                        {{ $grupo->cupo_ocupado }}/{{ $grupo->cupo_maximo }}
                                    </span>
                                </td>
                                <td>
                                    @foreach($grupo->actividad->carreras->take(2) as $c)
                                        <span class="badge badge-info" style="font-size:10px;">{{ $c->nombre }}</span>
                                    @endforeach
                                    @if($grupo->actividad->carreras->count() > 2)
                                        <small class="text-muted">+{{ $grupo->actividad->carreras->count() - 2 }} más</small>
                                    @endif
                                    @if($grupo->actividad->carreras->isEmpty())
                                        <small class="text-muted">Todas</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $grupo->estatus == 'abierta' ? 'success' : ($grupo->estatus == 'cancelada' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($grupo->estatus) }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('coordinador.grupos.edit', $grupo->id_grupo) }}"
                                       class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('coordinador.grupos.destroy', $grupo->id_grupo) }}"
                                          method="POST" style="display:inline-block"
                                          onsubmit="return confirm('¿Eliminar el grupo {{ $grupo->grupo }}? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-layer-group fa-2x mb-2 d-block"></i>
                                    No hay grupos registrados.
                                    <br>
                                    <a href="{{ route('coordinador.grupos.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fa fa-plus"></i> Crear primer grupo
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($grupos->hasPages())
                    <div class="card-footer">{{ $grupos->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection
