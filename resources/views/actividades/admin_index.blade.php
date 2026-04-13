@extends('layouts.app')
@section('title', 'Gestión de Actividades')

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

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Actividades Complementarias</h4>
                <div>
                    <a href="{{ route('actividades.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Nueva Actividad
                    </a>
                </div>
            </div>
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
                            @forelse ($actividades as $actividad)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $actividad->nombre }}</strong></td>
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
                                <td class="text-nowrap">
                                    <a href="{{ route('actividades.edit', $actividad->id_actividad) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmarEliminar({{ $actividad->id_actividad }}, '{{ addslashes($actividad->nombre) }}', {{ $actividad->grupos->count() }})">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </button>
                                    <form id="form-delete-{{ $actividad->id_actividad }}"
                                          action="{{ route('actividades.destroy', $actividad->id_actividad) }}"
                                          method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No hay actividades registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($actividades->hasPages())
                    <div class="card-footer">{!! $actividades->links() !!}</div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection

@section('scripts')
<script>
function confirmarEliminar(id, nombre, cantGrupos) {
    let mensaje = '¿Estás seguro de eliminar la actividad <strong>' + nombre + '</strong>?';
    if (cantGrupos > 0) {
        mensaje += '<br><br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Esta actividad tiene <strong>' + cantGrupos + ' grupo(s)</strong> asociados. Se eliminarán también sus horarios e inscripciones.</span>';
    }

    swal({
        title: 'Eliminar actividad',
        content: (function() {
            const div = document.createElement('div');
            div.innerHTML = mensaje;
            return div;
        })(),
        icon: 'warning',
        buttons: {
            cancel: {
                text: 'Cancelar',
                visible: true,
                className: 'btn btn-secondary',
            },
            confirm: {
                text: 'Sí, eliminar',
                className: 'btn btn-danger',
            }
        },
        dangerMode: true,
    }).then(function(confirmar) {
        if (confirmar) {
            document.getElementById('form-delete-' + id).submit();
        }
    });
}
</script>
@endsection
