@extends('layouts.app')

@section('title', 'Nueva Ubicación')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Control de Ubicaciones</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-map-marked-alt mr-2"></i>Nueva Ubicación</h4>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.ubicaciones.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Nombre del Espacio <span class="text-danger">*</span></label>
                                <input type="text" name="espacio" class="form-control"
                                       placeholder="Ej: Aula A-101, Auditorio Central, Cancha de Fútbol"
                                       value="{{ old('espacio') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Capacidad Máxima <span class="text-danger">*</span></label>
                                <input type="number" name="capacidad" class="form-control"
                                       placeholder="Ej: 30"
                                       value="{{ old('capacidad') }}" min="1" max="9999" required>
                                <small class="text-muted">Número máximo de personas que puede albergar.</small>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.ubicaciones.index') }}" class="btn btn-secondary">
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
