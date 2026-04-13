@extends('layouts.app')
@section('title', 'Docentes')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Gestión de Docentes</h3>
    </div>
    <div class="section-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Filtros instantáneos --}}
        <div class="card">
            <div class="card-body py-2">
                <form method="GET" id="form-filtros-doc" class="form-row align-items-end">
                    <div class="col-12 col-md-3 mb-2">
                        <input type="text" name="buscar" id="buscar-doc" value="{{ request('buscar') }}"
                               class="form-control form-control-sm" placeholder="Buscar por nombre...">
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <select name="id_departamento" id="id_departamento_doc" class="form-control form-control-sm">
                            <option value="">Todos los departamentos</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}"
                                    {{ request('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                    {{ $dep->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <select name="id_actividad" id="id_actividad_doc" class="form-control form-control-sm">
                            <option value="">Todas las actividades</option>
                            @foreach($actividades as $act)
                                <option value="{{ $act->id_actividad }}"
                                    {{ request('id_actividad') == $act->id_actividad ? 'selected' : '' }}>
                                    {{ $act->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                

                    <div class="col-6 col-md-2 mb-2">
                        <input type="text" name="especialidad" id="especialidad_doc" value="{{ request('especialidad') }}"
                               class="form-control form-control-sm" placeholder="Especialidad...">
                    </div>
                    <div class="col-6 col-md-1 mb-2">
                        <a href="{{ route('coordinador.docentes') }}" class="btn btn-light btn-sm btn-block">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Nombre Completo</th>
                                <th>Correo</th>
                                <th>Departamento</th>
                                <th>Especialidad</th>
                                <th>Grupos Asignados</th>
                                <th>Actividades</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($instructores as $ins)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $ins->usuario->nombre_completo ?? 'N/A' }}</strong></td>
                                <td><small class="text-muted">{{ $ins->usuario->email ?? '—' }}</small></td>
                                <td>{{ $ins->departamento->nombre ?? 'N/A' }}</td>
                                <td>{{ $ins->especialidad ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $ins->grupos->count() }}</span>
                                </td>
                                <td>
                                    @foreach($ins->grupos->unique('id_actividad')->take(3) as $g)
                                        <span class="badge badge-info" style="font-size:10px;">
                                            {{ $g->actividad->nombre ?? '' }}
                                        </span>
                                    @endforeach
                                    @if($ins->grupos->unique('id_actividad')->count() > 3)
                                        <small class="text-muted">+{{ $ins->grupos->unique('id_actividad')->count() - 3 }} más</small>
                                    @endif
                                    @if($ins->grupos->isEmpty())
                                        <small class="text-muted">Sin grupos</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-chalkboard-teacher fa-2x mb-2 d-block"></i>
                                    No hay docentes con los filtros seleccionados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($instructores->hasPages())
                    <div class="card-footer">{{ $instructores->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection

@section('scripts')
<script>
// Filtros que envían el form al instante al cambiar
['id_departamento_doc', 'id_actividad_doc'].forEach(function(id) {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', function() {
        document.getElementById('form-filtros-doc').submit();
    });
});

// Búsqueda y especialidad con debounce
let debounceTimer;
['buscar-doc', 'especialidad_doc'].forEach(function(id) {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            document.getElementById('form-filtros-doc').submit();
        }, 600);
    });
});
</script>
@endsection
