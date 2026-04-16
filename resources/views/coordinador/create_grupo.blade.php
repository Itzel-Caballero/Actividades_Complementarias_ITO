@extends('layouts.app')
@section('title', 'Nuevo Grupo')

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
        <h3 class="page__heading">Nuevo Grupo</h3>
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

                <form action="{{ route('coordinador.grupos.store') }}" method="POST" id="form-grupo">
                    @csrf

                    {{-- Información del Grupo --}}
                    <div class="card">
                        <div class="card-header"><h4><i class="fas fa-layer-group mr-2 text-primary"></i>Información del Grupo</h4></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Actividad <span class="text-danger">*</span></label>
                                        <select name="id_actividad" id="sel-actividad" class="form-control select2" required>
                                            <option value="">-- Selecciona una actividad --</option>
                                            @foreach($actividades as $act)
                                                <option value="{{ $act->id_actividad }}"
                                                    data-depto="{{ $act->id_departamento }}"
                                                    {{ old('id_actividad') == $act->id_actividad ? 'selected' : '' }}>
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
                                               value="{{ old('grupo') }}" placeholder="Ej. A, G01" required maxlength="10">
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
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cupo Máximo <span class="text-danger">*</span></label>
                                        <input type="number" name="cupo_maximo" class="form-control" value="{{ old('cupo_maximo',30) }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Modalidad <span class="text-danger">*</span></label>
                                        <select name="modalidad" class="form-control" required>
                                            <option value="presencial" {{ old('modalidad','presencial')=='presencial'?'selected':'' }}>Presencial</option>
                                            <option value="virtual"    {{ old('modalidad')=='virtual'?'selected':'' }}>Virtual</option>
                                            <option value="hibrida"    {{ old('modalidad')=='hibrida'?'selected':'' }}>Híbrida</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha de Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha de Fin <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}" required>
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
                                                <option value="{{ $ub->id_ubicacion }}" {{ old('id_ubicacion')==$ub->id_ubicacion?'selected':'' }}>{{ $ub->espacio }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Materiales Requeridos</label>
                                        <input type="text" name="materiales_requeridos" class="form-control" value="{{ old('materiales_requeridos') }}" placeholder="Opcional">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Docente (se filtra según departamento de la actividad) --}}
                    <div class="card">
                        <div class="card-header"><h4><i class="fas fa-chalkboard-teacher mr-2 text-primary"></i>Asignación de Docente</h4></div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="doc-asignar" name="modo_docente" value="asignar" class="custom-control-input" checked onchange="toggleDocente(this.value)">
                                    <label class="custom-control-label" for="doc-asignar"><i class="fas fa-check-circle text-success"></i> Asignar docente ahora</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="doc-despues" name="modo_docente" value="despues" class="custom-control-input" onchange="toggleDocente(this.value)">
                                    <label class="custom-control-label" for="doc-despues"><i class="fas fa-clock text-warning"></i> Dejar sin asignar</label>
                                </div>
                            </div>
                            <div id="seccion-docente">
                                <div class="form-group mb-0">
                                    <label>Nombre del Docente
                                        <small class="text-muted" id="docente-filtro-info">(Selecciona primero una actividad para filtrar por departamento)</small>
                                    </label>
                                    <select name="id_instructor" id="select-instructor" class="form-control select2">
                                        <option value="">-- Seleccionar docente --</option>
                                        @foreach($instructores as $ins)
                                            <option value="{{ $ins->id_instructor }}"
                                                data-depto="{{ $ins->id_departamento }}"
                                                {{ old('id_instructor')==$ins->id_instructor?'selected':'' }}>
                                                {{ $ins->usuario->nombre_completo ?? 'Sin nombre' }} — {{ $ins->departamento->nombre ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="msg-sin-docente" class="alert alert-warning mb-0 d-none">
                                <i class="fas fa-exclamation-triangle"></i> El grupo quedará <strong>sin docente asignado</strong>.
                            </div>
                        </div>
                    </div>

                    {{-- Carreras Permitidas (dinámico según la actividad elegida) --}}
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-university mr-2 text-primary"></i>Carreras Permitidas</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">
                                <i class="fas fa-info-circle"></i>
                                Las carreras habilitadas dependen de la actividad seleccionada.
                            </p>
                            <div id="carreras-lista">
                                <p class="text-muted"><i class="fas fa-arrow-up mr-1"></i>Selecciona una actividad para ver las carreras permitidas.</p>
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
                            <p class="text-muted small mb-3"><i class="fas fa-hand-pointer"></i> Haz clic para marcar bloques de 30 min.</p>
                            <div class="schedule-wrapper">
                                <table class="schedule-table">
                                    <thead>
                                        <tr>
                                            <th>Horario</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th>
                                        </tr>
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
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar Grupo</button>
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
const carrerasPorActiv  = @json($carrerasPorActividad);

function actualizarCarreras(idActividad) {
    const contenedor = document.getElementById('carreras-lista');
    // Buscar con clave string (JSON siempre usa string como clave de objeto)
    const key  = idActividad ? String(idActividad) : null;
    const lista = key ? (carrerasPorActiv[key] || []) : [];
    if (!key) {
        contenedor.innerHTML = '<p class="text-muted"><i class="fas fa-arrow-up mr-1"></i>Selecciona una actividad para ver las carreras permitidas.</p>';
        return;
    }
    if (lista.length === 0) {
        contenedor.innerHTML = '<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-1"></i>Esta actividad no tiene carreras asignadas. Edítala para agregarlas.</div>';
        return;
    }
    let html = '<div class="row">';
    lista.forEach(function(c) {
        html += '<div class="col-md-4 mb-2"><div class="d-flex align-items-center"><i class="fas fa-check-circle text-success mr-2"></i><span>' + c.nombre + '</span></div></div>';
    });
    html += '</div><p class="text-muted small mt-2 mb-0"><i class="fas fa-info-circle mr-1"></i>Solo alumnos de estas carreras podrán inscribirse.</p>';
    contenedor.innerHTML = html;
}


document.getElementById('sel-actividad').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    filtrarDocentes(selected ? parseInt(selected.dataset.depto) : null);
    actualizarCarreras(this.value || null);
});

