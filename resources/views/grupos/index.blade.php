@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Gestión de Grupos</h3>
    </div>
    <div class="section-body">

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ $message }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Grupos</h4>
                <a href="{{ route('grupos.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Nuevo Grupo
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Actividad</th>
                                <th>Grupo</th>
                                <th>Instructor</th>
                                <th>Modalidad</th>
                                <th>Cupo</th>
                                <th>Estatus</th>
                                <th>Vigencia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($grupos as $grupo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $grupo->actividad->nombre ?? 'N/A' }}</td>
                                <td><span class="badge badge-dark">{{ $grupo->grupo }}</span></td>

                                {{-- Selector rápido de instructor --}}
                                <td>
                                    <form action="{{ route('grupos.asignar-instructor', $grupo->id_grupo) }}"
                                          method="POST" class="form-inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="id_instructor"
                                                class="form-control form-control-sm"
                                                style="min-width:160px"
                                                onchange="this.form.submit()">
                                            <option value="">— Sin instructor —</option>
                                            @foreach(\App\Models\Instructor::with('usuario')->get() as $inst)
                                                <option value="{{ $inst->id_instructor }}"
                                                    {{ $grupo->id_instructor == $inst->id_instructor ? 'selected' : '' }}>
                                                    {{ $inst->usuario->nombre ?? '' }}
                                                    {{ $inst->usuario->apellido_paterno ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                <td>
                                    <span class="badge badge-secondary">{{ ucfirst($grupo->modalidad) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $grupo->inscripciones->count() >= $grupo->cupo_maximo ? 'danger' : 'success' }}">
                                        {{ $grupo->inscripciones->count() }} / {{ $grupo->cupo_maximo }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $colores = ['abierta'=>'success','cerrada'=>'warning','cancelada'=>'danger','finalizada'=>'secondary'];
                                    @endphp
                                    <span class="badge badge-{{ $colores[$grupo->estatus] ?? 'light' }}">
                                        {{ ucfirst($grupo->estatus) }}
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        {{ \Carbon\Carbon::parse($grupo->fecha_inicio)->format('d/m/Y') }}
                                        —
                                        {{ \Carbon\Carbon::parse($grupo->fecha_fin)->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('grupos.edit', $grupo->id_grupo) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('grupos.destroy', $grupo->id_grupo) }}"
                                          method="POST" style="display:inline-block"
                                          onsubmit="return confirm('¿Eliminar este grupo y sus inscripciones?')">
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
                                <td colspan="9" class="text-center">No hay grupos registrados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {!! $grupos->links() !!}
            </div>
        </div>

    </div>
</section>
@endsection
