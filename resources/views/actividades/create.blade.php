@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Nueva Actividad</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h4>Crear Actividad Complementaria</h4>
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

                        <form action="{{ route('actividades.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control"
                                    value="{{ old('nombre') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Departamento <span class="text-danger">*</span></label>
                                        <select name="id_departamento" class="form-control" required>
                                            <option value="">-- Selecciona --</option>
                                            @foreach ($departamentos as $dep)
                                                <option value="{{ $dep->id_departamento }}"
                                                    {{ old('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                                    {{ $dep->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Créditos <span class="text-danger">*</span></label>
                                        <select name="creditos" class="form-control" required>
                                            <option value="">-- Selecciona --</option>
                                            <option value="1" {{ old('creditos') == 1 ? 'selected' : '' }}>1 crédito</option>
                                            <option value="2" {{ old('creditos') == 2 ? 'selected' : '' }}>2 créditos</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nivel</label>
                                        <select name="nivel_actividad" class="form-control">
                                            <option value="">-- Selecciona --</option>
                                            <option value="Básico" {{ old('nivel_actividad') == 'Básico' ? 'selected' : '' }}>Básico</option>
                                            <option value="Intermedio" {{ old('nivel_actividad') == 'Intermedio' ? 'selected' : '' }}>Intermedio</option>
                                            <option value="Avanzado" {{ old('nivel_actividad') == 'Avanzado' ? 'selected' : '' }}>Avanzado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Disponible</label>
                                        <select name="disponible" class="form-control">
                                            <option value="1" {{ old('disponible', 1) == 1 ? 'selected' : '' }}>Sí</option>
                                            <option value="0" {{ old('disponible') == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Requisitos</label>
                                <textarea name="requisitos" class="form-control" rows="2">{{ old('requisitos') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Carreras que pueden inscribirse</label>
                                <div class="row">
                                    @foreach ($carreras as $carrera)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="carreras[]"
                                                value="{{ $carrera->id_carrera }}"
                                                id="carrera_{{ $carrera->id_carrera }}"
                                                {{ in_array($carrera->id_carrera, old('carreras', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="carrera_{{ $carrera->id_carrera }}">
                                                {{ $carrera->nombre }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('actividades.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Guardar
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