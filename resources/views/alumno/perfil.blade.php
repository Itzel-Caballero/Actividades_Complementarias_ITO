@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Mi Perfil</h3>
    </div>
    <div class="section-body">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <form action="{{ route('alumno.perfil.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- Datos personales --}}
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-user"></i> Datos Personales</h4>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                    value="{{ old('nombre', $user->nombre) }}" required maxlength="100">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Apellido Paterno <span class="text-danger">*</span></label>
                                <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror"
                                    value="{{ old('apellido_paterno', $user->apellido_paterno) }}" required maxlength="100">
                                @error('apellido_paterno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Apellido Materno</label>
                                <input type="text" name="apellido_materno" class="form-control @error('apellido_materno') is-invalid @enderror"
                                    value="{{ old('apellido_materno', $user->apellido_materno) }}" maxlength="100">
                                @error('apellido_materno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required maxlength="100">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono', $user->telefono) }}" maxlength="10" placeholder="10 dígitos">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Datos académicos + contraseña --}}
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-graduation-cap"></i> Datos Académicos</h4>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>No. de Control</label>
                                <input type="text" class="form-control" value="{{ $user->num_control }}" disabled>
                                <small class="text-muted">El número de control no se puede modificar.</small>
                            </div>

                            <div class="form-group">
                                <label>Carrera <span class="text-danger">*</span></label>
                                <select name="id_carrera" class="form-control @error('id_carrera') is-invalid @enderror" required>
                                    <option value="">-- Selecciona --</option>
                                    @foreach ($carreras as $carrera)
                                        <option value="{{ $carrera->id_carrera }}"
                                            {{ old('id_carrera', $alumno->id_carrera) == $carrera->id_carrera ? 'selected' : '' }}>
                                            {{ $carrera->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_carrera')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Semestre que cursas <span class="text-danger">*</span></label>
                                <select name="semestre_cursando" class="form-control @error('semestre_cursando') is-invalid @enderror" required>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}"
                                            {{ old('semestre_cursando', $alumno->semestre_cursando) == $i ? 'selected' : '' }}>
                                            {{ $i }}°
                                        </option>
                                    @endfor
                                </select>
                                @error('semestre_cursando')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Créditos Acumulados</label>
                                <input type="text" class="form-control" value="{{ $alumno->creditos_acumulados }}" disabled>
                                <small class="text-muted">Los créditos se actualizan automáticamente al aprobar actividades.</small>
                            </div>

                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h4><i class="fas fa-lock"></i> Cambiar Contraseña</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Deja estos campos vacíos si no deseas cambiar tu contraseña.</p>

                            <div class="form-group">
                                <label>Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Repite la nueva contraseña" autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="{{ route('home') }}" class="btn btn-light ml-2">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>

        </form>

    </div>
</section>
@endsection
