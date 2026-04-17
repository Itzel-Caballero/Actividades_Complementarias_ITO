@extends('layouts.app')
@section('title', 'Editar Grupo')

@section('page_css')
<style>
.schedule-wrapper { overflow-x: auto; }
.schedule-table { width:100%; border-collapse:collapse; min-width:600px; font-size:13px; }
.schedule-table th { background:#f4f6f9; text-align:center; padding:8px 4px; font-weight:700; font-size:12px; color:#6c757d; text-transform:uppercase; letter-spacing:.04em; border:1px solid #e9ecef; }
.schedule-table td.time-col { background:#f4f6f9; color:#6c757d; font-size:11px; font-weight:600; text-align:center; white-space:nowrap; padding:0 8px; border:1px solid #e9ecef; width:90px; }
.schedule-table td.slot { border:1px solid #e9ecef; padding:0; height:28px; cursor:pointer; transition:background .12s; }
.schedule-table td.slot:hover { background:#d6d3f7; }
.schedule-table td.slot.active { background:#6259ca; position:relative; }
.schedule-table td.slot.active::after { content:''; position:absolute; inset:2px; border-radius:3px; background:rgba(255,255,255,.15); }
.schedule-legend { display:flex; align-items:center; gap:8px; font-size:12px; color:#6c757d; margin-top:10px; }
.legend-box { width:18px; height:18px; border-radius:4px; display:inline-block; flex-shrink:0; }
.legend-box.active { background:#6259ca; }
.legend-box.inactive { background:#e9ecef; border:1px solid #dee2e6; }
.semestre-badge { display:inline-flex; align-items:center; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.semestre-ene-jun { background:#d4edda; color:#155724; }
.semestre-ago-dic { background:#fff3cd; color:#856404; }
</style>
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Editar Grupo: <span class="text-primary">{{ $grupo->grupo }}</span></h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Por favor corrige los siguientes errores:</strong>
                        <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <form action="{{ route('coordinador.grupos.update', $grupo->id_grupo) }}" method="POST" id="form-grupo">
                    @csrf
                    @method('PUT')

                    {{-- Información del Grupo --}}
                    <div class="card">
                        <div class="card-header"><h4><i class="fas fa-layer-group mr-2 text-primary"></i>Información del Grupo</h4></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Actividad <span class="text-danger">*</span></label>
                                        <select name="id_actividad" id="sel-actividad" class="form-control select2" required>
                                            @foreach($actividades as $act)
                                                <option value="{{ $act->id_actividad }}"
                                                    data-depto="{{ $act->id_departamento }}"
                                                    {{ old('id_actividad', $grupo->id_actividad) == $act->id_actividad ? 'selected' : '' }}>
                                                    {{ $act->nombre }}
                                                    @if($act->departamento)({{ $act->departamento->nombre }})@endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Identificador del Grupo <span class="text-danger">*</span></label>
                                        <input type="text" name="grupo" class="form-control text-uppercase"
                                               value="{{ old('grupo', $grupo->grupo) }}" required maxlength="10">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Periodo Escolar Activo</label>
                                        {{-- Campo oculto: el semestre viene fijo del servidor --}}
                                        <div class="form-control d-flex align-items-center" style="background:#f8f9fa; cursor:default;">
                                            <span class="badge {{ $semestreActual['clase'] }} mr-2" style="font-size:12px;">
                                                <i class="fas fa-calendar-check mr-1"></i>{{ $semestreActual['etiqueta'] }}
                                            </span>
                                        </div>
                                        <small class="text-muted">Los grupos se asignan al periodo activo automáticamente.</small>
                                        <input type="hidden" name="id_semestre" value="{{ $grupo->id_semestre }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Cupo Mínimo <span class="text-danger">*</span></label>
                                        <input type="number" name="cupo_minimo" class="form-control" value="{{ old('cupo_minimo',$grupo->cupo_minimo ?? 1) }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Cupo Máximo <span class="text-danger">*</span></label>
                                        <input type="number" name="cupo_maximo" class="form-control" value="{{ old('cupo_maximo',$grupo->cupo_maximo) }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Modalidad <span class="text-danger">*</span></label>
                                        <select name="modalidad" class="form-control" required>
                                            @foreach(['presencial','virtual','hibrida'] as $mod)
                                                <option value="{{ $mod }}" {{ old('modalidad',$grupo->modalidad)==$mod?'selected':'' }}>{{ ucfirst($mod) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Estatus</label>
                                        <select name="estatus" class="form-control">
                                            @foreach(['abierta','cerrada','cancelada','finalizada'] as $est)
                                                <option value="{{ $est }}" {{ old('estatus',$grupo->estatus)==$est?'selected':'' }}>{{ ucfirst($est) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Fecha Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio',$grupo->fecha_inicio) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Fecha Fin <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin',$grupo->fecha_fin) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ubicación</label>
                                        <select name="id_ubicacion" class="form-control">
                                            <option value="">-- Sin ubicación --</option>
                                            @foreach($ubicaciones as $ub)
                                                <option value="{{ $ub->id_ubicacion }}" {{ old('id_ubicacion',$grupo->id_ubicacion)==$ub->id_ubicacion?'selected':'' }}>{{ $ub->espacio }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Materiales Requeridos</label>
                                        <input type="text" name="materiales_requeridos" class="form-control" value="{{ old('materiales_requeridos',$grupo->materiales_requeridos) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Docente (filtrado por depto de la actividad) --}}
                    <div class="card">
                        <div class="card-header"><h4><i class="fas fa-chalkboard-teacher mr-2 text-primary"></i>Docente Asignado</h4></div>
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label>Nombre del Docente
                                    <small class="text-muted" id="docente-filtro-info">(Filtrado por departamento de la actividad)</small>
                                </label>
                                <select name="id_instructor" id="select-instructor" class="form-control select2">
                                    <option value="">-- Sin docente asignado --</option>
                                    @foreach($instructores as $ins)
                                        <option value="{{ $ins->id_instructor }}"
                                            data-depto="{{ $ins->id_departamento }}"
                                            {{ old('id_instructor',$grupo->id_instructor)==$ins->id_instructor?'selected':'' }}>
                                            {{ $ins->usuario->nombre_completo ?? 'Sin nombre' }} — {{ $ins->departamento->nombre ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Selecciona "Sin docente asignado" para quitar la asignación.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Horario --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4><i class="fas fa-calendar-alt mr-2 text-primary"></i>Horario Semanal</h4>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSchedule()">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3"><i class="fas fa-hand-pointer"></i> Los bloques morados son el horario actual.</p>
                            <div class="schedule-wrapper">
                                <table class="schedule-table">
                                    <thead>
                                        <tr><th>Horario</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th></tr>
                                    </thead>
                                    <tbody id="schedule-body"></tbody>
                                </table>
                            </div>
                            <div class="schedule-legend mt-2">
                                <span class="legend-box active"></span> Seleccionado
                                <span class="legend-box inactive ml-2"></span> Disponible
                            </div>
                            <div id="horarios-inputs"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('coordinador.grupos') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Actualizar Grupo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
const todosInstructores = @json($instructoresPorDepto);
const horExistentes     = @json($horExistentes);

// ── Filtrar docentes al cambiar actividad ─────────────────────────────────
document.getElementById('sel-actividad').addEventListener('change', function() {
    const opt    = this.options[this.selectedIndex];
    const deptoId = opt ? parseInt(opt.dataset.depto) : null;
    filtrarDocentes(deptoId, null);
});

function filtrarDocentes(deptoId, keepVal) {
    const sel  = document.getElementById('select-instructor');
    const info = document.getElementById('docente-filtro-info');
    const prev = keepVal !== null ? keepVal : sel.value;
    sel.innerHTML = '<option value="">-- Sin docente asignado --</option>';

    const lista = deptoId
        ? todosInstructores.filter(function(i) { return i.id_depto === deptoId; })
        : todosInstructores;

    info.textContent = deptoId
        ? (lista.length ? '(Filtrado por departamento)' : '(Sin docentes en este departamento)')
        : '(Todos los docentes)';

    lista.forEach(function(i) {
        const opt = document.createElement('option');
        opt.value       = i.id;
        opt.textContent = i.nombre + ' — ' + i.depto;
        opt.dataset.depto = i.id_depto;
        if (String(i.id) === String(prev)) opt.selected = true;
        sel.appendChild(opt);
    });

    if (typeof $ !== 'undefined' && $(sel).data('select2')) $(sel).trigger('change.select2');
}

// Cargar filtro inicial con la actividad ya seleccionada
(function() {
    const sel = document.getElementById('sel-actividad');
    const opt = sel.options[sel.selectedIndex];
    const deptoId = opt && opt.dataset.depto ? parseInt(opt.dataset.depto) : null;
    filtrarDocentes(deptoId, {{ $grupo->id_instructor ?? 'null' }});
})();

// ── Horario ───────────────────────────────────────────────────────────────
const DIAS_IDS = [
    @json($diasSemana->where('nombre_dia','lunes')->first()->id_dia    ?? 1),
    @json($diasSemana->where('nombre_dia','martes')->first()->id_dia   ?? 2),
    @json($diasSemana->where('nombre_dia','miercoles')->first()->id_dia ?? 3),
    @json($diasSemana->where('nombre_dia','jueves')->first()->id_dia   ?? 4),
    @json($diasSemana->where('nombre_dia','viernes')->first()->id_dia  ?? 5),
    @json($diasSemana->where('nombre_dia','sabado')->first()->id_dia   ?? 6),
];

const TIMES = [];
for (let h = 7; h < 20; h++) {
    TIMES.push(String(h).padStart(2,'0') + ':00');
    TIMES.push(String(h).padStart(2,'0') + ':30');
}

let scheduleState = {};

// Precargar horarios existentes
horExistentes.forEach(function(h) {
    const di = DIAS_IDS.indexOf(parseInt(h.id_dia));
    const ti = TIMES.indexOf(h.hora_inicio);
    if (di >= 0 && ti >= 0) scheduleState[di + '-' + ti] = true;
});

function buildSchedule() {
    const tbody = document.getElementById('schedule-body');
    tbody.innerHTML = '';
    TIMES.forEach(function(t, ti) {
        const endH   = ti % 2 === 0 ? String(parseInt(t)).padStart(2,'0')+':30' : String(parseInt(t)+1).padStart(2,'0')+':00';
        const tr     = document.createElement('tr');
        const tdTime = document.createElement('td');
        tdTime.className   = 'time-col';
        tdTime.textContent = t + ' - ' + endH;
        tr.appendChild(tdTime);
        DIAS_IDS.forEach(function(_, di) {
            const td     = document.createElement('td');
            td.className = 'slot';
            td.dataset.dia  = di;
            td.dataset.time = ti;
            td.onclick = function() { toggleSlot(di, ti); };
            if (scheduleState[di + '-' + ti]) td.classList.add('active');
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
}

function toggleSlot(di, ti) {
    const key = di + '-' + ti;
    scheduleState[key] = !scheduleState[key];
    const td = document.querySelector('td.slot[data-dia="'+di+'"][data-time="'+ti+'"]');
    if (td) td.classList.toggle('active', scheduleState[key]);
    updateHiddenInputs();
}

function clearSchedule() {
    scheduleState = {};
    document.querySelectorAll('td.slot.active').forEach(function(td) { td.classList.remove('active'); });
    updateHiddenInputs();
}

function updateHiddenInputs() {
    const c = document.getElementById('horarios-inputs');
    c.innerHTML = '';
    let idx = 0;
    TIMES.forEach(function(t, ti) {
        const endMin  = ti % 2 === 0 ? 30 : 0;
        const endHour = ti % 2 === 0 ? parseInt(t) : parseInt(t) + 1;
        const horaFin = String(endHour).padStart(2,'0') + ':' + String(endMin).padStart(2,'0');
        DIAS_IDS.forEach(function(diaId, di) {
            if (scheduleState[di + '-' + ti]) {
                c.insertAdjacentHTML('beforeend',
                    '<input type="hidden" name="horarios['+idx+'][id_dia]" value="'+diaId+'">'
                    + '<input type="hidden" name="horarios['+idx+'][hora_inicio]" value="'+t+'">'
                    + '<input type="hidden" name="horarios['+idx+'][hora_fin]" value="'+horaFin+'">');
                idx++;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    buildSchedule();
    updateHiddenInputs();
});
</script>
@endsection