// Select2 no dispara el evento change nativo: escuchar también el evento de jQuery
$(document).ready(function() {
    $('#sel-actividad').on('select2:select select2:clear', function() {
        const val     = $(this).val();
        const deptoId = val ? parseInt($(this).find(':selected').data('depto')) : null;
        filtrarDocentes(deptoId);
        actualizarCarreras(val || null);
    });
});

function filtrarDocentes(deptoId) {
    const sel = document.getElementById('select-instructor');
    const info = document.getElementById('docente-filtro-info');
    const oldVal = sel.value;
    sel.innerHTML = '<option value="">-- Seleccionar docente --</option>';
    const lista = deptoId ? todosInstructores.filter(function(i){ return i.id_depto === deptoId; }) : todosInstructores;
    info.textContent = deptoId ? (lista.length ? '(Filtrado por departamento)' : '(Sin docentes en este depto)') : '(Selecciona primero una actividad)';
    lista.forEach(function(i) {
        const opt = document.createElement('option');
        opt.value = i.id; opt.textContent = i.nombre + ' — ' + i.depto;
        if (String(i.id) === oldVal) opt.selected = true;
        sel.appendChild(opt);
    });
    if (typeof $ !== 'undefined' && $(sel).data('select2')) $(sel).trigger('change.select2');
}

@if(old('id_actividad'))
(function() {
    const sel = document.getElementById('sel-actividad');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        filtrarDocentes(parseInt(opt.dataset.depto)||null);
        actualizarCarreras(opt.value);
    }
})();
@endif

function toggleDocente(val) {
    const seccion = document.getElementById('seccion-docente');
    const msg = document.getElementById('msg-sin-docente');
    if (val === 'asignar') { seccion.classList.remove('d-none'); msg.classList.add('d-none'); }
    else { seccion.classList.add('d-none'); msg.classList.remove('d-none'); document.getElementById('select-instructor').value = ''; }
}

