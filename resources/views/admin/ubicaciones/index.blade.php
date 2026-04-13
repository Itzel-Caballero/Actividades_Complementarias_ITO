@extends('layouts.app')

@section('title', 'Ubicaciones')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Control de Ubicaciones</h3>
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
                        <h4>Aulas, Auditorios y Canchas</h4>
                        <div class="d-flex align-items-center">
                            <form action="{{ route('admin.ubicaciones.index') }}" method="GET" class="form-inline mr-3">
                                <div class="input-group">
                                    <input type="text" name="buscar" class="form-control form-control-sm"
                                           placeholder="Buscar espacio..." value="{{ $buscar }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <a href="{{ route('admin.ubicaciones.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Nueva Ubicación
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Espacio / Nombre</th>
                                        <th class="text-center">Capacidad Máx.</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ubicaciones as $ub)
                                    <tr>
                                        <td>{{ ($ubicaciones->currentPage() - 1) * $ubicaciones->perPage() + $loop->iteration }}</td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-primary mr-1"></i>
                                            {{ $ub->espacio }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary">{{ $ub->capacidad }} personas</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.ubicaciones.edit', $ub->id_ubicacion) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.ubicaciones.destroy', $ub->id_ubicacion) }}"
                                                  method="POST" style="display:inline-block"
                                                  onsubmit="return confirm('¿Eliminar esta ubicación?')">
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
                                        <td colspan="4" class="text-center text-muted">No hay ubicaciones registradas.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {!! $ubicaciones->appends(['buscar' => $buscar])->links() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
