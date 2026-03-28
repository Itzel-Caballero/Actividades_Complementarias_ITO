@extends('layouts.app')
@section('title', 'Editar Grupo')

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
    text-align: center;
    vertical-align: middle;
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
.semestre-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:20px; font-size:13px; font-weight:700; }
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
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <form action="{{ route('coordinador.grupos.update', $grupo->id_grupo) }}"
                      method="POST" id="form-grupo">
                    @csrf
                    @method('PUT')

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
                                            @foreach($actividades as $act)
                                                <option value="{{ $act->id_actividad }}"
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
                                        <label>Semestre <span class="text-danger">*</span>
                                            <span class="semestre-badge ml-1 {{ $semestreActual['clase'] }}">
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
                                                    {{ old('id_semestre', $grupo->id_semestre) == $sem->id_semestre ? 'selected' : '' }}>
                                                    {{ $etiq }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Cupo Máximo <span class="text-danger">*</span></label>
                                        <input type="number" name="cupo_maximo" class="form-control"
                                               value="{{ old('cupo_maximo', $grupo->cupo_maximo) }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Modalidad <span class="text-danger">*</span></label>
                                        <select name="modalidad" class="form-control" required>
                                            @foreach(['presencial', 'virtual', 'hibrida'] as $mod)
                                                <option value="{{ $mod }}"
                                                    {{ old('modalidad', $grupo->modalidad) == $mod ? 'selected' : '' }}>
                                                    {{ ucfirst($mod) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Estatus</label>
                                        <select name="estatus" class="form-control">
                                            @foreach(['abierta', 'cerrada', 'cancelada', 'finalizada'] as $est)
                                                <option value="{{ $est }}"
                                                    {{ old('estatus', $grupo->estatus) == $est ? 'selected' : '' }}>
                                                    {{ ucfirst($est) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Fecha Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" class="form-control"
                                               value="{{ old('fecha_inicio', $grupo->fecha_inicio) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha Fin <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin" class="form-control"
                                               value="{{ old('fecha_fin', $grupo->fecha_fin) }}" required>
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
                                                    {{ old('id_ubicacion', $grupo->id_ubicacion) == $ub->id_ubicacion ? 'selected' : '' }}>
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
                                               value="{{ old('materiales_requeridos', $grupo->materiales_requeridos) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Docente ───────────────────────────────────── --}}
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-chalkboard-teacher mr-2 text-primary"></i>Docente Asignado</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label>Nombre del Docente</label>
                                <select name="id_instructor" class="form-control select2">
                                    <option value="">-- Sin docente asignado --</option>
                                    @foreach($instructores as $ins)
                                        <option value="{{ $ins->id_instructor }}"
                                            {{ old('id_instructor', $grupo->id_instructor) == $ins->id_instructor ? 'selected' : '' }}>
                                            {{ $ins->usuario->nombre_completo ?? 'Sin nombre' }}
                                            — {{ $ins->departamento->nombre ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Selecciona "Sin docente asignado" para quitar la asignación.</small>
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
                                Haz clic en los bloques para marcar o desmarcar los horarios.
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
                            <div class="schedule-legend mt-2">
                                <span class="legend-box active"></span> Seleccionado
                                <span class="legend-box inactive ml-2"></span> Disponible
                            </div>
                            <div id="horarios-inputs"></div>
                        </div>
                    </div>

                    {{-- ── Botones ───────────────────────────────────── --}}
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('coordinador.grupos') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Actualizar Grupo
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
// Horarios existentes del grupo para precargar
const horariosExistentes = @json($grupo->horarios->map(fn($h) => [
    'id_dia'      => $h->id_dia,
    'hora_inicio' => substr($h->hora_inicio, 0, 5),
    'hora_fin'    => substr($h->hora_fin, 0, 5),
]));

const DIAS = [
    { id: @json($diasSemana->where('nombre_dia','lunes')->first()->id_dia    ?? 1) },
    { id: @json($diasSemana->where('nombre_dia','martes')->first()->id_dia   ?? 2) },
    { id: @json($diasSemana->where('nombre_dia','miercoles')->first()->id_dia ?? 3) },
    { id: @json($diasSemana->where('nombre_dia','jueves')->first()->id_dia   ?? 4) },
    { id: @json($diasSemana->where('nombre_dia','viernes')->first()->id_dia  ?? 5) },
    { id: @json($diasSemana->where('nombre_dia','sabado')->first()->id_dia   ?? 6) },
];

const TIMES = [];
for (let h = 7; h < 20; h++) {
    TIMES.push(`${String(h).padStart(2,'0')}:00`);
    TIMES.push(`${String(h).padStart(2,'0')}:30`);
}

let scheduleState = {};

function buildSchedule() {
    const tbody = document.getElementById('schedule-body');
    tbody.innerHTML = '';
    TIMES.forEach((t, ti) => {
        const endMin  = ti % 2 === 0 ? 30 : 0;
        const endHour = ti % 2 === 0 ? parseInt(t) : parseInt(t) + 1;
        const endT    = `${String(endHour).padStart(2,'0')}:${String(endMin).padStart(2,'0')}`;
        const tr = document.createElement('tr');

        const tdTime = document.createElement('td');
        tdTime.className = 'time-col';
        tdTime.textContent = `${t} - ${endT}`;
        tr.appendChild(tdTime);

        DIAS.forEach((dia, di) => {
            const td = document.createElement('td');
            td.className = 'slot';
            td.dataset.dia  = di;
            td.dataset.time = ti;
            td.onclick = () => toggleSlot(di, ti);

            // Precargar horarios existentes
            const key = `${di}-${ti}`;
            if (scheduleState[key]) td.classList.add('active');

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
        const endMin  = ti % 2 === 0 ? 30 : 0;
        const endHour = ti % 2 === 0 ? parseInt(t) : parseInt(t) + 1;
        const horaFin = `${String(endHour).padStart(2,'0')}:${String(endMin).padStart(2,'0')}`;
        DIAS.forEach((dia, di) => {
            if (scheduleState[`${di}-${ti}`]) {
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

// Precargar horarios existentes del grupo en el estado
horariosExistentes.forEach(h => {
    const di = DIAS.findIndex(d => d.id === h.id_dia);
    const ti = TIMES.indexOf(h.hora_inicio);
    if (di >= 0 && ti >= 0) {
        scheduleState[`${di}-${ti}`] = true;
    }
});

document.addEventListener('DOMContentLoaded', function () {
    buildSchedule();
    updateHiddenInputs();
});
</script>
@endsection
