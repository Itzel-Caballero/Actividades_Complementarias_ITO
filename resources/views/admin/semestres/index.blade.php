@extends('layouts.app')

@section('title', 'Semestres')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Gestión de Semestres</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-12">

                {{-- Mensajes de Notificación --}}
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong>{{ $message }}</strong>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>{{ $message }}</strong>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                {{-- SECCIÓN: PERIODO ACTUAL (ACTIVO) --}}
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4><i class="fa fa-star"></i> Periodo Actual Activo</h4>
                        @if(!$periodoActual)
                            <a href="{{ route('admin.semestres.create') }}" class="btn btn-light btn-sm">
                                <i class="fa fa-plus"></i> Iniciar Nuevo Periodo
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($periodoActual)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Periodo</th>
                                            <th>Inicio – Fin Semestre</th>
                                            <th>Inscripciones</th>
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
                                            <td>
                                                {{ \Carbon\Carbon::parse($periodoActual->fecha_inicio)->format('d/m/Y') }} - 
                                                {{ \Carbon\Carbon::parse($periodoActual->fecha_fin)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($periodoActual->fecha_inicio_inscripciones)->format('d/m/Y') }} - 
                                                {{ \Carbon\Carbon::parse($periodoActual->fecha_fin_inscripciones)->format('d/m/Y') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $periodoActual->grupos_count }}</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.semestres.edit', $periodoActual->id_semestre) }}" class="btn btn-warning btn-sm">
                                                    <i class="fa fa-edit"></i> Editar / Finalizar
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted">No hay ningún periodo activo actualmente.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- SECCIÓN: HISTORIAL DE PERIODOS (INACTIVOS) --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Historial de Periodos Pasados / Inactivos</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Periodo</th>
                                        <th>Inicio – Fin</th>
                                        <th>Inscripciones</th>
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center">Grupos</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($historial as $semestre)
                                        @php
                                            $label = $semestre->periodo == 1 ? "Ene–Jun {$semestre->año}" : "Ago–Dic {$semestre->año}";
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $label }}</strong></td>
                                            <td>
                                                <small>
                                                    {{ \Carbon\Carbon::parse($semestre->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($semestre->fecha_fin)->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ \Carbon\Carbon::parse($semestre->fecha_inicio_inscripciones)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($semestre->fecha_fin_inscripciones)->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-secondary text-uppercase">{{ $semestre->status }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $semestre->grupos_count }}</span>
                                            </td>
                                            {{-- BOTONES ELIMINADOS DEL HISTORIAL POR SEGURIDAD --}}
                                            <td class="text-center">
                                                <span class="text-muted small" title="Los periodos inactivos no se pueden modificar">
                                                    <i class="fa fa-lock"></i> Solo lectura
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No hay registros en el historial.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {!! $historial->links() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection