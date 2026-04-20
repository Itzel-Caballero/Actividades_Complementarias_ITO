@extends('layouts.app')

@section('title', 'Padrón de Alumnos')

@section('content')
<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="page__heading">Padrón de Alumnos</h3>
        <a href="{{ route('admin.alumnos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Inscribir Alumno
        </a>
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
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Núm. Control</th>
                                        <th>Nombre Completo</th>
                                        <th>Carrera</th>
                                        <th class="text-center">Semestre</th>
                                        <th class="text-center">Créditos</th>
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($alumnos as $alumno)
                                    <tr>
                                        <td>{{ ($alumnos->currentPage() - 1) * $alumnos->perPage() + $loop->iteration }}</td>
                                        <td><code>{{ $alumno->usuario->num_control ?? '—' }}</code></td>
                                        <td>{{ $alumno->usuario->nombre_completo ?? '—' }}</td>
                                        <td>{{ $alumno->carrera->nombre ?? '—' }}</td>
                                        <td class="text-center">{{ $alumno->semestre_cursando }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $alumno->creditos_acumulados >= 3 ? 'success' : 'secondary' }}">
                                                {{ $alumno->creditos_acumulados }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($alumno->inscripciones_count > 0 || (isset($alumno->grupos) && $alumno->grupos->count() > 0))
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-warning">No Inscrito</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <div class="btn-group">
                                                {{-- BOTÓN DE BAJA ACTUALIZADO --}}
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="confirmarBaja('{{ $alumno->usuario->nombre_completo ?? 'este alumno' }}', '{{ $alumno->id_alumno }}')"
                                                        title="Dar de baja al alumno">
                                                    <i class="fas fa-user-times"></i> Baja
                                                </button>

                                                <form id="delete-form-{{ $alumno->id_alumno }}" 
                                                      action="{{ route('admin.alumnos.destroy', $alumno->id_alumno) }}" 
                                                      method="POST" 
                                                      style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No se encontraron alumnos.</td>
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

@section('scripts')
<script>
    // Notificación de éxito con iziToast
    @if(session('success'))
        iziToast.success({
            title: 'Éxito',
            message: '{{ session("success") }}',
            position: 'topRight'
        });
    @endif

    // Función SweetAlert2 para el Padrón
    function confirmarBaja(nombre, id) {
        Swal.fire({
            title: '¿Confirmar baja del padrón?',
            text: "Vas a eliminar a " + nombre + ". Esta acción borrará su registro de forma permanente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar registro',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection

@endsection