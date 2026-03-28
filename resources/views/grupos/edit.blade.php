@extends('layouts.app')

@section('page_css')
<style>
.schedule-wrapper { overflow-x: auto; }
.schedule-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
    font-size: 13px;
}
.schedule-table th {
    background: #f4f6f9;
    text-align: center;
    padding: 8px 4px;
    font-weight: 700;
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: .04em;
    border: 1px solid #e9ecef;
}
.schedule-table td.time-col {
    background: #f4f6f9;
    color: #6c757d;
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    padding: 0 8px;
    border: 1px solid #e9ecef;
    width: 90px;
}
.schedule-table td.slot {
    border: 1px solid #e9ecef;
    padding: 0;
    height: 28px;
    cursor: pointer;
    transition: background .12s;
}
.schedule-table td.slot:hover  { background: #d6d3f7; }
.schedule-table td.slot.active { background: #6259ca; position: relative; }
.schedule-table td.slot.active::after {
    content: '';
    position: absolute;
    inset: 2px;
    border-radius: 3px;
    background: rgba(255,255,255,.15);
}
.schedule-legend {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #6c757d;
    margin-top: 10px;
}
.legend-box { width: 18px; height: 18px; border-radius: 4px; display: inline-block; flex-shrink: 0; }
.legend-box.active   { background: #6259ca; }
.legend-box.inactive { background: #e9ecef; border: 1px solid #dee2e6; }
</style>
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Editar Grupo</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Corrige los siguientes errores:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <form action="{{ route('grupos.update', $grupo->id_grupo) }}" method="POST" id="form-grupo">
                    @csrf
                    @method('PUT')

                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-layer-group mr-2 text-primary"></i>Editar Grupo: <strong>{{ $grupo->actividad->nombre ?? '' }} — {{ $grupo->grupo }}</strong></h4>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Actividad <span class="text-danger">*</span></label>
                                        <select name="id_actividad" class="form-control" required>
                                            <option value="">-- Selecciona --</option>
                                            @foreach ($actividades as $act)
                                                <option value="{{ $act->id_actividad }}"
                                                    {{ old('id_actividad', $grupo->id_actividad) == $act->id_actividad ? 'selected' : '' }}>
                                                    {{ $act->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Clave de Grupo <span class="text-danger">*</span></label>
                                        <input type="text" name="grupo" class="form-control"
                                               value="{{ old('grupo', $grupo->grupo) }}" required maxlength="10">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Semestre <span class="text-danger">*</span></label>
                                        <select name="id_semestre" class="form-control" required>
                                            <option value="">-- Selecciona --</option>
                                            @foreach ($semestres as $sem)
                                                <option value="{{ $sem->id_semestre }}"
                                                    {{ old('id_semestre', $grupo->id_semestre) == $sem->id_semestre ? 'selected' : '' }}>
                                                    {{ $sem->año }} — {{ $sem->periodo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- INSTRUCTOR — campo principal de esta pantalla --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <i class="fa fa-user-tie"></i>
                                            Instructor asignado
                                        </label>
                                        <select name="id_instructor" class="form-control">
                                            <option value="">— Sin asignar —</option>
                                            @foreach ($instructores as $inst)
                                                <option value="{{ $inst->id_instructor }}"
                                                    {{ old('id_instructor', $grupo->id_instructor) == $inst->id_instructor ? 'selected' : '' }}>
                                                    {{ $inst->usuario->nombre ?? '' }}
                                                    {{ $inst->usuario->apellido_paterno ?? '' }}
                                                    {{ $inst->usuario->apellido_materno ?? '' }}
                                                    @if($inst->especialidad)
                                                        — {{ $inst->especialidad }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">
                                            El instructor seleccionado verá este grupo en su panel.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ubicación</label>
                                        <select name="id_ubicacion" class="form-control">
                                            <option value="">— Sin asignar —</option>
                                            @foreach ($ubicaciones as $ubic)
                                                <option value="{{ $ubic->id_ubicacion }}"
                                                    {{ old('id_ubicacion', $grupo->id_ubicacion) == $ubic->id_ubicacion ? 'selected' : '' }}>
                                                    {{ $ubic->espacio }}
                                                    @if($ubic->capacidad)
                                                        (cap. {{ $ubic->capacidad }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Modalidad <span class="text-danger">*</span></label>
                                        <select name="modalidad" class="form-control" required>
                                            <option value="presencial" {{ old('modalidad', $grupo->modalidad) == 'presencial' ? 'selected' : '' }}>Presencial</option>
                                            <option value="virtual"    {{ old('modalidad', $grupo->modalidad) == 'virtual'    ? 'selected' : '' }}>Virtual</option>
                                            <option value="hibrida"    {{ old('modalidad', $grupo->modalidad) == 'hibrida'    ? 'selected' : '' }}>Híbrida</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Cupo Máximo <span class="text-danger">*</span></label>
                                        <input type="number" name="cupo_maximo" class="form-control"
                                               value="{{ old('cupo_maximo', $grupo->cupo_maximo) }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Estatus</label>
                                        <select name="estatus" class="form-control">
                                            @foreach(['abierta','cerrada','cancelada','finalizada'] as $est)
                                                <option value="{{ $est }}"
                                                    {{ old('estatus', $grupo->estatus) == $est ? 'selected' : '' }}>
                                                    {{ ucfirst($est) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" class="form-control"
                                               value="{{ old('fecha_inicio', $grupo->fecha_inicio) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha Fin <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin" class="form-control"
                                               value="{{ old('fecha_fin', $grupo->fecha_fin) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label>Materiales Requeridos</label>
                                <textarea name="materiales_requeridos" class="form-control" rows="2">{{ old('materiales_requeridos', $grupo->materiales_requeridos) }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- ── Horario Semanal ── --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4><i class="fas fa-calendar-alt mr-2 text-primary"></i>Horario Semanal</h4>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSchedule()">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                <i class="fas fa-hand-pointer"></i>
                                Haz clic en los bloques para marcar o desmarcar horarios.
                                Los bloques resaltados son el horario actual del grupo.
                            </p>
                            <div class="schedule-wrapper">
                                <table class="schedule-table" id="schedule-table">
                                    <thead>
                                        <tr>
                                            <th>Horario</th>
                                            <th>Lunes</th>
                                            <th>Martes</th>
                                            <th>Miércoles</th>
                                            <th>Jueves</th>
                                            <th>Viernes</th>
                                            <th>Sábado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="schedule-body"></tbody>
                                </table>
                            </div>
                            <div class="schedule-legend">
                                <span class="legend-box active"></span> Seleccionado
                                <span class="legend-box inactive ml-2"></span> Disponible
                            </div>
                            <div id="horarios-inputs"></div>
                        </div>
                    </div>

                    {{-- ── Botones ── --}}
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('grupos.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Guardar Cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
var horariosExistentes = {!! json_encode($grupo->horarios->map(function($h) {
    return [
        'id_dia'      => $h->id_dia,
        'hora_inicio' => substr($h->hora_inicio, 0, 5),
    ];
})) !!};

var DIAS = [
    { id: {{ $diasSemana->where('nombre_dia','lunes')->first()->id_dia    ?? 1 }} },
    { id: {{ $diasSemana->where('nombre_dia','martes')->first()->id_dia   ?? 2 }} },
    { id: {{ $diasSemana->where('nombre_dia','miercoles')->first()->id_dia ?? 3 }} },
    { id: {{ $diasSemana->where('nombre_dia','jueves')->first()->id_dia   ?? 4 }} },
    { id: {{ $diasSemana->where('nombre_dia','viernes')->first()->id_dia  ?? 5 }} },
    { id: {{ $diasSemana->where('nombre_dia','sabado')->first()->id_dia   ?? 6 }} }
];

var TIMES = [];
for (var h = 7; h < 20; h++) {
    TIMES.push((h < 10 ? '0' : '') + h + ':00');
    TIMES.push((h < 10 ? '0' : '') + h + ':30');
}

var scheduleState = {};

// Precargar horarios existentes
horariosExistentes.forEach(function(h) {
    var diaId = parseInt(h.id_dia);
    var di = -1;
    for (var i = 0; i < DIAS.length; i++) { if (DIAS[i].id === diaId) { di = i; break; } }
    var ti = TIMES.indexOf(h.hora_inicio);
    if (di >= 0 && ti >= 0) scheduleState[di + '-' + ti] = true;
});

function buildSchedule() {
    var tbody = document.getElementById('schedule-body');
    tbody.innerHTML = '';
    for (var ti = 0; ti < TIMES.length; ti++) {
        var t = TIMES[ti];
        var hNum = parseInt(t.split(':')[0]);
        var isHalf = ti % 2 !== 0;
        var endHour = isHalf ? hNum + 1 : hNum;
        var endMin  = isHalf ? '00' : '30';
        var endT = (endHour < 10 ? '0' : '') + endHour + ':' + endMin;

        var tr = document.createElement('tr');
        var tdTime = document.createElement('td');
        tdTime.className = 'time-col';
        tdTime.textContent = t + ' - ' + endT;
        tr.appendChild(tdTime);

        for (var di = 0; di < DIAS.length; di++) {
            var td = document.createElement('td');
            td.className = 'slot';
            td.setAttribute('data-dia', di);
            td.setAttribute('data-time', ti);
            td.onclick = (function(d, t2) {
                return function() { toggleSlot(d, t2); };
            })(di, ti);
            if (scheduleState[di + '-' + ti]) td.classList.add('active');
            tr.appendChild(td);
        }
        tbody.appendChild(tr);
    }
}

function toggleSlot(di, ti) {
    var key = di + '-' + ti;
    scheduleState[key] = !scheduleState[key];
    var td = document.querySelector('td.slot[data-dia="' + di + '"][data-time="' + ti + '"]');
    if (td) td.classList.toggle('active', scheduleState[key]);
    updateHiddenInputs();
}

function clearSchedule() {
    scheduleState = {};
    document.querySelectorAll('td.slot.active').forEach(function(td) { td.classList.remove('active'); });
    updateHiddenInputs();
}

function updateHiddenInputs() {
    var container = document.getElementById('horarios-inputs');
    container.innerHTML = '';
    var idx = 0;
    for (var ti = 0; ti < TIMES.length; ti++) {
        var t = TIMES[ti];
        var hNum = parseInt(t.split(':')[0]);
        var isHalf = ti % 2 !== 0;
        var endHour = isHalf ? hNum + 1 : hNum;
        var endMin  = isHalf ? '00' : '30';
        var horaFin = (endHour < 10 ? '0' : '') + endHour + ':' + endMin;
        for (var di = 0; di < DIAS.length; di++) {
            if (scheduleState[di + '-' + ti]) {
                container.insertAdjacentHTML('beforeend',
                    '<input type="hidden" name="horarios[' + idx + '][id_dia]"      value="' + DIAS[di].id + '">' +
                    '<input type="hidden" name="horarios[' + idx + '][hora_inicio]" value="' + t + '">' +
                    '<input type="hidden" name="horarios[' + idx + '][hora_fin]"    value="' + horaFin + '">'
                );
                idx++;
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    buildSchedule();
    updateHiddenInputs();
});
</script>
@endsection
