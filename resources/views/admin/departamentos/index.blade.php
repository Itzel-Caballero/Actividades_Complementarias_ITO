@extends('layouts.app')

@section('title', 'Departamentos')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Departamentos</h3>
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
                        <h4>Listado de Departamentos</h4>
                        <div class="d-flex align-items-center">
                            <form action="{{ route('admin.departamentos.index') }}" method="GET" class="form-inline mr-3">
                                <div class="input-group">
                                    <input type="text" name="buscar" class="form-control form-control-sm"
                                           placeholder="Nombre o edificio..." value="{{ $buscar }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <a href="{{ route('admin.departamentos.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Nuevo Departamento
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre del Departamento</th>
                                        <th>Edificio</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departamentos as $dep)
                                    <tr>
                                        <td>{{ ($departamentos->currentPage() - 1) * $departamentos->perPage() + $loop->iteration }}</td>
                                        <td>{{ $dep->nombre }}</td>
                                        <td>{{ $dep->edificio ?? '—' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.departamentos.edit', $dep->id_departamento) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.departamentos.destroy', $dep->id_departamento) }}"
                                                  method="POST" style="display:inline-block"
                                                  onsubmit="return confirm('¿Eliminar este departamento?')">
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
                                        <td colspan="4" class="text-center text-muted">No hay departamentos registrados.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {!! $departamentos->appends(['buscar' => $buscar])->links() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
