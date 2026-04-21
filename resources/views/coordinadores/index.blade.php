@extends('layouts.app')
@section('title', 'Panel de Coordinadores')

@section('content')
<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h3 class="page__heading">
            <i class="fas fa-user-tie mr-2"></i>Panel de Coordinadores
        </h3>
    </div>

    <div class="section-body">

        {{-- Notificaciones --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="row">

            {{-- ════════════════════════════════════════════════
                 Columna izquierda: Formulario de asignación
            ════════════════════════════════════════════════ --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle text-primary mr-1"></i>
                            Asignar Coordinador
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            Selecciona un departamento y el coordinador que lo gestionará.
                            Solo puede haber <strong>un coordinador por departamento</strong>.
                        </p>

                        <form action="{{ route('admin.coordinadores.asignar') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>Departamento <span class="text-danger">*</span></label>
                                <select name="id_departamento" class="form-control" required>
                                    <option value="">-- Selecciona un departamento --</option>
                                    @foreach($departamentos as $dep)
                                        <option value="{{ $dep->id_departamento }}"
                                            {{ old('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                            {{ $dep->nombre }}
                                            @if($dep->coordinadorDepartamento)
                                                — (ya asignado)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    Los que muestran "(ya asignado)" reemplazarán al coordinador actual.
                                </small>
                            </div>

                            <div class="form-group">
                                <label>Coordinador <span class="text-danger">*</span></label>
                                <select name="id_usuario" class="form-control" required>
                                    <option value="">-- Selecciona un coordinador --</option>
                                    @foreach($coordinadores as $coord)
                                        <option value="{{ $coord->id }}"
                                            {{ old('id_usuario') == $coord->id ? 'selected' : '' }}>
                                            {{ $coord->nombre }} {{ $coord->apellido_paterno }}
                                            {{ $coord->apellido_materno }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($coordinadores->isEmpty())
                                    <small class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No hay usuarios con rol "coordinador" activos.
                                        <a href="{{ route('usuarios.create') }}">Crear uno aquí.</a>
                                    </small>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary btn-block"
                                {{ $coordinadores->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-link mr-1"></i> Asignar Coordinador
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Estadísticas rápidas --}}
                <div class="card bg-light">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Departamentos con coordinador</span>
                            <strong class="text-success">
                                {{ $departamentos->whereNotNull('coordinadorDepartamento')->count() }}
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Departamentos sin coordinador</span>
                            <strong class="text-warning">
                                {{ $departamentos->whereNull('coordinadorDepartamento')->count() }}
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Total coordinadores activos</span>
                            <strong class="text-primary">{{ $coordinadores->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════
                 Columna derecha: Tabla de asignaciones actuales
            ════════════════════════════════════════════════ --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-table text-secondary mr-1"></i>
                            Asignaciones por Departamento
                        </h4>
                        <span class="badge badge-primary badge-pill">
                            {{ $departamentos->whereNotNull('coordinadorDepartamento')->count() }}
                            / {{ $departamentos->count() }} asignados
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Departamento</th>
                                        <th>Coordinador Asignado</th>
                                        <th>Email</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($departamentos as $dep)
                                    <tr class="{{ $dep->coordinadorDepartamento ? '' : 'table-warning' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $dep->nombre }}</strong>
                                            @if($dep->edificio)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-building"></i> {{ $dep->edificio }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dep->coordinadorDepartamento && $dep->coordinadorDepartamento->usuario)
                                                @php $coord = $dep->coordinadorDepartamento->usuario @endphp
                                                <span class="badge badge-success mr-1">
                                                    <i class="fas fa-user-check"></i>
                                                </span>
                                                {{ $coord->nombre }} {{ $coord->apellido_paterno }}
                                                {{ $coord->apellido_materno }}
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-user-slash"></i> Sin asignar
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dep->coordinadorDepartamento && $dep->coordinadorDepartamento->usuario)
                                                <small>{{ $dep->coordinadorDepartamento->usuario->email }}</small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($dep->coordinadorDepartamento)
                                                <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    title="Quitar coordinador de este departamento"
                                                    onclick="confirmarQuitar(
                                                        '{{ $dep->nombre }}',
                                                        '{{ $dep->id_departamento }}'
                                                    )">
                                                    <i class="fas fa-unlink"></i> Quitar
                                                </button>

                                                <form id="quitar-form-{{ $dep->id_departamento }}"
                                                      action="{{ route('admin.coordinadores.quitar', $dep->id_departamento) }}"
                                                      method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox mr-1"></i>
                                            No hay departamentos registrados.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Regla del sistema:</strong>
                    Cada departamento tiene <strong>un único coordinador</strong>.
                    Al asignar uno nuevo en un departamento que ya tiene, se reemplaza automáticamente.
                    Un coordinador tampoco puede gestionar más de un departamento a la vez.
                    Las filas en amarillo indican departamentos sin coordinador asignado.
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    function confirmarQuitar(depto, id) {
        Swal.fire({
            title: '¿Quitar coordinador?',
            html: `Se removerá al coordinador del departamento<br><strong>${depto}</strong>.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('quitar-form-' + id).submit();
            }
        });
    }

    @if(Session::has('success'))
        iziToast.success({
            title: 'Éxito',
            message: '{{ Session::get("success") }}',
            position: 'topRight'
        });
    @endif

    @if(Session::has('error'))
        iziToast.error({
            title: 'Error',
            message: '{{ Session::get("error") }}',
            position: 'topRight'
        });
    @endif
</script>
@endsection
