@extends('layouts.app')

@section('content')
<section class="section">

    <div class="section-header d-flex justify-content-between align-items-center">
        <h3 class="page__heading">
            <i class="fas fa-chalkboard-teacher mr-2"></i>Mis Grupos
        </h3>
        <small class="text-muted">
            {{ $instructor->departamento->nombre ?? '' }}
            @if($semestreActivo)
                &nbsp;·&nbsp;
                <span class="badge badge-success">
                    <i class="fas fa-calendar-check mr-1"></i>Periodo activo: {{ $semestreActivo->label }}
                </span>
            @else
                &nbsp;·&nbsp;
                <span class="badge badge-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Sin periodo activo
                </span>
            @endif
        </small>
    </div>

    <div class="section-body">

        {{-- Alertas flash --}}
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

        {{-- Aviso si no hay periodo activo --}}
        @if (!$semestreActivo)
            <div class="alert alert-warning">
                <i class="fas fa-lock mr-2"></i>
                <strong>Modo solo lectura.</strong>
                No hay un semestre activo. Puedes ver tus grupos y alumnos, pero no puedes calificar hasta que un administrador active un periodo.
            </div>
        @endif

        {{-- ── GRUPOS ──────────────────────────────────────────────────────── --}}
        @forelse ($grupos as $grupo)
        @php
            // Todos los alumnos inscritos (sin filtro)
            $todasInscripciones = $grupo->inscripciones;
            $totalInscritos     = $todasInscripciones->count();
            $calificados        = $todasInscripciones->filter(fn($i) => $i->calificaciones->count() > 0)->count();
            $pendientes         = $totalInscritos - $calificados;
            $pct                = $grupo->cupo_maximo > 0 ? round(($grupo->cupo_ocupado / $grupo->cupo_maximo) * 100) : 0;
            $colorBarra         = $pct >= 90 ? 'danger' : ($pct >= 60 ? 'warning' : 'success');
        @endphp

        <div class="card mb-4">
            {{-- Cabecera clicable --}}
            <div class="card-header d-flex justify-content-between align-items-center"
                 data-toggle="collapse" data-target="#grupo-{{ $grupo->id_grupo }}"
                 style="cursor:pointer;">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-book-open text-primary mr-2"></i>
                        {{ $grupo->actividad->nombre ?? 'Sin actividad' }}
                        <span class="badge badge-secondary ml-2">Grupo {{ $grupo->grupo }}</span>
                        <span class="badge badge-{{ $grupo->estatus === 'abierta' ? 'success' : ($grupo->estatus === 'cancelada' ? 'danger' : 'warning') }} ml-1">
                            {{ ucfirst($grupo->estatus) }}
                        </span>
                    </h5>
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        {{ $grupo->semestre->año ?? '—' }}-{{ $grupo->semestre->periodo ?? '—' }}
                        &nbsp;·&nbsp;
                        <i class="fas fa-laptop mr-1"></i>{{ ucfirst($grupo->modalidad) }}
                        &nbsp;·&nbsp;
                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $grupo->ubicacion->espacio ?? 'Virtual' }}
                        &nbsp;·&nbsp;
                        <i class="fas fa-calendar mr-1"></i>
                        {{ \Carbon\Carbon::parse($grupo->fecha_inicio)->format('d/m/Y') }} –
                        {{ \Carbon\Carbon::parse($grupo->fecha_fin)->format('d/m/Y') }}
                    </small>
                </div>

                <div class="text-right ml-3 flex-shrink-0" style="min-width:180px;">
                    <small class="text-muted">{{ $grupo->cupo_ocupado }}/{{ $grupo->cupo_maximo }} alumnos</small>
                    <div class="progress mt-1" style="height:7px;">
                        <div class="progress-bar bg-{{ $colorBarra }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <small class="mt-1 d-block">
                        @if ($pendientes > 0)
                            <span class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i>{{ $pendientes }} por calificar</span>
                        @else
                            <span class="text-success"><i class="fas fa-check-circle mr-1"></i>Todos calificados</span>
                        @endif
                    </small>
                    <i class="fas fa-chevron-down mt-1 text-muted chevron-icon"></i>
                </div>
            </div>

            {{-- Contenido colapsable --}}
            <div id="grupo-{{ $grupo->id_grupo }}" class="collapse">
                <div class="card-body pb-0">

                    {{-- Info adicional del grupo --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-clock text-info mr-1"></i> Horarios:</strong>
                            @forelse ($grupo->horarios as $horario)
                                <span class="badge badge-light border ml-1 px-2 py-1">
                                    {{ ucfirst($horario->dia->nombre_dia ?? '') }}
                                    {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} –
                                    {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                                </span>
                            @empty
                                <span class="text-muted ml-2">Sin horario registrado</span>
                            @endforelse
                        </div>
                        <div class="col-md-6">
                            @if ($grupo->materiales_requeridos)
                            <strong><i class="fas fa-box text-warning mr-1"></i> Materiales:</strong>
                            <small class="text-muted ml-1">{{ $grupo->materiales_requeridos }}</small>
                            @endif
                        </div>
                    </div>

                    {{-- ── Filtro de alumnos ──────────────────────────────── --}}
                    <div class="d-flex align-items-center mb-3">
                        <span class="mr-3 font-weight-bold text-muted small">
                            <i class="fas fa-filter mr-1"></i>Mostrar:
                        </span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button"
                                    class="btn btn-outline-secondary filtro-btn active"
                                    data-grupo="{{ $grupo->id_grupo }}"
                                    data-filtro="todos">
                                <i class="fas fa-users mr-1"></i>Todos
                                <span class="badge badge-secondary ml-1">{{ $totalInscritos }}</span>
                            </button>
                            <button type="button"
                                    class="btn btn-outline-warning filtro-btn"
                                    data-grupo="{{ $grupo->id_grupo }}"
                                    data-filtro="pendientes">
                                <i class="fas fa-exclamation-circle mr-1"></i>Sin calificar
                                <span class="badge badge-warning ml-1">{{ $pendientes }}</span>
                            </button>
                            <button type="button"
                                    class="btn btn-outline-success filtro-btn"
                                    data-grupo="{{ $grupo->id_grupo }}"
                                    data-filtro="calificados">
                                <i class="fas fa-check-circle mr-1"></i>Calificados
                                <span class="badge badge-success ml-1">{{ $calificados }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- ── TABLA COMPLETA DE ALUMNOS (todos) ──────────────── --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0" id="tabla-grupo-{{ $grupo->id_grupo }}">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Alumno</th>
                                    <th>No. Control</th>
                                    <th>Carrera</th>
                                    <th>Semestre</th>
                                    <th>Fecha Inscripción</th>
                                    <th>Estatus</th>
                                    <th>Calificación</th>
                                    <th>Observaciones</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Se muestran TODOS los alumnos inscritos --}}
                                @forelse ($todasInscripciones as $idx => $inscripcion)
                                @php
                                    $alumno       = $inscripcion->alumno;
                                    $usuario      = $alumno->usuario ?? null;
                                    $calificacion = $inscripcion->calificaciones->first();
                                    $tieneCalif   = $calificacion ? 'calificado' : 'pendiente';
                                @endphp
                                <tr class="{{ $calificacion ? '' : 'table-warning' }}"
                                    data-estado="{{ $tieneCalif }}">
                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <strong>
                                            {{ $usuario->nombre ?? '—' }}
                                            {{ $usuario->apellido_paterno ?? '' }}
                                            {{ $usuario->apellido_materno ?? '' }}
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border">
                                            {{ $usuario->num_control ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td><small>{{ $alumno->carrera->nombre ?? 'N/A' }}</small></td>
                                    <td class="text-center">{{ $alumno->semestre_cursando ?? '—' }}</td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->format('d/m/Y') }}</small>
                                    </td>
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
                                                @case('excelente')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-award mr-1"></i>Excelente
                                                    </span> @break
                                                @case('bueno')
                                                    <span class="badge badge-primary">
                                                        <i class="fas fa-thumbs-up mr-1"></i>Bueno
                                                    </span> @break
                                                @case('malo')
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-thumbs-down mr-1"></i>Malo
                                                    </span> @break
                                            @endswitch
                                        @else
                                            <span class="text-muted small">
                                                <i class="fas fa-minus-circle mr-1"></i>Sin calificar
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $calificacion->observaciones ?? '—' }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if ($semestreActivo)
                                            <a href="{{ route('instructor.calificar', $inscripcion->id_inscripcion) }}"
                                               class="btn btn-sm btn-{{ $calificacion ? 'warning' : 'primary' }}">
                                                <i class="fas fa-{{ $calificacion ? 'redo' : 'star' }} mr-1"></i>
                                                {{ $calificacion ? 'Recalificar' : 'Calificar' }}
                                            </a>
                                        @else
                                            <span class="btn btn-sm btn-secondary disabled">
                                                <i class="fas fa-lock mr-1"></i>Bloqueado
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr class="fila-vacia">
                                    <td colspan="10" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle mr-2"></i>No hay alumnos inscritos.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mensaje cuando el filtro no encuentra resultados --}}
                    <div class="text-center text-muted py-3 d-none filtro-sin-resultados"
                         id="sin-resultados-{{ $grupo->id_grupo }}">
                        <i class="fas fa-search mr-2"></i>No hay alumnos en esta categoría.
                    </div>

                </div>

                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-users mr-1"></i>{{ $totalInscritos }} inscritos
                        &nbsp;·&nbsp;
                        <i class="fas fa-check-circle text-success mr-1"></i>{{ $calificados }} calificados
                        &nbsp;·&nbsp;
                        <i class="fas fa-exclamation-circle text-warning mr-1"></i>{{ $pendientes }} pendientes
                    </small>
                    <small class="text-muted">
                        {{ $grupo->actividad->creditos ?? '—' }} crédito(s)
                        &nbsp;·&nbsp;
                        {{ $grupo->actividad->nivel_actividad ?? '' }}
                    </small>
                </div>
            </div>
        </div>
        @empty
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle mr-2"></i>
                No tienes grupos asignados por el momento.
            </div>
        @endforelse

    </div>
</section>
@endsection

@section('scripts')
<script>
    // ── Chevron animado al colapsar/expandir ─────────────────────────────────
    document.querySelectorAll('[data-toggle="collapse"]').forEach(function (header) {
        const targetId = header.dataset.target;
        const target   = document.querySelector(targetId);
        const chevron  = header.querySelector('.chevron-icon');
        if (!target || !chevron) return;

        target.addEventListener('show.bs.collapse',  () => chevron.style.transform = 'rotate(180deg)');
        target.addEventListener('hide.bs.collapse',  () => chevron.style.transform = 'rotate(0deg)');
    });

    // ── Filtro de alumnos (Todos / Sin calificar / Calificados) ─────────────
    document.querySelectorAll('.filtro-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const grupoId  = this.dataset.grupo;
            const filtro   = this.dataset.filtro;
            const tabla    = document.getElementById('tabla-grupo-' + grupoId);
            const sinRes   = document.getElementById('sin-resultados-' + grupoId);

            // Actualizar botón activo dentro de este grupo
            const grupoBtns = document.querySelectorAll('.filtro-btn[data-grupo="' + grupoId + '"]');
            grupoBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Mostrar/ocultar filas según filtro
            const filas = tabla.querySelectorAll('tbody tr[data-estado]');
            let visibles = 0;

            filas.forEach(function (fila) {
                const estado = fila.dataset.estado; // 'calificado' | 'pendiente'
                let mostrar = false;

                if (filtro === 'todos') {
                    mostrar = true;
                } else if (filtro === 'pendientes' && estado === 'pendiente') {
                    mostrar = true;
                } else if (filtro === 'calificados' && estado === 'calificado') {
                    mostrar = true;
                }

                fila.style.display = mostrar ? '' : 'none';
                if (mostrar) visibles++;
            });

            // Mostrar mensaje si no hay resultados en el filtro activo
            if (sinRes) {
                sinRes.classList.toggle('d-none', visibles > 0 || filtro === 'todos');
            }
        });
    });
</script>
@endsection
