@extends('layouts.app')

@php
    $todasInscripciones = $grupo->inscripciones;
    $totalInscritos     = $todasInscripciones->count();
    $calificados        = $todasInscripciones->filter(fn($i) => $i->calificaciones->count() > 0)->count();
    $pendientes         = $totalInscritos - $calificados;
    $pct                = $grupo->cupo_maximo > 0 ? round(($grupo->cupo_ocupado / $grupo->cupo_maximo) * 100) : 0;
    $colFill            = $pct >= 90 ? '#fc544b' : ($pct >= 60 ? '#ffa426' : '#47c363');
    $creditos           = $grupo->actividad->creditos ?? 1;
    $estatusColors      = ['abierta' => '#47c363', 'cerrada' => '#ffa426', 'cancelada' => '#fc544b'];
@endphp

@section('css')
<style>
    /* ── Barra compacta de resumen ── */
    .gd-summary-bar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        background: #fff;
        border: 1px solid #e3eaef;
        border-radius: 8px;
        padding: 14px 22px;
        margin-bottom: 20px;
    }
    .gd-summary-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: .85rem;
        color: #34395e;
    }
    .gd-sum-num {
        font-size: 1.3rem;
        font-weight: 800;
        line-height: 1;
    }
    .gd-sum-label {
        font-size: .72rem;
        color: #98a6ad;
        line-height: 1.3;
    }
    .gd-summary-divider {
        width: 1px;
        height: 36px;
        background: #e3eaef;
        flex-shrink: 0;
    }
    .gd-prog-bar  { height: 7px; width: 90px; border-radius: 4px; background: #e3eaef; overflow: hidden; margin-top: 4px; }
    .gd-prog-fill { height: 100%; border-radius: 4px; }

    /* ── Info del grupo ── */
    .gd-info-card {
        background: #fff;
        border: 1px solid #e3eaef;
        border-radius: 8px;
        padding: 20px 24px;
        margin-bottom: 20px;
    }
    .gd-sec-label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #4e38a4;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 2px solid #ede9f9;
    }

    /* ── Tabla de alumnos ── */
    .gd-table-card {
        background: #fff;
        border: 1px solid #e3eaef;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .gd-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 14px 20px;
        border-bottom: 1px solid #e3eaef;
        background: #f9f9f9;
    }
    .gd-table-card .table thead th {
        background: #f9f9f9;
        font-size: .76rem;
        font-weight: 700;
        color: #6c757d;
        border-bottom: 2px solid #e3eaef;
        vertical-align: middle;
    }
    .gd-table-card .table tbody td { font-size: .82rem; vertical-align: middle; }

    /* ── Footer de acciones ── */
    .gd-footer-actions {
        background: #fff;
        border: 1px solid #e3eaef;
        border-radius: 8px;
        padding: 14px 22px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
</style>
@endsection

@section('content')
<section class="section">

    {{-- Encabezado nativo Stisla --}}
    <div class="section-header">
        <h1>
            <i class="fas fa-book-open mr-2"></i>
            {{ $grupo->actividad->nombre ?? 'Sin actividad' }}
            <span class="badge badge-light border ml-2"
                  style="font-size:.72rem; font-weight:600; vertical-align:middle;">
                Grupo {{ $grupo->grupo }}
            </span>
        </h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item">
                <a href="{{ route('instructor.mis-grupos') }}">
                    <i class="fas fa-arrow-left mr-1"></i>Mis Grupos
                </a>
            </div>
            <div class="breadcrumb-item active">
                {{ $grupo->actividad->nombre ?? 'Detalle' }}
            </div>
        </div>
    </div>

    <div class="section-body">

        {{-- Flash --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if (!$semestreActivo)
            <div class="alert alert-warning">
                <i class="fas fa-lock mr-2"></i>
                <strong>Modo solo lectura.</strong>
                No hay un semestre activo. Las calificaciones están bloqueadas.
            </div>
        @endif

        {{-- ── BARRA DE RESUMEN COMPACTA ── --}}
        <div class="gd-summary-bar">
            <div class="gd-summary-item">
                <i class="fas fa-users fa-lg text-primary"></i>
                <div>
                    <div class="gd-sum-num">{{ $totalInscritos }}</div>
                    <div class="gd-sum-label">Inscritos</div>
                </div>
            </div>
            <div class="gd-summary-divider"></div>
            <div class="gd-summary-item">
                <i class="fas fa-check-circle fa-lg" style="color:#47c363;"></i>
                <div>
                    <div class="gd-sum-num" style="color:#47c363;">{{ $calificados }}</div>
                    <div class="gd-sum-label">Calificados</div>
                </div>
            </div>
            <div class="gd-summary-divider"></div>
            <div class="gd-summary-item">
                <i class="fas fa-exclamation-circle fa-lg" style="color:#ffa426;"></i>
                <div>
                    <div class="gd-sum-num" style="color:#ffa426;">{{ $pendientes }}</div>
                    <div class="gd-sum-label">Sin calificar</div>
                </div>
            </div>
            <div class="gd-summary-divider"></div>
            <div class="gd-summary-item">
                <i class="fas fa-award fa-lg" style="color:#3abaf4;"></i>
                <div>
                    <div class="gd-sum-num">{{ $creditos }}</div>
                    <div class="gd-sum-label">Crédito(s)</div>
                </div>
            </div>
            <div class="gd-summary-divider"></div>
            <div class="gd-summary-item">
                <i class="fas fa-users-cog fa-lg text-secondary"></i>
                <div>
                    <div class="gd-sum-num">
                        {{ $grupo->cupo_ocupado }}<span style="font-size:.85rem; color:#98a6ad; font-weight:500;">/{{ $grupo->cupo_maximo }}</span>
                    </div>
                    <div class="gd-sum-label">Capacidad</div>
                    <div class="gd-prog-bar">
                        <div class="gd-prog-fill" style="width:{{ $pct }}%; background:{{ $colFill }};"></div>
                    </div>
                </div>
            </div>
            {{-- Pills de info del grupo --}}
            <div class="gd-summary-divider"></div>
            <div style="display:flex; flex-wrap:wrap; gap:6px; align-items:center;">
                <span class="badge badge-light border px-2 py-1" style="font-size:.75rem;">
                    <i class="fas fa-calendar-alt mr-1 text-primary"></i>
                    {{ $grupo->semestre->año ?? '—' }}-{{ $grupo->semestre->periodo ?? '' }}
                </span>
                <span class="badge badge-light border px-2 py-1" style="font-size:.75rem;">
                    <i class="fas fa-laptop mr-1 text-primary"></i>{{ ucfirst($grupo->modalidad) }}
                </span>
                <span class="badge badge-light border px-2 py-1" style="font-size:.75rem;">
                    <i class="fas fa-map-marker-alt mr-1 text-primary"></i>{{ $grupo->ubicacion->espacio ?? 'Virtual' }}
                </span>
                <span class="badge badge-light border px-2 py-1" style="font-size:.75rem;">
                    <i class="fas fa-circle mr-1" style="font-size:.45rem; color:{{ $estatusColors[$grupo->estatus] ?? '#ccc' }};"></i>
                    {{ ucfirst($grupo->estatus) }}
                </span>
            </div>
        </div>

        {{-- ── INFORMACIÓN DEL GRUPO ── --}}
        <div class="gd-info-card">
            <div class="row">
                <div class="col-md-4">
                    <p class="gd-sec-label"><i class="fas fa-clock mr-1"></i>Horarios</p>
                    @forelse ($grupo->horarios as $h)
                        <span class="badge badge-light border px-2 py-1 mr-1 mb-1" style="font-size:.8rem;">
                            {{ ucfirst($h->dia->nombre_dia ?? '') }}
                            {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} –
                            {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                        </span>
                    @empty
                        <span class="text-muted small">Sin horario registrado</span>
                    @endforelse
                </div>
                <div class="col-md-4">
                    <p class="gd-sec-label"><i class="fas fa-layer-group mr-1"></i>Nivel &amp; Créditos</p>
                    <p class="mb-1" style="font-size:.84rem;"><strong>Nivel:</strong> {{ $grupo->actividad->nivel_actividad ?? '—' }}</p>
                    <p class="mb-0" style="font-size:.84rem;"><strong>Créditos:</strong> {{ $creditos }}</p>
                </div>
                <div class="col-md-4">
                    @if($grupo->materiales_requeridos)
                        <p class="gd-sec-label"><i class="fas fa-box mr-1"></i>Materiales</p>
                        <p class="text-muted" style="font-size:.84rem;">{{ $grupo->materiales_requeridos }}</p>
                    @else
                        <p class="gd-sec-label"><i class="fas fa-building mr-1"></i>Departamento</p>
                        <p class="text-muted" style="font-size:.84rem;">{{ $instructor->departamento->nombre ?? 'Sin departamento' }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── TABLA DE ALUMNOS ── --}}
        <div class="gd-table-card">
            <div class="gd-table-toolbar">
                <p class="gd-sec-label mb-0"><i class="fas fa-users mr-1"></i>Lista de alumnos</p>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <input type="text" id="buscador-alumnos" class="form-control form-control-sm"
                           placeholder="Buscar por nombre o no. control…" style="width:230px;">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary filtro-btn active" data-filtro="todos">
                            <i class="fas fa-users mr-1"></i>Todos
                            <span class="badge badge-secondary ml-1">{{ $totalInscritos }}</span>
                        </button>
                        <button type="button" class="btn btn-outline-warning filtro-btn" data-filtro="pendientes">
                            <i class="fas fa-exclamation-circle mr-1"></i>Sin calificar
                            <span class="badge badge-warning ml-1">{{ $pendientes }}</span>
                        </button>
                        <button type="button" class="btn btn-outline-success filtro-btn" data-filtro="calificados">
                            <i class="fas fa-check-circle mr-1"></i>Calificados
                            <span class="badge badge-success ml-1">{{ $calificados }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" id="tabla-alumnos">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Alumno</th>
                            <th>No. Control</th>
                            <th>Carrera</th>
                            <th>Sem.</th>
                            <th>Inscripción</th>
                            <th>Estatus</th>
                            <th>Calificación</th>
                            <th>Observaciones</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($todasInscripciones as $idx => $inscripcion)
                        @php
                            $alumno         = $inscripcion->alumno;
                            $usuario        = $alumno->usuario ?? null;
                            $calificacion   = $inscripcion->calificaciones->first();
                            $tieneCalif     = $calificacion ? 'calificado' : 'pendiente';
                            $nombreCompleto = trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido_paterno ?? '') . ' ' . ($usuario->apellido_materno ?? ''));
                        @endphp
                        <tr class="{{ $calificacion ? '' : 'table-warning' }}"
                            data-estado="{{ $tieneCalif }}"
                            data-nombre="{{ strtolower($nombreCompleto) }}"
                            data-control="{{ strtolower($usuario->num_control ?? '') }}">
                            <td class="text-muted">{{ $idx + 1 }}</td>
                            <td><strong>{{ $nombreCompleto ?: '—' }}</strong></td>
                            <td><span class="badge badge-light border">{{ $usuario->num_control ?? 'N/A' }}</span></td>
                            <td><small>{{ $alumno->carrera->nombre ?? 'N/A' }}</small></td>
                            <td class="text-center">{{ $alumno->semestre_cursando ?? '—' }}</td>
                            <td><small>{{ \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->format('d/m/Y') }}</small></td>
                            <td>
                                @switch($inscripcion->estatus)
                                    @case('inscrito')  <span class="badge badge-secondary">Inscrito</span>  @break
                                    @case('cursando')  <span class="badge badge-info">Cursando</span>       @break
                                    @case('aprobado')  <span class="badge badge-success">Aprobado</span>    @break
                                    @case('reprobado') <span class="badge badge-danger">Reprobado</span>    @break
                                    @default           <span class="badge badge-warning">{{ ucfirst($inscripcion->estatus) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if ($calificacion)
                                    @switch($calificacion->desempenio)
                                        @case('excelente') <span class="badge badge-success"><i class="fas fa-award mr-1"></i>Excelente</span> @break
                                        @case('bueno')     <span class="badge badge-primary"><i class="fas fa-thumbs-up mr-1"></i>Bueno</span> @break
                                        @case('malo')      <span class="badge badge-danger"><i class="fas fa-thumbs-down mr-1"></i>Malo</span> @break
                                    @endswitch
                                @else
                                    <span class="text-muted small"><i class="fas fa-minus-circle mr-1"></i>Sin calificar</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $calificacion->observaciones ?? '—' }}</small></td>
                            <td class="text-center">
                                @if ($semestreActivo)
                                    <a href="{{ route('instructor.calificar', $inscripcion->id_inscripcion) }}"
                                       class="btn btn-sm btn-{{ $calificacion ? 'warning' : 'primary' }}">
                                        <i class="fas fa-{{ $calificacion ? 'redo' : 'star' }} mr-1"></i>{{ $calificacion ? 'Recalificar' : 'Calificar' }}
                                    </a>
                                @else
                                    <span class="btn btn-sm btn-secondary disabled">
                                        <i class="fas fa-lock mr-1"></i>Bloqueado
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle mr-2"></i>No hay alumnos inscritos en este grupo.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="text-center text-muted py-3 d-none" id="sin-resultados">
                <i class="fas fa-search mr-2"></i>No hay alumnos en esta categoría.
            </div>
        </div>

        {{-- ── ACCIONES ── --}}
        <div class="gd-footer-actions">
            <a href="{{ route('instructor.mis-grupos') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Volver
            </a>
            <a href="{{ route('instructor.descargar-lista', $grupo->id_grupo) }}" class="btn btn-outline-success">
                <i class="fas fa-file-download mr-1"></i>Descargar CSV
            </a>
            <a href="{{ route('instructor.lista-asistencia-pdf', $grupo->id_grupo) }}" class="btn btn-outline-danger">
                <i class="fas fa-file-pdf mr-1"></i>Lista PDF
            </a>
        </div>

    </div>{{-- /section-body --}}
</section>
@endsection

@section('scripts')
<script>
    var filtroActual = 'todos';
    var busqueda     = '';

    function aplicarFiltros() {
        var filas    = document.querySelectorAll('#tabla-alumnos tbody tr[data-estado]');
        var sinRes   = document.getElementById('sin-resultados');
        var visibles = 0;

        filas.forEach(function (fila) {
            var estado  = fila.dataset.estado;
            var nombre  = fila.dataset.nombre  || '';
            var control = fila.dataset.control || '';

            var pasaFiltro   = filtroActual === 'todos' ||
                               (filtroActual === 'pendientes'  && estado === 'pendiente') ||
                               (filtroActual === 'calificados' && estado === 'calificado');
            var pasaBusqueda = busqueda === '' ||
                               nombre.indexOf(busqueda)  !== -1 ||
                               control.indexOf(busqueda) !== -1;

            var mostrar = pasaFiltro && pasaBusqueda;
            fila.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        if (sinRes) sinRes.classList.toggle('d-none', visibles > 0);
    }

    document.querySelectorAll('.filtro-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filtro-btn').forEach(function(b){ b.classList.remove('active'); });
            this.classList.add('active');
            filtroActual = this.dataset.filtro;
            aplicarFiltros();
        });
    });

    var buscador = document.getElementById('buscador-alumnos');
    if (buscador) {
        buscador.addEventListener('input', function () {
            busqueda = this.value.toLowerCase().trim();
            aplicarFiltros();
        });
    }
</script>
@endsection
