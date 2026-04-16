@extends('layouts.app')
@section('title', 'Editar Periodo Escolar')

@section('page_css')
<style>
.section-label { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.regla-hint { font-size:12px; color:#6c757d; margin-top:4px; }
.regla-hint.ok  { color:#28a745; }
.regla-hint.err { color:#dc3545; }
</style>
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Gestión de Semestres</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-9 offset-lg-1">
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-edit mr-2 text-primary"></i>
                            Editar Periodo Escolar
                        </h4>
                        @if($semestre->status === 'inactivo')
                            <span class="badge badge-warning ml-2">Reactivación del período más reciente</span>
                        @endif
                    </div>
                    <div class="card-body">

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger"><strong>{{ session('error') }}</strong></div>
                        @endif

                        @if($semestre->status === 'inactivo')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Estás editando un periodo <strong>inactivo</strong>.
                                Cambia el estatus a <strong>Activo</strong> para reactivarlo.
                            </div>
                        @endif

                        <form action="{{ route('admin.semestres.update', $semestre->id_semestre) }}" method="POST" id="form-semestre">
                            @csrf
                            @method('PUT')

                            {{-- Año, Periodo, Estatus --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Año <span class="text-danger">*</span></label>
                                        <input type="number" name="año" id="input_año" class="form-control"
                                               value="{{ old('año', $semestre->año) }}" min="2000" max="2100" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Periodo <span class="text-danger">*</span></label>
                                        <select name="periodo" id="select_periodo" class="form-control" required>
                                            <option value="1" {{ old('periodo',$semestre->periodo)=='1'?'selected':'' }}>1 – Enero / Junio</option>
                                            <option value="2" {{ old('periodo',$semestre->periodo)=='2'?'selected':'' }}>2 – Agosto / Diciembre</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Estatus <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="activo"   {{ old('status',$semestre->status)=='activo'  ?'selected':'' }}>Activo</option>
                                            <option value="inactivo" {{ old('status',$semestre->status)=='inactivo'?'selected':'' }}>Inactivo (Finalizar)</option>
                                        </select>
                                        <small class="text-muted">Cambia a "Inactivo" para cerrar el periodo.</small>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            {{-- Fechas del Semestre --}}
                            <h6 class="text-primary section-label"><i class="fas fa-calendar-alt mr-1"></i> Fechas del Semestre</h6>
                            <p class="regla-hint"><i class="fas fa-info-circle"></i> Duración entre 3 y 5 meses.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                                               class="form-control date-restrict"
                                               value="{{ old('fecha_inicio', $semestre->fecha_inicio) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Fin <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin" id="fecha_fin"
                                               class="form-control date-restrict"
                                               value="{{ old('fecha_fin', $semestre->fecha_fin) }}" required>
                                        <small id="hint-semestre" class="regla-hint"></small>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            {{-- Período de Inscripciones --}}
                            <h6 class="text-success section-label"><i class="fas fa-clipboard-list mr-1"></i> Período de Inscripciones</h6>
                            <p class="regla-hint"><i class="fas fa-info-circle"></i> Duración entre 5 y 10 días.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Inicio de Inscripciones <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio_inscripciones" id="fecha_ini_insc"
                                               class="form-control date-restrict"
                                               value="{{ old('fecha_inicio_inscripciones', $semestre->fecha_inicio_inscripciones) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Fin de Inscripciones <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin_inscripciones" id="fecha_fin_insc"
                                               class="form-control date-restrict"
                                               value="{{ old('fecha_fin_inscripciones', $semestre->fecha_fin_inscripciones) }}" required>
                                        <small id="hint-inscripcion" class="regla-hint"></small>
                                    </div>
                                </div>
                            </div>

                            {{-- Horas de Inscripciones --}}
                            <div class="row mt-1">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Hora de Inicio de Inscripciones <span class="text-danger">*</span></label>
                                        <input type="time" name="hora_inicio_inscripciones"
                                               class="form-control"
                                               value="{{ old('hora_inicio_inscripciones', $semestre->hora_inicio_inscripciones ? substr($semestre->hora_inicio_inscripciones,0,5) : '08:00') }}"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Hora de Fin de Inscripciones <span class="text-danger">*</span></label>
                                        <input type="time" name="hora_fin_inscripciones"
                                               class="form-control"
                                               value="{{ old('hora_fin_inscripciones', $semestre->hora_fin_inscripciones ? substr($semestre->hora_fin_inscripciones,0,5) : '20:00') }}"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.semestres.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Actualizar Semestre
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputAño     = document.getElementById('input_año');
    const selPeriodo   = document.getElementById('select_periodo');
    const fechaInicio  = document.getElementById('fecha_inicio');
    const fechaFin     = document.getElementById('fecha_fin');
    const fechaIniInsc = document.getElementById('fecha_ini_insc');
    const fechaFinInsc = document.getElementById('fecha_fin_insc');
    const hintSem      = document.getElementById('hint-semestre');
    const hintInsc     = document.getElementById('hint-inscripcion');

    function actualizarLimites() {
        const año = inputAño.value;
        const per = selPeriodo.value;
        if (!año || año.length !== 4 || !per) return;

        const minDate = per == '1' ? `${año}-01-01` : `${año}-08-01`;
        const maxDate = per == '1' ? `${año}-06-30` : `${año}-12-31`;

        document.querySelectorAll('.date-restrict').forEach(function(inp) {
            inp.min = minDate;
            inp.max = maxDate;
            if (inp.value && (inp.value < minDate || inp.value > maxDate)) inp.value = '';
        });
        validarSemestre();
        validarInscripciones();
    }

    function validarSemestre() {
        if (!fechaInicio.value || !fechaFin.value) { hintSem.textContent = ''; return; }
        const d1    = new Date(fechaInicio.value);
        const d2    = new Date(fechaFin.value);
        const meses = (d2.getFullYear() - d1.getFullYear()) * 12 + (d2.getMonth() - d1.getMonth());
        if (meses < 3) {
            hintSem.className = 'regla-hint err';
            hintSem.textContent = `⚠ Duración: ${meses} mes(es). Mínimo 3 meses.`;
        } else if (meses > 5) {
            hintSem.className = 'regla-hint err';
            hintSem.textContent = `⚠ Duración: ${meses} meses. Máximo 5 meses.`;
        } else {
            hintSem.className = 'regla-hint ok';
            hintSem.textContent = `✓ Duración: ${meses} mes(es). Válido.`;
        }
    }

    function validarInscripciones() {
        if (!fechaIniInsc.value || !fechaFinInsc.value) { hintInsc.textContent = ''; return; }
        const d1   = new Date(fechaIniInsc.value);
        const d2   = new Date(fechaFinInsc.value);
        const dias = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
        if (dias < 5) {
            hintInsc.className = 'regla-hint err';
            hintInsc.textContent = `⚠ ${dias} día(s). Mínimo 5 días.`;
        } else if (dias > 10) {
            hintInsc.className = 'regla-hint err';
            hintInsc.textContent = `⚠ ${dias} días. Máximo 10 días.`;
        } else {
            hintInsc.className = 'regla-hint ok';
            hintInsc.textContent = `✓ ${dias} día(s). Válido.`;
        }
    }

    inputAño.addEventListener('input', actualizarLimites);
    selPeriodo.addEventListener('change', actualizarLimites);
    fechaInicio.addEventListener('change', validarSemestre);
    fechaFin.addEventListener('change', validarSemestre);
    fechaIniInsc.addEventListener('change', validarInscripciones);
    fechaFinInsc.addEventListener('change', validarInscripciones);

    actualizarLimites();
    validarSemestre();
    validarInscripciones();
});
</script>
@endsection
