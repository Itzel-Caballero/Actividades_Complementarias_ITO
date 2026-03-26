@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Gestión de Actividades</h3>
    </div>
    <div class="section-body">

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ $message }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Actividades Complementarias</h4>
                <div>
                    <a href="{{ route('grupos.index') }}" class="btn btn-secondary btn-sm mr-2">
                        <i class="fa fa-users"></i> Gestión de Grupos
                    </a>
                    <a href="{{ route('actividades.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Nueva Actividad
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
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
                            @forelse ($actividades as $actividad)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $actividad->nombre }}</td>
                                <td>{{ $actividad->departamento->nombre ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $actividad->creditos == 2 ? 'success' : 'info' }}">
                                        {{ $actividad->creditos }}
                                    </span>
                                </td>
                                <td>{{ $actividad->nivel_actividad ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $actividad->disponible ? 'success' : 'danger' }}">
                                        {{ $actividad->disponible ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td>{{ $actividad->grupos->count() }}</td>
                                <td>
                                    <a href="{{ route('actividades.edit', $actividad->id_actividad) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('actividades.destroy', $actividad->id_actividad) }}"
                                          method="POST" style="display:inline-block"
                                          onsubmit="return confirm('¿Eliminar esta actividad?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay actividades registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {!! $actividades->links() !!}
            </div>
        </div>

    </div>
</section>
@endsection