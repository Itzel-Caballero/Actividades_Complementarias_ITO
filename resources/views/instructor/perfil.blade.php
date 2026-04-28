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
                                    <input type="text" name="nombre" id="nombre"
                                           class="form-control @error('nombre') is-invalid @enderror"
                                           value="{{ old('nombre', $user->nombre) }}"
                                           required maxlength="50"
                                           placeholder="Ej: Carlos">
                                    <small class="text-muted">Solo letras, 2-50 caracteres.</small>
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="invalid-feedback" id="nombre-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Apellido Paterno <span class="text-danger">*</span></label>
                                    <input type="text" name="apellido_paterno" id="apellido_paterno"
                                           class="form-control @error('apellido_paterno') is-invalid @enderror"
                                           value="{{ old('apellido_paterno', $user->apellido_paterno) }}"
                                           required maxlength="50"
                                           placeholder="Ej: García">
                                    <small class="text-muted">Solo letras, 2-50 caracteres.</small>
                                    @error('apellido_paterno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="invalid-feedback" id="apellido_paterno-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Apellido Materno</label>
                                    <input type="text" name="apellido_materno" id="apellido_materno"
                                           class="form-control @error('apellido_materno') is-invalid @enderror"
                                           value="{{ old('apellido_materno', $user->apellido_materno) }}"
                                           maxlength="50"
                                           placeholder="Ej: López">
                                    <small class="text-muted">Solo letras, 2-50 caracteres (opcional).</small>
                                    @error('apellido_materno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="invalid-feedback" id="apellido_materno-error"></div>
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
                                    <input type="text" name="telefono" id="telefono"
                                           class="form-control @error('telefono') is-invalid @enderror"
                                           value="{{ old('telefono', $user->telefono) }}"
                                           maxlength="10" inputmode="numeric"
                                           placeholder="Ej: 9511234567">
                                    <small class="text-muted">Exactamente 10 dígitos numéricos.</small>
                                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="invalid-feedback" id="telefono-error"></div>
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
                                    <input type="text" name="especialidad" id="especialidad"
                                           class="form-control @error('especialidad') is-invalid @enderror"
                                           value="{{ old('especialidad', $instructor->especialidad) }}"
                                           maxlength="100"
                                           placeholder="Ej: Programación y Desarrollo de Software">
                                    <small class="text-muted">Letras, números, espacios, guiones y puntos. 3-100 caracteres.</small>
                                    @error('especialidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="invalid-feedback" id="especialidad-error"></div>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const soloLetras   = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/;
    const soloNumeros  = /^[0-9]+$/;
    const especialidadRegex = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s\-\.]+$/;

    // ── Bloquear teclas no válidas en tiempo real ────────────────────────────

    // Nombres y apellidos: solo letras y espacios
    ['nombre', 'apellido_paterno', 'apellido_materno'].forEach(function (id) {
        const input = document.getElementById(id);
        if (!input) return;

        input.addEventListener('keypress', function (e) {
            const char = String.fromCharCode(e.which);
            if (!/[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/.test(char)) {
                e.preventDefault();
            }
        });

        input.addEventListener('input', function () {
            // Eliminar cualquier carácter no válido pegado
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '');
            validarNombre(this, id);
        });

        input.addEventListener('blur', function () {
            validarNombre(this, id);
        });
    });

    function validarNombre(input, id) {
        const errorDiv = document.getElementById(id + '-error');
        const val = input.value.trim();
        const esOpcional = (id === 'apellido_materno');

        if (!esOpcional && val.length === 0) {
            setError(input, errorDiv, 'Este campo es obligatorio.');
        } else if (val.length > 0 && val.length < 2) {
            setError(input, errorDiv, 'Debe tener al menos 2 caracteres.');
        } else if (val.length > 50) {
            setError(input, errorDiv, 'No puede exceder 50 caracteres.');
        } else if (val.length > 0 && !soloLetras.test(val)) {
            setError(input, errorDiv, 'Solo se permiten letras y espacios.');
        } else {
            clearError(input, errorDiv);
        }
    }

    // ── Teléfono: solo dígitos, exactamente 10 ──────────────────────────────
    const telInput = document.getElementById('telefono');
    if (telInput) {
        telInput.addEventListener('keypress', function (e) {
            const char = String.fromCharCode(e.which);
            if (!soloNumeros.test(char)) {
                e.preventDefault();
            }
        });

        telInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            validarTelefono(this);
        });

        telInput.addEventListener('blur', function () {
            validarTelefono(this);
        });
    }

    function validarTelefono(input) {
        const errorDiv = document.getElementById('telefono-error');
        const val = input.value.trim();
        if (val.length === 0) {
            clearError(input, errorDiv); // Es opcional
        } else if (val.length !== 10) {
            setError(input, errorDiv, 'El teléfono debe tener exactamente 10 dígitos.');
        } else {
            clearError(input, errorDiv);
        }
    }

    // ── Especialidad ─────────────────────────────────────────────────────────
    const espInput = document.getElementById('especialidad');
    if (espInput) {
        espInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s\-\.]/g, '');
            validarEspecialidad(this);
        });

        espInput.addEventListener('blur', function () {
            validarEspecialidad(this);
        });
    }

    function validarEspecialidad(input) {
        const errorDiv = document.getElementById('especialidad-error');
        const val = input.value.trim();
        if (val.length === 0) {
            clearError(input, errorDiv); // Opcional
        } else if (val.length < 3) {
            setError(input, errorDiv, 'Debe tener al menos 3 caracteres.');
        } else if (val.length > 100) {
            setError(input, errorDiv, 'No puede exceder 100 caracteres.');
        } else if (!especialidadRegex.test(val)) {
            setError(input, errorDiv, 'Contiene caracteres no permitidos.');
        } else {
            clearError(input, errorDiv);
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    function setError(input, errorDiv, msg) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        if (errorDiv) { errorDiv.textContent = msg; errorDiv.style.display = 'block'; }
    }

    function clearError(input, errorDiv) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        if (errorDiv) { errorDiv.style.display = 'none'; }
    }

    // ── Bloquear submit si hay errores ────────────────────────────────────────
    document.querySelector('form').addEventListener('submit', function (e) {
        let valido = true;

        ['nombre', 'apellido_paterno', 'apellido_materno'].forEach(function (id) {
            const input = document.getElementById(id);
            if (input) validarNombre(input, id);
            if (input && input.classList.contains('is-invalid')) valido = false;
        });

        if (telInput) {
            validarTelefono(telInput);
            if (telInput.classList.contains('is-invalid')) valido = false;
        }

        if (espInput) {
            validarEspecialidad(espInput);
            if (espInput.classList.contains('is-invalid')) valido = false;
        }

        if (!valido) {
            e.preventDefault();
            // Scroll hacia el primer error
            const primerError = document.querySelector('.is-invalid');
            if (primerError) primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endsection
