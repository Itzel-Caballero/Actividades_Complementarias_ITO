@extends('layouts.app')

@section('css')
<style>
    /* ── Página ───────────────────────────────────── */
    /* (el encabezado ahora usa .section-header nativo de Stisla) */
    .mg-periodo-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eaf4ea;
        color: #2e7d32;
        border-radius: 20px;
        font-size: .78rem;
        font-weight: 600;
        padding: 3px 12px;
    }
    .mg-periodo-badge.sin {
        background: #fff8e1;
        color: #f57f17;
    }

    /* ── Grid de cards ────────────────────────────── */
    .mg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 22px;
    }

    /* ── Card individual ──────────────────────────── */
    .mg-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
        border: 1px solid #e8eaf0;
        display: flex;
        flex-direction: column;
        transition: box-shadow .18s, transform .18s;
        overflow: hidden;
        cursor: pointer;
    }
    .mg-card:hover {
        box-shadow: 0 6px 22px rgba(0,0,0,.12);
        transform: translateY(-3px);
    }
    .mg-card-body {
        padding: 20px 22px 16px;
        flex: 1;
    }
    /* fila superior: título + badge créditos */
    .mg-top-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 4px;
    }
    .mg-actividad-nombre {
        font-size: 1.15rem;
        font-weight: 700;
        color: #23263b;
        line-height: 1.3;
        flex: 1;
        word-break: break-word;
    }
    .mg-creditos-badge {
        flex-shrink: 0;
        background: #3d5afe;
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
        border-radius: 20px;
        padding: 4px 12px;
        white-space: nowrap;
    }
    .mg-creditos-badge.c1 { background: #00bcd4; }
    .mg-creditos-badge.c2 { background: #4caf50; }
    .mg-creditos-badge.c3 { background: #ff9800; }
    /* subtexto */
    .mg-semestre {
        font-size: .82rem;
        color: #9e9e9e;
        margin-bottom: 14px;
    }
    /* lista de info */
    .mg-info-list {
        list-style: none;
        padding: 0;
        margin: 0 0 14px;
    }
    .mg-info-list li {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .84rem;
        color: #3d3d3d;
        padding: 3px 0;
    }
    .mg-info-list li i {
        width: 16px;
        text-align: center;
        color: #7b7b8d;
        font-size: .82rem;
    }
    .mg-info-list li strong {
        color: #23263b;
    }
    /* mini barra de ocupación */
    .mg-cap-wrap {
        margin-top: 2px;
    }
    .mg-cap-bar {
        height: 6px;
        border-radius: 3px;
        background: #e8eaf0;
        overflow: hidden;
        margin-top: 4px;
    }
    .mg-cap-fill {
        height: 100%;
        border-radius: 3px;
        transition: width .4s;
    }
    /* ── Footer de card ──────────────────────────── */
    .mg-card-footer {
        border-top: 1px solid #e8eaf0;
        padding: 12px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .mg-estatus-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 600;
        padding: 4px 12px;
    }
    .mg-estatus-badge.abierta  { background: #eaf4ea; color: #2e7d32; }
    .mg-estatus-badge.cerrada  { background: #fff8e1; color: #f57f17; }
    .mg-estatus-badge.cancelada{ background: #fdecea; color: #c62828; }
    .mg-btn-detalle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #3d5afe;
        color: #fff !important;
        border: none;
        border-radius: 20px;
        font-size: .78rem;
        font-weight: 600;
        padding: 6px 16px;
        cursor: pointer;
        text-decoration: none;
        transition: background .15s;
    }
    .mg-btn-detalle:hover { background: #2a3eb1; }

    /* ── Modal detalle ──────────────────────────── */
    /* (eliminado: ahora se usa vista dedicada) */
</style>
@endsection

@section('content')
<section class="section">

    {{-- Encabezado --}}
    <div class="section-header">
        <h1><i class="fas fa-chalkboard-teacher mr-2"></i>Mis Grupos</h1>
        <div class="section-header-breadcrumb">
            <span style="font-size:.85rem; color:#666;">
                <i class="fas fa-building mr-1"></i>{{ $instructor->departamento->nombre ?? 'Sin departamento' }}
            </span>
            @if($semestreActivo)
                <span class="mg-periodo-badge ml-2">
                    <i class="fas fa-calendar-check"></i>{{ $semestreActivo->label }}
                </span>
            @else
                <span class="mg-periodo-badge sin ml-2">
                    <i class="fas fa-exclamation-triangle"></i>Sin periodo activo
                </span>
            @endif
        </div>
    </div>

    <div class="section-body">
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
                No hay un semestre activo. Puedes consultar tus grupos, pero las calificaciones estarán bloqueadas.
            </div>
        @endif

        {{-- ── GRID DE CARDS ── --}}
        @if($grupos->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle mr-2"></i>No tienes grupos asignados por el momento.
            </div>
        @else
        <div class="mg-grid">

        @foreach ($grupos as $grupo)
        @php
            $todasInscripciones = $grupo->inscripciones;
            $totalInscritos     = $todasInscripciones->count();
            $calificados        = $todasInscripciones->filter(fn($i) => $i->calificaciones->count() > 0)->count();
            $pendientes         = $totalInscritos - $calificados;
            $pct                = $grupo->cupo_maximo > 0 ? round(($grupo->cupo_ocupado / $grupo->cupo_maximo) * 100) : 0;
            $colFill            = $pct >= 90 ? '#e53935' : ($pct >= 60 ? '#fb8c00' : '#43a047');
            $creditos           = $grupo->actividad->creditos ?? 1;
            $creditoClass       = $creditos == 1 ? 'c1' : ($creditos == 2 ? 'c2' : 'c3');
            $estatusClass       = $grupo->estatus === 'abierta' ? 'abierta' : ($grupo->estatus === 'cancelada' ? 'cancelada' : 'cerrada');
        @endphp

        {{-- Card --}}
        <a href="{{ route('instructor.ver-grupo', $grupo->id_grupo) }}" class="mg-card" style="text-decoration:none;">

            <div class="mg-card-body">
                {{-- Título + badge créditos --}}
                <div class="mg-top-row">
                    <div class="mg-actividad-nombre">{{ $grupo->actividad->nombre ?? 'Sin actividad' }}</div>
                    <span class="mg-creditos-badge {{ $creditoClass }}">{{ $creditos }} crédito(s)</span>
                </div>

                {{-- Periodo como subtexto --}}
                <p class="mg-semestre">
                    {{ $grupo->semestre->año ?? '' }}-{{ $grupo->semestre->periodo ?? '' }}
                    &nbsp;&bull;&nbsp; Grupo {{ $grupo->grupo }}
                </p>

                {{-- Info esencial --}}
                <ul class="mg-info-list">
                    <li>
                        <i class="fas fa-laptop"></i>
                        <strong>Modalidad:</strong> {{ ucfirst($grupo->modalidad) }}
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <strong>Lugar:</strong> {{ $grupo->ubicacion->espacio ?? 'Virtual' }}
                    </li>
                    <li>
                        <i class="fas fa-layer-group"></i>
                        <strong>Nivel:</strong> {{ $grupo->actividad->nivel_actividad ?? 'Básico' }}
                    </li>
                    <li>
                        <i class="fas fa-users"></i>
                        <strong>Alumnos:</strong> {{ $totalInscritos }} inscritos
                        @if($pendientes > 0)
                            &mdash; <span style="color:#e65100;">{{ $pendientes }} sin calificar</span>
                        @else
                            &mdash; <span style="color:#2e7d32;">todos calificados</span>
                        @endif
                    </li>
                </ul>

                {{-- Barra de capacidad --}}
                <div class="mg-cap-wrap">
                    <div style="display:flex; justify-content:space-between; font-size:.75rem; color:#9e9e9e;">
                        <span>Capacidad</span>
                        <span>{{ $grupo->cupo_ocupado }}/{{ $grupo->cupo_maximo }}</span>
                    </div>
                    <div class="mg-cap-bar">
                        <div class="mg-cap-fill" style="width:{{ $pct }}%; background:{{ $colFill }};"></div>
                    </div>
                </div>
            </div>

            {{-- Footer de la card --}}
            <div class="mg-card-footer">
                <span class="mg-estatus-badge {{ $estatusClass }}">
                    <i class="fas fa-circle" style="font-size:.5rem;"></i>
                    {{ ucfirst($grupo->estatus) }}
                </span>
                <span class="mg-btn-detalle">
                    <i class="fas fa-eye"></i>Ver lista
                </span>
            </div>
        </a>

        @endforeach

        </div>{{-- /mg-grid --}}
        @endif

    </div>
</section>
@endsection

@section('scripts')
<script>
    // Las cards ahora navegan a la vista de detalle; no se necesita JS adicional.
</script>
@endsection
