@extends('layouts.app')

@section('content')
<section class="section">

    <div class="section-header">
        <h3 class="page__heading">
            <i class="fas fa-user-edit mr-2"></i>Mi Perfil
        </h3>
    </div>

    <div class="section-body">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i><strong>{{ session('success') }}</strong>
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

        <form action="{{ route('instructor.perfil.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- ── Columna izquierda: Datos personales ── --}}
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-user mr-2"></i>Datos Personales</h4>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre', $user->nombre) }}"
                                       required maxlength="50" placeholder="Ej: Carlos">
                                <small class="text-muted">Solo letras, 2–50 caracteres.</small>
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback" id="nombre-error"></div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Apellido Paterno <span class="text-danger">*</span></label>
                                <input type="text" name="apellido_paterno" id="apellido_paterno"
                                       class="form-control @error('apellido_paterno') is-invalid @enderror"
                                       value="{{ old('apellido_paterno', $user->apellido_paterno) }}"
                                       required maxlength="50" placeholder="Ej: García">
                                <small class="text-muted">Solo letras, 2–50 caracteres.</small>
                                @error('apellido_paterno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback" id="apellido_paterno-error"></div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Apellido Materno</label>
                                <input type="text" name="apellido_materno" id="apellido_materno"
                                       class="form-control @error('apellido_materno') is-invalid @enderror"
                                       value="{{ old('apellido_materno', $user->apellido_materno) }}"
                                       maxlength="50" placeholder="Ej: López">
                                <small class="text-muted">Solo letras, 2–50 caracteres (opcional).</small>
                                @error('apellido_materno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback" id="apellido_materno-error"></div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Correo Electrónico</label>
                                <input type="email" class="form-control bg-light"
                                       value="{{ $user->email }}" disabled>
                                <small class="text-muted">El correo no puede modificarse.</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Teléfono</label>
                                <input type="text" name="telefono" id="telefono"
                                       class="form-control @error('telefono') is-invalid @enderror"
                                       value="{{ old('telefono', $user->telefono) }}"
                                       maxlength="10" inputmode="numeric" placeholder="10 dígitos">
                                <small class="text-muted">Exactamente 10 dígitos numéricos (opcional).</small>
                                @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback" id="telefono-error"></div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Columna derecha: Datos de instructor + contraseña ── --}}
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-chalkboard-teacher mr-2"></i>Datos de Instructor</h4>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label class="font-weight-bold">Departamento</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ $instructor->departamento->nombre ?? 'No asignado' }}" disabled>
                                <small class="text-muted">El departamento es asignado por el administrador.</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Especialidad</label>
                                <input type="text" name="especialidad" id="especialidad"
                                       class="form-control @error('especialidad') is-invalid @enderror"
                                       value="{{ old('especialidad', $instructor->especialidad) }}"
                                       maxlength="100"
                                       placeholder="Ej: Programación y Desarrollo de Software">
                                <small class="text-muted">Letras, números, espacios, guiones y puntos. 3–100 caracteres.</small>
                                @error('especialidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback" id="especialidad-error"></div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Último acceso</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ $user->ultimo_acceso ? \Carbon\Carbon::parse($user->ultimo_acceso)->format('d/m/Y H:i') : 'N/A' }}"
                                       disabled>
                            </div>

                        </div>
                    </div>

                    {{-- Cambio de contraseña --}}
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4><i class="fas fa-lock mr-2"></i>Cambiar Contraseña</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Deja estos campos vacíos si no deseas cambiar tu contraseña.</p>

                            <div class="form-group">
                                <label class="font-weight-bold">Nueva Contraseña</label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation"
                                       class="form-control"
                                       placeholder="Repite la nueva contraseña" autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                </button>
                <a href="{{ route('home') }}" class="btn btn-light ml-2">
                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                </a>
            </div>

        </form>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const soloLetras        = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/;
    const soloNumeros       = /^[0-9]+$/;
    const especialidadRegex = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s\-\.]+$/;

    // ── Nombres y apellidos ──────────────────────────────────────────────────
    ['nombre', 'apellido_paterno', 'apellido_materno'].forEach(function (id) {
        const input = document.getElementById(id);
        if (!input) return;

        input.addEventListener('keypress', function (e) {
            if (!/[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/.test(String.fromCharCode(e.which)))
                e.preventDefault();
        });

        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '');
            validarNombre(this, id);
        });

        input.addEventListener('blur', function () { validarNombre(this, id); });
    });

    function validarNombre(input, id) {
        const errorDiv = document.getElementById(id + '-error');
        const val = input.value.trim();
        const esOpcional = (id === 'apellido_materno');

        if (!esOpcional && val.length === 0)        setError(input, errorDiv, 'Este campo es obligatorio.');
        else if (val.length > 0 && val.length < 2)  setError(input, errorDiv, 'Debe tener al menos 2 caracteres.');
        else if (val.length > 50)                   setError(input, errorDiv, 'No puede exceder 50 caracteres.');
        else if (val.length > 0 && !soloLetras.test(val)) setError(input, errorDiv, 'Solo se permiten letras y espacios.');
        else                                         clearError(input, errorDiv);
    }

    // ── Teléfono ─────────────────────────────────────────────────────────────
    const telInput = document.getElementById('telefono');
    if (telInput) {
        telInput.addEventListener('keypress', function (e) {
            if (!soloNumeros.test(String.fromCharCode(e.which))) e.preventDefault();
        });
        telInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            validarTelefono(this);
        });
        telInput.addEventListener('blur', function () { validarTelefono(this); });
    }

    function validarTelefono(input) {
        const errorDiv = document.getElementById('telefono-error');
        const val = input.value.trim();
        if (val.length === 0)        clearError(input, errorDiv);
        else if (val.length !== 10)  setError(input, errorDiv, 'El teléfono debe tener exactamente 10 dígitos.');
        else                         clearError(input, errorDiv);
    }

    // ── Especialidad ─────────────────────────────────────────────────────────
    const espInput = document.getElementById('especialidad');
    if (espInput) {
        espInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s\-\.]/g, '');
            validarEspecialidad(this);
        });
        espInput.addEventListener('blur', function () { validarEspecialidad(this); });
    }

    function validarEspecialidad(input) {
        const errorDiv = document.getElementById('especialidad-error');
        const val = input.value.trim();
        if (val.length === 0)                              clearError(input, errorDiv);
        else if (val.length < 3)                           setError(input, errorDiv, 'Debe tener al menos 3 caracteres.');
        else if (val.length > 100)                         setError(input, errorDiv, 'No puede exceder 100 caracteres.');
        else if (!especialidadRegex.test(val))             setError(input, errorDiv, 'Contiene caracteres no permitidos.');
        else                                               clearError(input, errorDiv);
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
            if (input) { validarNombre(input, id); if (input.classList.contains('is-invalid')) valido = false; }
        });

        if (telInput) { validarTelefono(telInput); if (telInput.classList.contains('is-invalid')) valido = false; }
        if (espInput) { validarEspecialidad(espInput); if (espInput.classList.contains('is-invalid')) valido = false; }

        if (!valido) {
            e.preventDefault();
            const primerError = document.querySelector('.is-invalid');
            if (primerError) primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endsection
