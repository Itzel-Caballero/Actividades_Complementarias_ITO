@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Nuevo Grupo</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="card">
                    <div class="card-header">
                        <h4>Crear Grupo</h4>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('grupos.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Actividad <span class="text-danger">*</span></label>
                                        <select name="id_actividad" class="form-control" required>
                                            <option value="">-- Selecciona --</option>
                                            @foreach ($actividades as $act)
                                                <option value="{{ $act->id_actividad }}"
                                                    {{ old('id_actividad') == $act->id_actividad ? 'selected' : '' }}>
                                                    {{ $act->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Clave de Grupo <span class="text-danger">*</span></label>
                                        <input type="text" name="grupo" class="form-control"
                                               placeholder="Ej: A, B, 01"
                                               value="{{ old('grupo') }}" required maxlength="10">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Semestre <span class="text-danger">*</span></label>
                                        <select name="id_semestre" class="form-control" required>
                                            <option value="">-- Selecciona --</option>
                                            @foreach ($semestres as $sem)
                                                <option value="{{ $sem->id_semestre }}"
                                                    {{ old('id_semestre') == $sem->id_semestre ? 'selected' : '' }}>
                                                    {{ $sem->año }} — {{ $sem->periodo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- INSTRUCTOR --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Instructor</label>
                                        <select name="id_instructor" class="form-control">
                                            <option value="">— Sin asignar —</option>
                                            @foreach ($instructores as $inst)
                                                <option value="{{ $inst->id_instructor }}"
                                                    {{ old('id_instructor') == $inst->id_instructor ? 'selected' : '' }}>
                                                    {{ $inst->usuario->nombre ?? '' }}
                                                    {{ $inst->usuario->apellido_paterno ?? '' }}
                                                    {{ $inst->usuario->apellido_materno ?? '' }}
                                                    @if($inst->especialidad)
                                                        — {{ $inst->especialidad }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ubicación</label>
                                        <select name="id_ubicacion" class="form-control">
                                            <option value="">— Sin asignar —</option>
                                            @foreach ($ubicaciones as $ubic)
                                                <option value="{{ $ubic->id_ubicacion }}"
                                                    {{ old('id_ubicacion') == $ubic->id_ubicacion ? 'selected' : '' }}>
                                                    {{ $ubic->espacio }}
                                                    @if($ubic->capacidad)
                                                        (cap. {{ $ubic->capacidad }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Modalidad <span class="text-danger">*</span></label>
                                        <select name="modalidad" class="form-control" required>
                                            <option value="presencial" {{ old('modalidad') == 'presencial' ? 'selected' : '' }}>Presencial</option>
                                            <option value="virtual"    {{ old('modalidad') == 'virtual'    ? 'selected' : '' }}>Virtual</option>
                                            <option value="hibrida"    {{ old('modalidad') == 'hibrida'    ? 'selected' : '' }}>Híbrida</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Cupo Máximo <span class="text-danger">*</span></label>
                                        <input type="number" name="cupo_maximo" class="form-control"
                                               value="{{ old('cupo_maximo', 30) }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Estatus</label>
                                        <select name="estatus" class="form-control">
                                            <option value="abierta">Abierta</option>
                                            <option value="cerrada">Cerrada</option>
                                            <option value="cancelada">Cancelada</option>
                                            <option value="finalizada">Finalizada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" class="form-control"
                                               value="{{ old('fecha_inicio') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha Fin <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin" class="form-control"
                                               value="{{ old('fecha_fin') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Materiales Requeridos</label>
                                <textarea name="materiales_requeridos" class="form-control" rows="2"
                                          placeholder="Lista de materiales que el alumno necesita...">{{ old('materiales_requeridos') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('grupos.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Crear Grupo
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
