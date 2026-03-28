@extends('layouts.app')
@section('title', 'Nuevo Grupo')

@section('page_css')
<style>
/* ── Tabla de horario estilo MindBox ─────────────────────────────────── */
.schedule-wrapper {
    overflow-x: auto;
}
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
    text-align: center;
    vertical-align: middle;
}
.schedule-table td.slot:hover {
    background: #d6d3f7;
}
.schedule-table td.slot.active {
    background: #6259ca;
    position: relative;
}
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
.legend-box {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    display: inline-block;
    flex-shrink: 0;
}
.legend-box.active  { background: #6259ca; }
.legend-box.inactive { background: #e9ecef; border: 1px solid #dee2e6; }

/* Semestre badge */
.semestre-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
}
.semestre-ene-jun  { background: #d4edda; color: #155724; }
.semestre-ago-dic  { background: #fff3cd; color: #856404; }
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
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <form action="{{ route('coordinador.grupos.store') }}" method="POST" id="form-grupo">
                    @csrf

                    {{-- ── Información General ───────────────────────── --}}
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-layer-group mr-2 text-primary"></i>Información del Grupo</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Actividad <span class="text-danger">*</span></label>
                                        <select name="id_actividad" class="form-control select2" required>
                                            <option value="">-- Selecciona una actividad --</option>
                                            @foreach($actividades as $act)
                                                <option value="{{ $act->id_actividad }}"
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
                                               value="{{ old('grupo') }}" placeholder="Ej. A, B, G01"
                                               required maxlength="10">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Semestre <span class="text-danger">*</span>
                                            <span id="semestre-badge" class="semestre-badge ml-1 {{ $semestreActual['clase'] }}">
                                                {{ $semestreActual['etiqueta'] }}
                                            </span>
                                        </label>
                                        <select name="id_semestre" class="form-control" required>
                                            <option value="">-- Selecciona --</option>
                                            @foreach($semestres as $sem)
                                                @php
                                                    $etiq = $sem->periodo == 1
                                                        ? "Enero–Junio {$sem->año}"
                                                        : "Agosto–Diciembre {$sem->año}";
                                                @endphp
                                                <option value="{{ $sem->id_semestre }}"
                                                    {{ old('id_semestre', $semestreActual['id']) == $sem->id_semestre ? 'selected' : '' }}>
                                                    {{ $etiq }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cupo Máximo <span class="text-danger">*</span></label>
                                        <input type="number" name="cupo_maximo" class="form-control"
                                               value="{{ old('cupo_maximo', 30) }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Modalidad <span class="text-danger">*</span></label>
                                        <select name="modalidad" class="form-control" required>
                                            <option value="presencial" {{ old('modalidad','presencial') == 'presencial' ? 'selected' : '' }}>Presencial</option>
                                            <option value="virtual"    {{ old('modalidad') == 'virtual'    ? 'selected' : '' }}>Virtual</option>
                                            <option value="hibrida"    {{ old('modalidad') == 'hibrida'    ? 'selected' : '' }}>Híbrida</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha de Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" class="form-control"
                                               value="{{ old('fecha_inicio') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha de Fin <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin" class="form-control"
                                               value="{{ old('fecha_fin') }}" required>
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
                                                <option value="{{ $ub->id_ubicacion }}"
                                                    {{ old('id_ubicacion') == $ub->id_ubicacion ? 'selected' : '' }}>
                                                    {{ $ub->espacio }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Materiales Requeridos</label>
                                        <input type="text" name="materiales_requeridos" class="form-control"
                                               value="{{ old('materiales_requeridos') }}"
                                               placeholder="Opcional (ej. ropa deportiva, laptop...)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Docente ───────────────────────────────────── --}}
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-chalkboard-teacher mr-2 text-primary"></i>Asignación de Docente</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="doc-asignar" name="modo_docente" value="asignar"
                                           class="custom-control-input" checked
                                           onchange="toggleDocente(this.value)">
                                    <label class="custom-control-label" for="doc-asignar">
                                        <i class="fas fa-check-circle text-success"></i> Asignar docente ahora
                                    </label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="doc-despues" name="modo_docente" value="despues"
                                           class="custom-control-input"
                                           onchange="toggleDocente(this.value)">
                                    <label class="custom-control-label" for="doc-despues">
                                        <i class="fas fa-clock text-warning"></i> Dejar sin asignar (agregar después)
                                    </label>
                                </div>
                            </div>

                            <div id="seccion-docente">
                                <div class="form-group mb-0">
                                    <label>Nombre del Docente</label>
                                    <select name="id_instructor" id="select-instructor" class="form-control select2">
                                        <option value="">-- Buscar y seleccionar docente --</option>
                                        @foreach($instructores as $ins)
                                            <option value="{{ $ins->id_instructor }}"
                                                {{ old('id_instructor') == $ins->id_instructor ? 'selected' : '' }}>
                                                {{ $ins->usuario->nombre_completo ?? 'Sin nombre' }}
                                                — {{ $ins->departamento->nombre ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div id="msg-sin-docente" class="alert alert-warning mb-0 d-none">
                                <i class="fas fa-exclamation-triangle"></i>
                                El grupo quedará <strong>sin docente asignado</strong>.
                                Podrás asignarlo después desde la lista de grupos.
                            </div>
                        </div>
                    </div>

                    {{-- ── Carreras Permitidas ───────────────────────── --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4><i class="fas fa-university mr-2 text-primary"></i>Carreras Permitidas</h4>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="selectAllCarreras()">Seleccionar todas</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="clearAllCarreras()">Ninguna</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                <i class="fas fa-info-circle"></i>
                                Selecciona las carreras cuyos alumnos podrán
                                <strong>ver e inscribirse</strong> en esta actividad.
                                Si no seleccionas ninguna, quedará abierta a todas.
                            </p>
                            <div class="row">
                                @foreach($carreras as $carrera)
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input carrera-check" type="checkbox"
                                               name="carreras_permitidas[]"
                                               value="{{ $carrera->id_carrera }}"
                                               id="carr_{{ $carrera->id_carrera }}"
                                               {{ in_array($carrera->id_carrera, old('carreras_permitidas', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="carr_{{ $carrera->id_carrera }}">
                                            {{ $carrera->nombre }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ── Horario Semanal (tabla estilo MindBox) ───── --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4><i class="fas fa-calendar-alt mr-2 text-primary"></i>Horario Semanal</h4>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="clearSchedule()">
                                <i class="fas fa-eraser"></i> Limpiar selección
                            </button>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                <i class="fas fa-hand-pointer"></i>
                                Haz clic en los bloques para marcar los horarios del grupo.
                                Puedes seleccionar varios bloques por día.
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
                                    <tbody id="schedule-body">
                                        {{-- Generado por JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <div class="schedule-legend mt-2">
                                <span class="legend-box active"></span> Seleccionado
                                <span class="legend-box inactive ml-2"></span> Disponible
                            </div>

                            {{-- Inputs ocultos generados dinámicamente --}}
                            <div id="horarios-inputs"></div>
                        </div>
                    </div>

                    {{-- ── Botones ───────────────────────────────────── --}}
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('coordinador.grupos') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Guardar Grupo
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
// ── Docente toggle ────────────────────────────────────────────────────────
function toggleDocente(val) {
    const seccion = document.getElementById('seccion-docente');
    const msg     = document.getElementById('msg-sin-docente');
    const select  = document.getElementById('select-instructor');
    if (val === 'asignar') {
        seccion.classList.remove('d-none');
        msg.classList.add('d-none');
    } else {
        seccion.classList.add('d-none');
        msg.classList.remove('d-none');
        if (select) select.value = '';
    }
}

// ── Carreras ──────────────────────────────────────────────────────────────
function selectAllCarreras() {
    document.querySelectorAll('.carrera-check').forEach(c => c.checked = true);
}
function clearAllCarreras() {
    document.querySelectorAll('.carrera-check').forEach(c => c.checked = false);
}

// ── Tabla de horario estilo MindBox ───────────────────────────────────────
const lunesId = @json($diasSemana->where('nombre_dia','lunes')->first()->id_dia ?? 1);
const martesId = @json($diasSemana->where('nombre_dia','martes')->first()->id_dia ?? 2);
const miercolesId = @json($diasSemana->where('nombre_dia','miercoles')->first()->id_dia ?? 3);
const juevesId = @json($diasSemana->where('nombre_dia','jueves')->first()->id_dia ?? 4);
const viernesId = @json($diasSemana->where('nombre_dia','viernes')->first()->id_dia ?? 5);
const sabadoId = @json($diasSemana->where('nombre_dia','sabado')->first()->id_dia ?? 6);

const DIAS = [
    { id: lunesId, nombre: 'Lunes'     },
    { id: martesId, nombre: 'Martes'    },
    { id: miercolesId, nombre: 'Miércoles' },
    { id: juevesId, nombre: 'Jueves'    },
    { id: viernesId, nombre: 'Viernes'   },
    { id: sabadoId, nombre: 'Sábado'    },
];

// Bloques de 30 min, 07:00 - 20:00
const TIMES = [];
for (let h = 7; h < 20; h++) {
    TIMES.push(`${String(h).padStart(2,'0')}:00`);
    TIMES.push(`${String(h).padStart(2,'0')}:30`);
}

// Estado: "diaIdx-timeIdx" => bool
let scheduleState = {};

function buildSchedule() {
    const tbody = document.getElementById('schedule-body');
    tbody.innerHTML = '';
    TIMES.forEach((t, ti) => {
        const endH = ti % 2 === 0
            ? `${String(parseInt(t)).padStart(2,'0')}:30`
            : `${String(parseInt(t) + 1).padStart(2,'0')}:00`;
        const tr = document.createElement('tr');

        // Celda de hora
        const tdTime = document.createElement('td');
        tdTime.className = 'time-col';
        tdTime.textContent = `${t} - ${endH}`;
        tr.appendChild(tdTime);

        // Celdas de días
        DIAS.forEach((dia, di) => {
            const td = document.createElement('td');
            td.className = 'slot';
            td.dataset.dia = di;
            td.dataset.time = ti;
            td.onclick = () => toggleSlot(di, ti);
            tr.appendChild(td);
        });

        tbody.appendChild(tr);
    });
}

function toggleSlot(di, ti) {
    const key = `${di}-${ti}`;
    scheduleState[key] = !scheduleState[key];
    const td = document.querySelector(`td.slot[data-dia="${di}"][data-time="${ti}"]`);
    if (td) td.classList.toggle('active', scheduleState[key]);
    updateHiddenInputs();
}

function clearSchedule() {
    scheduleState = {};
    document.querySelectorAll('td.slot.active').forEach(td => td.classList.remove('active'));
    updateHiddenInputs();
}

function updateHiddenInputs() {
    const container = document.getElementById('horarios-inputs');
    container.innerHTML = '';
    let idx = 0;
    TIMES.forEach((t, ti) => {
        const endMin = ti % 2 === 0 ? 30 : 0;
        const endHour = ti % 2 === 0 ? parseInt(t) : parseInt(t) + 1;
        const horaFin = `${String(endHour).padStart(2,'0')}:${String(endMin).padStart(2,'0')}`;

        DIAS.forEach((dia, di) => {
            const key = `${di}-${ti}`;
            if (scheduleState[key]) {
                container.insertAdjacentHTML('beforeend', `
                    <input type="hidden" name="horarios[${idx}][id_dia]"      value="${dia.id}">
                    <input type="hidden" name="horarios[${idx}][hora_inicio]" value="${t}">
                    <input type="hidden" name="horarios[${idx}][hora_fin]"    value="${horaFin}">
                `);
                idx++;
            }
        });
    });
}

// Precargar si hay errores de validación (old horarios)
@if(old('horarios'))
const oldHorarios = @json(old('horarios'));
oldHorarios.forEach(function(h) {
    const diaId = parseInt(h.id_dia);
    const di = DIAS.findIndex(function(d) { return d.id === diaId; });
    const horaInicio = h.hora_inicio ? h.hora_inicio.substring(0, 5) : '';
    const ti = TIMES.indexOf(horaInicio);
    if (di >= 0 && ti >= 0) {
        scheduleState[di + '-' + ti] = true;
    }
});
@endif

// Construir tabla al cargar y resaltar celdas preseleccionadas
document.addEventListener('DOMContentLoaded', function () {
    buildSchedule();
    Object.keys(scheduleState).forEach(function(key) {
        const parts = key.split('-');
        const di = parts[0];
        const ti = parts[1];
        const td = document.querySelector('td.slot[data-dia="' + di + '"][data-time="' + ti + '"]');
        if (td) td.classList.add('active');
    });
    updateHiddenInputs();
});
</script>
@endsection
