@extends('layouts.app')

@section('content')
<section class="section">

    <div class="section-header d-flex justify-content-between align-items-center">
        <h3 class="page__heading">
            <a href="{{ route('home') }}" class="text-secondary mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            Editar Perfil
        </h3>
    </div>

    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-user-edit mr-2"></i>Información Personal
                        </h6>
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

                        <form action="{{ route('instructor.perfil.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                           value="{{ old('nombre', $user->nombre) }}" required>
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Apellido Paterno <span class="text-danger">*</span></label>
                                    <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror"
                                           value="{{ old('apellido_paterno', $user->apellido_paterno) }}" required>
                                    @error('apellido_paterno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Apellido Materno</label>
                                    <input type="text" name="apellido_materno" class="form-control @error('apellido_materno') is-invalid @enderror"
                                           value="{{ old('apellido_materno', $user->apellido_materno) }}">
                                    @error('apellido_materno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Correo electrónico</label>
                                    <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                                    <small class="text-muted">El correo no puede modificarse.</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                           value="{{ old('telefono', $user->telefono) }}" maxlength="20"
                                           placeholder="Ej: 9511234567">
                                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Departamento</label>
                                    <input type="text" class="form-control"
                                           value="{{ $instructor->departamento->nombre ?? 'No asignado' }}" disabled>
                                    <small class="text-muted">El departamento es asignado por el administrador.</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Especialidad</label>
                                    <input type="text" name="especialidad" class="form-control @error('especialidad') is-invalid @enderror"
                                           value="{{ old('especialidad', $instructor->especialidad) }}" maxlength="100"
                                           placeholder="Ej: Programación y Desarrollo de Software">
                                    @error('especialidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('home') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Guardar cambios
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
