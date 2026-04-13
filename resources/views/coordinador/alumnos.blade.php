@extends('layouts.app')
@section('title', 'Alumnos Inscritos')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Alumnos Inscritos</h3>
    </div>
    <div class="section-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Filtros (se aplican al instante con JS) --}}
        <div class="card">
            <div class="card-body py-2">
                <form method="GET" id="form-filtros" class="form-row align-items-end">
                    <div class="col-12 col-md-3 mb-2">
                        <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}"
                               class="form-control form-control-sm"
                               placeholder="Nombre o No. de control...">
                    </div>
                    <div class="col-6 col-md-2 mb-2">
                        <select name="id_carrera" id="id_carrera" class="form-control form-control-sm">
                            <option value="">Todas las carreras</option>
                            @foreach($carreras as $car)
                                <option value="{{ $car->id_carrera }}"
                                    {{ request('id_carrera') == $car->id_carrera ? 'selected' : '' }}>
                                    {{ $car->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2 mb-2">
                        <select name="id_departamento" id="id_departamento" class="form-control form-control-sm">
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
                        <select name="id_actividad" id="id_actividad" class="form-control form-control-sm">
                            <option value="">Todas las actividades</option>
                            @foreach($actividades as $act)
                                <option value="{{ $act->id_actividad }}"
                                    {{ request('id_actividad') == $act->id_actividad ? 'selected' : '' }}>
                                    {{ $act->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2 mb-2 d-flex gap-1">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('coordinador.alumnos') }}" class="btn btn-light btn-sm ml-1">
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
                                <th>No. Control</th>
                                <th>Nombre</th>
                                <th>Carrera</th>
                                <th>Actividad Inscrita</th>
                                <th>Grupo</th>
                                <th>Departamento</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alumnos as $alumno)
                            @php
                                $inscActiva = $alumno->inscripciones
                                    ->whereIn('estatus', ['inscrito', 'cursando'])
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><code>{{ $alumno->usuario->num_control ?? '—' }}</code></td>
                                <td><strong>{{ $alumno->usuario->nombre_completo ?? 'N/A' }}</strong></td>
                                <td>
                                    <span class="badge badge-info" style="font-size:11px;">
                                        {{ $alumno->carrera->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($inscActiva)
                                        <strong>{{ $inscActiva->grupo->actividad->nombre ?? 'N/A' }}</strong>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($inscActiva)
                                        <span class="badge badge-primary">{{ $inscActiva->grupo->grupo ?? '' }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $inscActiva->grupo->actividad->departamento->nombre ?? '—' }}</small>
                                </td>
                                <td>
                                    @if($inscActiva)
                                        <span class="badge badge-{{ $inscActiva->estatus == 'cursando' ? 'success' : 'info' }}">
                                            {{ ucfirst($inscActiva->estatus) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($inscActiva)
                                        <button type="button"
                                                class="btn btn-danger btn-sm"
                                                onclick="confirmarBaja({{ $inscActiva->id_inscripcion }}, '{{ addslashes($alumno->usuario->nombre_completo ?? '') }}', '{{ addslashes($inscActiva->grupo->actividad->nombre ?? '') }}')">
                                            <i class="fas fa-user-minus"></i>
                                            <span class="d-none d-md-inline">Dar de baja</span>
                                        </button>
                                        <form id="form-baja-{{ $inscActiva->id_inscripcion }}"
                                              action="{{ route('coordinador.alumnos.baja', $inscActiva->id_inscripcion) }}"
                                              method="POST" style="display:none;">
                                            @csrf
                                            {{-- Preservar filtros al redirigir --}}
                                            @foreach(request()->except('_token') as $k => $v)
                                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                            @endforeach
                                        </form>
                                    @else
                                        <span class="text-muted small">Sin inscripción</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-graduate fa-2x mb-2 d-block"></i>
                                    No hay alumnos inscritos con los filtros seleccionados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($alumnos->hasPages())
                    <div class="card-footer">{{ $alumnos->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection

@section('scripts')
<script>
// ── Filtros instantáneos al cambiar selects ───────────────────────────────
['id_carrera', 'id_departamento', 'id_actividad'].forEach(function(id) {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', function() {
        document.getElementById('form-filtros').submit();
    });
});

// Búsqueda con debounce al escribir
let debounceTimer;
const buscarInput = document.getElementById('buscar');
if (buscarInput) {
    buscarInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            document.getElementById('form-filtros').submit();
        }, 600);
    });
}

// ── Confirmación de baja ──────────────────────────────────────────────────
function confirmarBaja(idInscripcion, nombreAlumno, nombreActividad) {
    swal({
        title: 'Dar de baja',
        content: (function() {
            const div = document.createElement('div');
            div.innerHTML = '¿Deseas dar de baja a <strong>' + nombreAlumno + '</strong>'
                + ' de la actividad <strong>' + nombreActividad + '</strong>?'
                + '<br><br><small class="text-muted">Esta acción se conservará en el historial con estatus "baja".</small>';
            return div;
        })(),
        icon: 'warning',
        buttons: {
            cancel: { text: 'Cancelar', visible: true, className: 'btn btn-secondary' },
            confirm: { text: 'Sí, dar de baja', className: 'btn btn-danger' }
        },
        dangerMode: true,
    }).then(function(ok) {
        if (ok) document.getElementById('form-baja-' + idInscripcion).submit();
    });
}
</script>
@endsection
