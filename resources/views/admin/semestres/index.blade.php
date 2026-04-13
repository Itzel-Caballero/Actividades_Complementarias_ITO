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

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Periodos Escolares</h4>
                        <a href="{{ route('admin.semestres.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Nuevo Semestre
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Periodo</th>
                                        <th>Inicio – Fin del Semestre</th>
                                        <th>Inscripciones</th>
                                        <th class="text-center">Grupos</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($semestres as $semestre)
                                    @php
                                        $label = $semestre->periodo == 1
                                            ? "Ene–Jun {$semestre->año}"
                                            : "Ago–Dic {$semestre->año}";
                                        $badge = $semestre->periodo == 1 ? 'badge-primary' : 'badge-warning';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="badge {{ $badge }}">{{ $label }}</span>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($semestre->fecha_inicio)->format('d/m/Y') }}
                                            –
                                            {{ \Carbon\Carbon::parse($semestre->fecha_fin)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($semestre->fecha_inicio_inscripciones)->format('d/m/Y') }}
                                            –
                                            {{ \Carbon\Carbon::parse($semestre->fecha_fin_inscripciones)->format('d/m/Y') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info">{{ $semestre->grupos_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.semestres.edit', $semestre->id_semestre) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.semestres.destroy', $semestre->id_semestre) }}"
                                                  method="POST" style="display:inline-block"
                                                  onsubmit="return confirm('¿Eliminar este semestre? Solo es posible si no tiene grupos.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No hay semestres registrados.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {!! $semestres->links() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
