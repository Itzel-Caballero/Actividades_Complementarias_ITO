@extends('layouts.app')

@section('title', 'Padrón de Alumnos')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Padrón de Alumnos</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-12">

                {{-- Filtros --}}
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <form action="{{ route('admin.reportes.alumnos') }}" method="GET"
                              class="form-inline flex-wrap" style="gap:8px">
                            <input type="text" name="buscar" class="form-control form-control-sm"
                                   placeholder="Nombre, control o email..." value="{{ $buscar }}">
                            <select name="id_carrera" class="form-control form-control-sm">
                                <option value="">Todas las carreras</option>
                                @foreach ($carreras as $c)
                                    <option value="{{ $c->id_carrera }}"
                                        {{ $id_carrera == $c->id_carrera ? 'selected' : '' }}>
                                        {{ $c->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fa fa-filter"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.reportes.alumnos') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-times"></i> Limpiar
                            </a>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>
                            <i class="fas fa-user-graduate mr-2 text-primary"></i>
                            Listado de Alumnos
                            <span class="badge badge-primary ml-2">{{ $alumnos->total() }}</span>
                        </h4>
                        <small class="text-muted"><i class="fas fa-lock mr-1"></i>Vista de solo lectura</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Núm. Control</th>
                                        <th>Nombre Completo</th>
                                        <th>Email</th>
                                        <th>Carrera</th>
                                        <th class="text-center">Semestre</th>
                                        <th class="text-center">Créditos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($alumnos as $alumno)
                                    <tr>
                                        <td>{{ ($alumnos->currentPage() - 1) * $alumnos->perPage() + $loop->iteration }}</td>
                                        <td><code>{{ $alumno->usuario->num_control ?? '—' }}</code></td>
                                        <td>{{ $alumno->usuario->nombre_completo ?? '—' }}</td>
                                        <td>{{ $alumno->usuario->email ?? '—' }}</td>
                                        <td>{{ $alumno->carrera->nombre ?? '—' }}</td>
                                        <td class="text-center">{{ $alumno->semestre_cursando }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $alumno->creditos_acumulados >= 3 ? 'success' : 'secondary' }}">
                                                {{ $alumno->creditos_acumulados }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No se encontraron alumnos.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {!! $alumnos->appends(request()->query())->links() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