const DIAS_IDS = [
    @json($diasSemana->where('nombre_dia','lunes')->first()->id_dia ?? 1),
    @json($diasSemana->where('nombre_dia','martes')->first()->id_dia ?? 2),
    @json($diasSemana->where('nombre_dia','miercoles')->first()->id_dia ?? 3),
    @json($diasSemana->where('nombre_dia','jueves')->first()->id_dia ?? 4),
    @json($diasSemana->where('nombre_dia','viernes')->first()->id_dia ?? 5),
    @json($diasSemana->where('nombre_dia','sabado')->first()->id_dia ?? 6),
];
const TIMES = [];
for (let h = 7; h < 20; h++) { TIMES.push(String(h).padStart(2,'0')+':00'); TIMES.push(String(h).padStart(2,'0')+':30'); }
let scheduleState = {};

function buildSchedule() {
    const tbody = document.getElementById('schedule-body');
    tbody.innerHTML = '';
    TIMES.forEach(function(t, ti) {
        const endH = ti%2===0 ? String(parseInt(t)).padStart(2,'0')+':30' : String(parseInt(t)+1).padStart(2,'0')+':00';
        const tr = document.createElement('tr');
        const tdT = document.createElement('td'); tdT.className='time-col'; tdT.textContent=t+' - '+endH; tr.appendChild(tdT);
        DIAS_IDS.forEach(function(_,di) {
            const td = document.createElement('td'); td.className='slot';
            td.dataset.dia=di; td.dataset.time=ti;
            td.onclick=function(){toggleSlot(di,ti);};
            if (scheduleState[di+'-'+ti]) td.classList.add('active');
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
}
function toggleSlot(di,ti) {
    const key=di+'-'+ti; scheduleState[key]=!scheduleState[key];
    const td=document.querySelector('td.slot[data-dia="'+di+'"][data-time="'+ti+'"]');
    if(td) td.classList.toggle('active',scheduleState[key]);
    updateHiddenInputs();
}
function clearSchedule() {
    scheduleState={};
    document.querySelectorAll('td.slot.active').forEach(function(td){td.classList.remove('active');});
    updateHiddenInputs();
}
function updateHiddenInputs() {
    const c=document.getElementById('horarios-inputs'); c.innerHTML=''; let idx=0;
    TIMES.forEach(function(t,ti) {
        const endMin=ti%2===0?30:0; const endHour=ti%2===0?parseInt(t):parseInt(t)+1;
        const horaFin=String(endHour).padStart(2,'0')+':'+String(endMin).padStart(2,'0');
        DIAS_IDS.forEach(function(diaId,di) {
            if(scheduleState[di+'-'+ti]) {
                c.insertAdjacentHTML('beforeend','<input type="hidden" name="horarios['+idx+'][id_dia]" value="'+diaId+'"><input type="hidden" name="horarios['+idx+'][hora_inicio]" value="'+t+'"><input type="hidden" name="horarios['+idx+'][hora_fin]" value="'+horaFin+'">');
                idx++;
            }
        });
    });
}

@if(old('horarios'))
(function() {
    const oldH = @json(old('horarios'));
    oldH.forEach(function(h) {
        const di=DIAS_IDS.indexOf(parseInt(h.id_dia));
        const ti=TIMES.indexOf(h.hora_inicio?h.hora_inicio.substring(0,5):'');
        if(di>=0&&ti>=0) scheduleState[di+'-'+ti]=true;
    });
})();
@endif

document.addEventListener('DOMContentLoaded', function() {
    buildSchedule();
    Object.keys(scheduleState).forEach(function(key){
        const p=key.split('-');
        const td=document.querySelector('td.slot[data-dia="'+p[0]+'"][data-time="'+p[1]+'"]');
        if(td) td.classList.add('active');
    });
    updateHiddenInputs();
    const selAct=document.getElementById('sel-actividad');
    if(selAct&&selAct.value) actualizarCarreras(selAct.value);
});
</script>
@endsection
