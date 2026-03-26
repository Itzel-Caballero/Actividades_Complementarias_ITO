@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Usuarios</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-12">

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ $message }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <h4>Listado de Usuarios</h4>
                        
                        {{-- BLOQUE DE BÚSQUEDA --}}
                        <div class="d-flex align-items-center">
                            <form action="{{ route('usuarios.index') }}" method="GET" class="form-inline mr-3">
                                <div class="input-group">
                                    <input type="text" name="buscar" class="form-control form-control-sm" 
                                           placeholder="Nombre, email o control..." value="{{ $buscar ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Nuevo Usuario
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mt-3">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($usuarios as $usuario)
                                    <tr>
                                        {{-- Usamos el ID real o el índice de paginación --}}
                                        <td>{{ ($usuarios->currentPage() - 1) * $usuarios->perPage() + $loop->iteration }}</td>
                                        <td>{{ $usuario->nombre }} {{ $usuario->apellido_paterno }}</td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>
                                            @foreach ($usuario->getRoleNames() as $rol)
                                                <span class="badge badge-primary">{{ $rol }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display:inline-block"
                                                onsubmit="return confirm('¿Eliminar este usuario?')">
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
                                        <td colspan="5" class="text-center">No hay usuarios que coincidan con la búsqueda.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- BLOQUE DE PAGINACIÓN --}}
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