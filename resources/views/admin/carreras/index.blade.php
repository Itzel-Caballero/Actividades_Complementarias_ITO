@extends('layouts.app')

@section('title', 'Carreras')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Catálogo de Carreras</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-12">

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong>{{ $message }}</strong>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>{{ $message }}</strong>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4>Listado de Carreras</h4>
                        <div class="d-flex align-items-center">
                            <form action="{{ route('admin.carreras.index') }}" method="GET" class="form-inline mr-3">
                                <div class="input-group">
                                    <input type="text" name="buscar" class="form-control form-control-sm"
                                           placeholder="Buscar carrera..." value="{{ $buscar }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <a href="{{ route('admin.carreras.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Nueva Carrera
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre de la Carrera</th>
                                        <th class="text-center">Alumnos</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($carreras as $carrera)
                                    <tr>
                                        <td>{{ ($carreras->currentPage() - 1) * $carreras->perPage() + $loop->iteration }}</td>
                                        <td>{{ $carrera->nombre }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-info">{{ $carrera->alumnos_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.carreras.edit', $carrera->id_carrera) }}"
                                               class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.carreras.destroy', $carrera->id_carrera) }}"
                                                  method="POST" style="display:inline-block"
                                                  onsubmit="return confirm('¿Eliminar esta carrera? Solo es posible si no tiene alumnos.')">
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
                                        <td colspan="4" class="text-center text-muted">No hay carreras registradas.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {!! $carreras->appends(['buscar' => $buscar])->links() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
