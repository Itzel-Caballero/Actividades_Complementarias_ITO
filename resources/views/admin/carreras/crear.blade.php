@extends('layouts.app')

@section('title', 'Nueva Carrera')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Catálogo de Carreras</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-graduation-cap mr-2"></i>Nueva Carrera</h4>
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

                        <form action="{{ route('admin.carreras.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Nombre de la Carrera <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control"
                                       placeholder="Ej: Ingeniería en Sistemas Computacionales"
                                       value="{{ old('nombre') }}" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.carreras.index') }}" class="btn btn-secondary">
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
