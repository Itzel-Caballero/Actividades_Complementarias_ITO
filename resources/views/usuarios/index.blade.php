@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="page__heading">Gestión de Usuarios / Alumnos</h3>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="fa fa-user-plus"></i> Inscribir Alumno
        </a>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-lg-12">
                {{-- El mensaje de éxito ahora se maneja por JS al final --}}
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <h4>Listado General</h4>
                        <form action="{{ route('usuarios.index') }}" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar..." value="{{ $buscar ?? '' }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-search"></i></button>
                                </div>
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
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($usuarios as $usuario)
                                    <tr>
                                        <td>{{ ($usuarios->currentPage() - 1) * $usuarios->perPage() + $loop->iteration }}</td>
                                        <td>{{ $usuario->nombre }} {{ $usuario->apellido_paterno }}</td>
                                        <td>{{ $usuario->email }}</td>
                                        <td class="text-center">
                                            @if($usuario->hasRole('Alumno'))
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-info">Personal</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="alertaEliminar('{{ $usuario->nombre }}', '{{ $usuario->id }}')"
                                                    title="Dar de baja">
                                                    <i class="fa fa-trash"></i> Baja
                                                </button>

                                                <form id="delete-form-{{ $usuario->id }}" action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No hay registros que coincidan con la búsqueda.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {!! $usuarios->appends(['buscar' => $buscar ?? ''])->links() !!}
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
    // 1. Mostrar notificación de éxito si existe en la sesión
    @if(Session::has('success'))
        iziToast.success({
            title: 'Éxito',
            message: '{{ Session::get("success") }}',
            position: 'topRight'
        });
    @endif

    // 2. Función para confirmar la baja
    function alertaEliminar(nombre, id) {
        Swal.fire({
            title: '¿Confirmar baja?',
            text: "El alumno " + nombre + " será eliminado permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar cuenta',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection