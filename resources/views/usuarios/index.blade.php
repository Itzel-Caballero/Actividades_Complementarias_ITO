@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="page__heading">Gestión de Usuarios</h3>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="fa fa-user-plus"></i> Nuevo Usuario
        </a>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <h4>Listado General</h4>
                        <form action="{{ route('usuarios.index') }}" method="GET" class="form-inline">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-sm" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <input type="text" name="buscar" class="form-control form-control-sm"
                                       placeholder="Buscar por nombre, email, N° control..."
                                       value="{{ $buscar ?? '' }}">
                                 
                                <select name="tipo_usuario" class="form-control form-control-sm">
                                    <option value="">Todos los tipos</option>
                                    <option value="alumno"      {{ $tipo_usuario == 'alumno'      ? 'selected' : '' }}>Alumno</option>
                                    <option value="instructor"  {{ $tipo_usuario == 'instructor'  ? 'selected' : '' }}>Instructor</option>
                                    <option value="coordinador" {{ $tipo_usuario == 'coordinador' ? 'selected' : '' }}>Coordinador</option>
                                </select>
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fa fa-filter"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.reportes.alumnos') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-times"></i> Limpiar
                            </a>
                               
                            </div>
                        </form>
                    </div> 
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mt-3">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre Completo</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($usuarios as $usuario)
                                    <tr class="{{ $usuario->activo ? '' : 'table-secondary text-muted' }}">
                                        <td>{{ ($usuarios->currentPage() - 1) * $usuarios->perPage() + $loop->iteration }}</td>
                                        <td>
                                            {{ $usuario->nombre }} {{ $usuario->apellido_paterno }}
                                            {{ $usuario->apellido_materno }}
                                            @if(!$usuario->activo)
                                                <span class="badge badge-danger ml-1">Deshabilitado</span>
                                            @endif
                                        </td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>
                                            <span class="badge badge-light border">
                                                {{ ucfirst($usuario->tipo_usuario ?? '—') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($usuario->activo)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Activo
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-ban"></i> Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                {{-- Editar --}}
                                                <a href="{{ route('usuarios.edit', $usuario->id) }}"
                                                   class="btn btn-warning btn-sm" title="Editar usuario">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                {{-- Deshabilitar / Habilitar --}}
                                                @if($usuario->activo)
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm"
                                                        title="Deshabilitar usuario"
                                                        onclick="alertaToggle(
                                                            '{{ $usuario->nombre }} {{ $usuario->apellido_paterno }}',
                                                            '{{ $usuario->id }}',
                                                            false
                                                        )">
                                                        <i class="fas fa-user-slash"></i> Deshabilitar
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-success btn-sm"
                                                        title="Habilitar usuario"
                                                        onclick="alertaToggle(
                                                            '{{ $usuario->nombre }} {{ $usuario->apellido_paterno }}',
                                                            '{{ $usuario->id }}',
                                                            true
                                                        )">
                                                        <i class="fas fa-user-check"></i> Habilitar
                                                    </button>
                                                @endif

                                                {{-- Form oculto para toggle --}}
                                                <form id="toggle-form-{{ $usuario->id }}"
                                                      action="{{ route('usuarios.toggle', $usuario->id) }}"
                                                      method="POST" style="display:none;">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-search mr-1"></i>
                                            No hay registros que coincidan con la búsqueda.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            {!! $usuarios->appends(['buscar' => $buscar ?? '', 'tipo_usuario' => $tipo_usuario ?? ''])->links() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    @if(Session::has('success'))
        iziToast.success({
            title: 'Éxito',
            message: '{{ Session::get("success") }}',
            position: 'topRight'
        });
    @endif

    function alertaToggle(nombre, id, activar) {
        const accion  = activar ? 'habilitar' : 'deshabilitar';
        const icono   = activar ? 'success'   : 'warning';
        const btnColor = activar ? '#28a745'  : '#e3342f';

        Swal.fire({
            title: '¿Confirmar acción?',
            html: `Se va a <strong>${accion}</strong> al usuario <strong>${nombre}</strong>.
                   <br><small class="text-muted">El usuario ${activar ? 'podrá' : 'no podrá'} iniciar sesión en el sistema.</small>`,
            icon: icono,
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Sí, ${accion}`,
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('toggle-form-' + id).submit();
            }
        });
    }
</script>
@endsection
