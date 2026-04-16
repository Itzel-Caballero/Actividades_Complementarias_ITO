@extends('layouts.app')
@section('title', 'Grupos y Horarios')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Grupos y Horarios</h3>
    </div>
    <div class="section-body">

        @if(!$hasActiveSemestre)

    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>No hay un período escolar activo.</strong> No es posible crear nuevos grupos en este momento.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

        {{-- Leyenda indicador de cupo --}}
        <div class="d-flex align-items-center mb-2" style="gap:12px; font-size:13px;">
            <span><span class="badge badge-danger">●</span> Bajo mínimo requerido</span>
            <span><span class="badge badge-success">●</span> Cupo Mínimo Superado</span>
            <span><span class="badge badge-secondary">●</span> Cupo lleno</span>
        </div>

        {{-- Filtros instantáneos --}}
        <div class="card">
            <div class="card-body py-2">
                <form method="GET" id="form-filtros-grupos" class="form-row align-items-end">
                    <div class="col-12 col-md-4 mb-2">
                        <input type="text" name="buscar" id="buscar-grupos" value="{{ request('buscar') }}"
                               class="form-control form-control-sm" placeholder="Buscar actividad...">
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <select name="id_departamento" id="dep-grupos" class="form-control form-control-sm">
                            <option value="">Todos los departamentos</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}"
                                    {{ request('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                    {{ $dep->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2 mb-2">
                        <select name="estatus" id="est-grupos" class="form-control form-control-sm">
                            <option value="">Todos los estatus</option>
                            <option value="abierta"    {{ request('estatus') == 'abierta'    ? 'selected' : '' }}>Abierta</option>
                            <option value="cerrada"    {{ request('estatus') == 'cerrada'    ? 'selected' : '' }}>Cerrada</option>
                            <option value="cancelada"  {{ request('estatus') == 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
                            <option value="finalizada" {{ request('estatus') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 mb-2 d-flex" style="gap:6px;">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('coordinador.grupos') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-times"></i>
                        </a>
                        <a href="{{ route('coordinador.grupos.create') }}" class="btn btn-primary btn-sm ml-auto {{ !$hasActiveSemestre ? 'disabled' : '' }}">
                            <i class="fa fa-plus"></i> Nuevo Grupo
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
                                <th>Actividad</th>
                                <th>Grupo</th>
                                <th>Departamento</th>
                                <th>Docente</th>
                                <th>Horario</th>
                                <th>Cupo</th>
                                <th>Carreras</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grupos as $grupo)
                            @php
                                $inscritos   = $grupo->inscripciones->count();
                                $maximo      = $grupo->cupo_maximo;
                                // Mínimo = 30% del cupo máximo (puedes ajustar)
                                $minimo      = max(1, (int)round($maximo * 0.3));
                                $lleno       = $inscritos >= $maximo;
                                $superaMin   = $inscritos >= $minimo && !$lleno;
                                $bajMin      = $inscritos < $minimo;

                                if ($lleno)         $badgeCupo = 'secondary';
                                elseif ($superaMin) $badgeCupo = 'success';
                                else                $badgeCupo = 'danger';

                                // Colapsar horarios por día
                                $horariosPorDia = $grupo->horarios->groupBy(fn($h) => $h->dia->nombre_dia ?? 'Sin día');
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $grupo->actividad->nombre ?? 'N/A' }}</strong></td>
                                <td><span class="badge badge-primary">{{ $grupo->grupo }}</span></td>
                                <td><small>{{ $grupo->actividad->departamento->nombre ?? 'N/A' }}</small></td>
                                <td>
                                    @if($grupo->instructor)
                                        {{ $grupo->instructor->usuario->nombre_completo ?? 'N/A' }}
                                    @else
                                        <span class="badge badge-warning">Sin asignar</span>
                                    @endif
                                </td>
                                <td style="min-width:130px;">
                                    @forelse($horariosPorDia as $dia => $bloques)
                                        @php
                                            // Rango: primer inicio – último fin del día
                                            $inicio = substr($bloques->min('hora_inicio'), 0, 5);
                                            $fin    = substr($bloques->max('hora_fin'), 0, 5);
                                        @endphp
                                        <small class="d-block text-nowrap">
                                            <i class="fa fa-clock text-muted"></i>
                                            <strong>{{ ucfirst($dia) }}</strong>: {{ $inicio }}–{{ $fin }}
                                        </small>
                                    @empty
                                        <small class="text-muted">Sin horario</small>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge badge-{{ $badgeCupo }}"
                                          title="{{ $lleno ? 'Cupo lleno' : ($superaMin ? 'Supera el mínimo ('.$minimo.')' : 'Bajo mínimo requerido ('.$minimo.')') }}">
                                        {{ $inscritos }}/{{ $maximo }}
                                    </span>
                                    <br><small class="text-muted" style="font-size:10px;">mín. {{ $minimo }}</small>
                                </td>
                                <td>
                                    @foreach($grupo->actividad->carreras->take(2) as $c)
                                        <span class="badge badge-info" style="font-size:10px;">{{ $c->nombre }}</span>
                                    @endforeach
                                    @if($grupo->actividad->carreras->count() > 2)
                                        <small class="text-muted">+{{ $grupo->actividad->carreras->count() - 2 }} más</small>
                                    @endif
                                    @if($grupo->actividad->carreras->isEmpty())
                                        <small class="text-muted">Todas</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $grupo->estatus == 'abierta' ? 'success' : ($grupo->estatus == 'cancelada' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($grupo->estatus) }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('coordinador.grupos.edit', $grupo->id_grupo) }}"
                                       class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                            onclick="confirmarEliminarGrupo({{ $grupo->id_grupo }}, '{{ addslashes($grupo->grupo) }}', '{{ addslashes($grupo->actividad->nombre ?? '') }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <form id="form-del-{{ $grupo->id_grupo }}"
                                          action="{{ route('coordinador.grupos.destroy', $grupo->id_grupo) }}"
                                          method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-layer-group fa-2x mb-2 d-block"></i>
                                    No hay grupos registrados.
                                    <br>
                                    <a href="{{ route('coordinador.grupos.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fa fa-plus"></i> Crear primer grupo
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($grupos->hasPages())
                    <div class="card-footer">{{ $grupos->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection

@section('scripts')
<script>
// Filtros instantáneos al cambiar selects
['dep-grupos', 'est-grupos'].forEach(function(id) {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', function() {
        document.getElementById('form-filtros-grupos').submit();
    });
});
// Búsqueda con debounce
let debounceTimer;
const buscarInput = document.getElementById('buscar-grupos');
if (buscarInput) {
    buscarInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            document.getElementById('form-filtros-grupos').submit();
        }, 600);
    });
}

// Confirmación de eliminar grupo en rojo
function confirmarEliminarGrupo(id, grupo, actividad) {
    swal({
        title: 'Eliminar Grupo',
        content: (function() {
            const div = document.createElement('div');
            div.innerHTML = '¿Deseas eliminar el grupo <strong>' + grupo + '</strong>'
                + ' de <strong>' + actividad + '</strong>?'
                + '<br><br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Se eliminarán también sus horarios e inscripciones.</span>';
            return div;
        })(),
        icon: 'warning',
        buttons: {
            cancel: { text: 'Cancelar', visible: true, className: 'btn btn-secondary' },
            confirm: { text: 'Sí, eliminar', className: 'btn btn-danger' }
        },
        dangerMode: true,
    }).then(function(ok) {
        if (ok) document.getElementById('form-del-' + id).submit();
    });
}
</script>
@endsection
