@extends('layouts.app')

@section('title', 'Monitor de Inscripciones')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Monitor de Inscripciones</h3>
    </div>
    <div class="section-body">

        {{-- Tarjetas de resumen --}}
        <div class="row mb-3">
            @php
                $estatusConfig = [
                    'inscrito'   => ['color' => 'bg-c-blue',   'icon' => 'fa-clipboard-check', 'label' => 'Inscritos'],
                    'cursando'   => ['color' => 'bg-c-green',  'icon' => 'fa-book-open',       'label' => 'Cursando'],
                    'completado' => ['color' => 'bg-c-pink', 'icon' => 'fa-check-circle',    'label' => 'Completados'],
                    'baja'       => ['color' => 'bg-c-pink',   'icon' => 'fa-user-minus',      'label' => 'Bajas'],
                ];
            @endphp
            @foreach ($estatusConfig as $key => $cfg)
            <div class="col-md-3 col-sm-6">
                <div class="card {{ $cfg['color'] }} order-card">
                    <div class="card-block">
                        <h6>{{ $cfg['label'] }}</h6>
                        <h2 class="text-right">
                            <i class="fa {{ $cfg['icon'] }} f-left"></i>
                            <span>{{ $totales[$key] ?? 0 }}</span>
                        </h2>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-12">

                {{-- Filtros --}}
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <form action="{{ route('admin.reportes.inscripciones') }}" method="GET"
                              class="form-inline flex-wrap" style="gap:8px">
                            <input type="text" name="buscar" class="form-control form-control-sm"
                                   placeholder="Nombre o num. control..." value="{{ $buscar }}">
                            <select name="estatus" class="form-control form-control-sm">
                                <option value="">Todos los estatus</option>
                                <option value="inscrito"   {{ $estatus == 'inscrito'   ? 'selected' : '' }}>Inscrito</option>
                                <option value="cursando"   {{ $estatus == 'cursando'   ? 'selected' : '' }}>Cursando</option>
                                <option value="completado" {{ $estatus == 'completado' ? 'selected' : '' }}>Completado</option>
                                <option value="baja"       {{ $estatus == 'baja'       ? 'selected' : '' }}>Baja</option>
                            </select>
                            <select name="id_carrera" class="form-control form-control-sm">
                                <option value="">Todas las carreras</option>
                                @foreach ($carreras as $c)
                                    <option value="{{ $c->id_carrera }}"
                                        {{ $id_carrera == $c->id_carrera ? 'selected' : '' }}>
                                        {{ $c->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fa fa-filter"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.reportes.inscripciones') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-times"></i> Limpiar
                            </a>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>
                            <i class="fas fa-list-alt mr-2 text-primary"></i>
                            Inscripciones
                            <span class="badge badge-primary ml-2">{{ $inscripciones->total() }}</span>
                        </h4>
                        <small class="text-muted"><i class="fas fa-lock mr-1"></i>Vista de solo lectura</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Alumno</th>
                                        <th>Carrera</th>
                                        <th>Actividad / Grupo</th>
                                        <th>Instructor</th>
                                        <th class="text-center">Fecha Inscripción</th>
                                        <th class="text-center">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($inscripciones as $insc)
                                    @php
                                        $badgeMap = [
                                            'inscrito'   => 'badge-primary',
                                            'cursando'   => 'badge-success',
                                            'completado' => 'badge-info',
                                            'baja'       => 'badge-danger',
                                        ];
                                        $badge = $badgeMap[$insc->estatus] ?? 'badge-secondary';
                                    @endphp
                                    <tr>
                                        <td>{{ ($inscripciones->currentPage() - 1) * $inscripciones->perPage() + $loop->iteration }}</td>
                                        <td>
                                            {{ $insc->alumno->usuario->nombre_completo ?? '—' }}
                                            <br>
                                            <small class="text-muted">{{ $insc->alumno->usuario->num_control ?? '' }}</small>
                                        </td>
                                        <td><small>{{ $insc->alumno->carrera->nombre ?? '—' }}</small></td>
                                        <td>
                                            {{ $insc->grupo->actividad->nombre ?? '—' }}
                                            <br>
                                            <small class="text-muted">Grupo: {{ $insc->grupo->grupo ?? '' }}</small>
                                        </td>
                                        <td><small>{{ $insc->grupo->instructor->usuario->nombre_completo ?? '—' }}</small></td>
                                        <td class="text-center">
                                            <small>{{ \Carbon\Carbon::parse($insc->fecha_inscripcion)->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badge }}">{{ ucfirst($insc->estatus) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No hay inscripciones con esos filtros.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {!! $inscripciones->appends(request()->query())->links() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
