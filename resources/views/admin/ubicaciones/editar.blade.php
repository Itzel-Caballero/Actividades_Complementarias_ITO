@extends('layouts.app')

@section('title', 'Editar Ubicación')

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
                        <h4><i class="fas fa-edit mr-2"></i>Editar Ubicación</h4>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.ubicaciones.update', $ubicacion->id_ubicacion) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Nombre del Espacio <span class="text-danger">*</span></label>
                                <input type="text" name="espacio" class="form-control"
                                       value="{{ old('espacio', $ubicacion->espacio) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Capacidad Máxima <span class="text-danger">*</span></label>
                                <input type="number" name="capacidad" class="form-control"
                                       value="{{ old('capacidad', $ubicacion->capacidad) }}"
                                       min="1" max="9999" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.ubicaciones.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Actualizar
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
