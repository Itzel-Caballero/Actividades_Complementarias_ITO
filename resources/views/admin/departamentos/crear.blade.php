@extends('layouts.app')

@section('title', 'Nuevo Departamento')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Departamentos</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-building mr-2"></i>Nuevo Departamento</h4>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.departamentos.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Nombre del Departamento <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control"
                                       placeholder="Ej: Ciencias Básicas"
                                       value="{{ old('nombre') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Edificio</label>
                                <input type="text" name="edificio" class="form-control"
                                       placeholder="Ej: Edificio A"
                                       value="{{ old('edificio') }}">
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.departamentos.index') }}" class="btn btn-secondary">
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
