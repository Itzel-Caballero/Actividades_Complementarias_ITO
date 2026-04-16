@extends('layouts.app')
@section('title', 'Gestión de Semestres')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Gestión de Semestres ses</h3>
    </div>
    <div class="section-body">

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>{{ session('error') }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- ── PERIODO ACTIVO ───────────────────────────────────────────── --}}
        <div class="card border-primary mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fa fa-star mr-2"></i>Periodo Actual Activo</h4>
                @if(!$periodoActual)
                    <a href="{{ route('admin.semestres.create') }}" class="btn btn-light btn-sm">
                        <i class="fa fa-plus"></i> Iniciar Nuevo Periodo
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($periodoActual)
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Periodo</th>
                                    <th>Inicio Semestre</th>
                                    <th>Fin Semestre</th>
                                    <th>Inicio Inscripciones</th>
                                    <th>Fin Inscripciones</th>
                                    <th class="text-center">Grupos</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>{{ $periodoActual->periodo == 1 ? 'Ene–Jun' : 'Ago–Dic' }} {{ $periodoActual->año }}</strong>
                                        <br><span class="badge badge-success">ACTIVO</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($periodoActual->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($periodoActual->fecha_fin)->format('d/m/Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($periodoActual->fecha_inicio_inscripciones)->format('d/m/Y') }}
                                        @if($periodoActual->hora_inicio_inscripciones)
                                            <span class="badge badge-light text-dark">{{ substr($periodoActual->hora_inicio_inscripciones, 0, 5) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($periodoActual->fecha_fin_inscripciones)->format('d/m/Y') }}
                                        @if($periodoActual->hora_fin_inscripciones)
                                            <span class="badge badge-light text-dark">{{ substr($periodoActual->hora_fin_inscripciones, 0, 5) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $periodoActual->grupos_count }}</span>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="{{ route('admin.semestres.edit', $periodoActual->id_semestre) }}"
                                           class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i> Editar / Finalizar
                                        </a>
                                        <a href="{{ route('admin.semestres.show', $periodoActual->id_semestre) }}"
                                           class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i> Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                        No hay ningún periodo activo actualmente.
                        @if($ultimoInactivo)
                            <br>
                            <small>Puedes reactivar el período más reciente:</small>
                            <strong class="d-block">{{ $ultimoInactivo->label }}</strong>
                            <a href="{{ route('admin.semestres.edit', $ultimoInactivo->id_semestre) }}"
                               class="btn btn-outline-warning btn-sm mt-2">
                                <i class="fas fa-redo"></i> Reactivar período más reciente
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- ── HISTORIAL ────────────────────────────────────────────────── --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Historial de Periodos Pasados</h4>
                @if(!$periodoActual)
                    <a href="{{ route('admin.semestres.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Nuevo Periodo
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Periodo</th>
                                <th>Inicio Semestre</th>
                                <th>Fin Semestre</th>
                                <th>Inicio Inscripciones</th>
                                <th>Fin Inscripciones</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center">Grupos</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historial as $semestre)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $semestre->periodo == 1 ? "Ene–Jun {$semestre->año}" : "Ago–Dic {$semestre->año}" }}</strong>
                                </td>
                                <td><small>{{ \Carbon\Carbon::parse($semestre->fecha_inicio)->format('d/m/Y') }}</small></td>
                                <td><small>{{ \Carbon\Carbon::parse($semestre->fecha_fin)->format('d/m/Y') }}</small></td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($semestre->fecha_inicio_inscripciones)->format('d/m/Y') }}</small>
                                    @if($semestre->hora_inicio_inscripciones)
                                        <span class="badge badge-light text-dark" style="font-size:10px;">{{ substr($semestre->hora_inicio_inscripciones,0,5) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($semestre->fecha_fin_inscripciones)->format('d/m/Y') }}</small>
                                    @if($semestre->hora_fin_inscripciones)
                                        <span class="badge badge-light text-dark" style="font-size:10px;">{{ substr($semestre->hora_fin_inscripciones,0,5) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary text-uppercase">{{ $semestre->status }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $semestre->grupos_count }}</span>
                                </td>
                                <td class="text-center text-nowrap">
                                    {{-- Ver detalle siempre disponible --}}
                                    <a href="{{ route('admin.semestres.show', $semestre->id_semestre) }}"
                                       class="btn btn-info btn-sm" title="Ver grupos y alumnos">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    {{-- Editar/reactivar solo si es el más reciente y no hay activo --}}
                                    @if(!$periodoActual && $ultimoInactivo && $ultimoInactivo->id_semestre === $semestre->id_semestre)
                                        <a href="{{ route('admin.semestres.edit', $semestre->id_semestre) }}"
                                           class="btn btn-warning btn-sm" title="Editar / Reactivar">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @else
                                        <span class="text-muted" title="Solo lectura"><i class="fa fa-lock"></i></span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No hay registros en el historial.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($historial->hasPages())
                    <div class="card-footer">{!! $historial->links() !!}</div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection
