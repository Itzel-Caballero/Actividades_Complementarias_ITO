@extends('layouts.app')

@section('title', 'Nuevo Semestre')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Gestión de Semestres</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-calendar-plus mr-2"></i>Abrir Nuevo Periodo Escolar</h4>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger"><strong>{{ $message }}</strong></div>
                        @endif

                        <form action="{{ route('admin.semestres.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Año <span class="text-danger">*</span></label>
                                        <input type="number" name="año" class="form-control"
                                               value="{{ old('año', date('Y')) }}"
                                               min="2000" max="2100" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Periodo <span class="text-danger">*</span></label>
                                        <select name="periodo" class="form-control" required>
                                            <option value="">-- Selecciona --</option>
                                            <option value="1" {{ old('periodo') == '1' ? 'selected' : '' }}>1 – Enero / Junio</option>
                                            <option value="2" {{ old('periodo') == '2' ? 'selected' : '' }}>2 – Agosto / Diciembre</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h6 class="text-primary"><i class="fas fa-calendar-alt mr-1"></i> Fechas del Semestre</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" class="form-control"
                                               value="{{ old('fecha_inicio') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Fin <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin" class="form-control"
                                               value="{{ old('fecha_fin') }}" required>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h6 class="text-success"><i class="fas fa-clipboard-list mr-1"></i> Período de Inscripciones</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Inicio de Inscripciones <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio_inscripciones" class="form-control"
                                               value="{{ old('fecha_inicio_inscripciones') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fin de Inscripciones <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin_inscripciones" class="form-control"
                                               value="{{ old('fecha_fin_inscripciones') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.semestres.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Guardar Semestre
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
