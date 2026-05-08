@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Usuarios</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="mb-0"><i class="fa fa-user-edit mr-2"></i>Editar Usuario</h4>
                        <span class="badge badge-secondary"># {{ $user->id }}</span>
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

                        <form action="{{ route('usuarios.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- ─── Datos Personales ─────────────────────────────── --}}
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-id-card mr-1"></i> Datos Personales
                            </h6>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nombre <span class="text-danger">*</span></label>
                                        <input type="text" name="nombre" class="form-control"
                                               value="{{ old('nombre', $user->nombre) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Apellido Paterno <span class="text-danger">*</span></label>
                                        <input type="text" name="apellido_paterno" class="form-control"
                                               value="{{ old('apellido_paterno', $user->apellido_paterno) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Apellido Materno</label>
                                        <input type="text" name="apellido_materno" class="form-control"
                                               value="{{ old('apellido_materno', $user->apellido_materno) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" name="telefono" class="form-control"
                                               value="{{ old('telefono', $user->telefono) }}"
                                               placeholder="10 dígitos">
                                    </div>
                                </div>
                            </div>

                            {{-- ─── Datos de Acceso ──────────────────────────────── --}}
                            <hr>
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-lock mr-1"></i> Datos de Acceso
                            </h6>

                            <div class="form-group">
                                <label>Correo electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nueva contraseña
                                            <small class="text-muted">(dejar vacío para no cambiar)</small>
                                        </label>
                                        <input type="password" name="password" class="form-control"
                                               placeholder="••••••••" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirmar nueva contraseña</label>
                                        <input type="password" name="confirm-password" class="form-control"
                                               placeholder="••••••••" autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            {{-- ─── Tipo de Usuario ─────────────────────────────── --}}
                            <hr>
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user-shield mr-1"></i> Tipo de Usuario
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipo de usuario <span class="text-danger">*</span></label>
                                        <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                                            <option value="">-- Selecciona --</option>
                                            <option value="alumno"      {{ old('tipo_usuario', $user->tipo_usuario) == 'alumno'      ? 'selected' : '' }}>Alumno</option>
                                            <option value="instructor"  {{ old('tipo_usuario', $user->tipo_usuario) == 'instructor'  ? 'selected' : '' }}>Instructor</option>
                                            <option value="coordinador" {{ old('tipo_usuario', $user->tipo_usuario) == 'coordinador' ? 'selected' : '' }}>Coordinador</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Campo oculto para el rol --}}
                            <input type="hidden" name="roles" id="roles_hidden"
                                   value="{{ isset($userRole) && count($userRole) > 0 ? array_key_first($userRole) : '' }}">

                            {{-- ─── Datos del Alumno (solo si es alumno) ─────────── --}}
                            <div id="campos_alumno" style="{{ old('tipo_usuario', $user->tipo_usuario) == 'alumno' ? '' : 'display:none' }}">
                                <hr>
                                <h6 class="text-info mb-3">
                                    <i class="fas fa-user-graduate mr-1"></i> Datos del Alumno
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Número de Control <span class="text-danger">*</span></label>
                                            <input type="text" name="num_control" class="form-control"
                                                   maxlength="9"
                                                   placeholder="Ej: 21ITI001"
                                                   value="{{ old('num_control', $alumno->num_control ?? '') }}">
                                            <small class="form-text text-muted">Máximo 9 caracteres.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ─── Datos de Instructor (si aplica) ─────────────── --}}
                            <div id="campos_instructor" style="{{ old('tipo_usuario', $user->tipo_usuario) == 'instructor' ? '' : 'display:none' }}">
                                <hr>
                                <h6 class="text-info mb-3">
                                    <i class="fas fa-chalkboard-teacher mr-1"></i> Datos del Instructor
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Departamento</label>
                                            <select name="id_departamento" class="form-control">
                                                <option value="">-- Selecciona --</option>
                                                @foreach ($departamentos as $dep)
                                                    <option value="{{ $dep->id_departamento }}"
                                                        {{ old('id_departamento', $instructor->id_departamento ?? '') == $dep->id_departamento ? 'selected' : '' }}>
                                                        {{ $dep->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Especialidad</label>
                                            <input type="text" name="especialidad" class="form-control"
                                                   value="{{ old('especialidad', $instructor->especialidad ?? '') }}"
                                                   placeholder="Ej: Desarrollo Web, Cultura Física...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ─── Botones ──────────────────────────────────────── --}}
                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Actualizar Usuario
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
    function sincronizarRol() {
        const tipo = document.getElementById('tipo_usuario').value;
        let rol = '';

        switch (tipo) {
            case 'alumno':      rol = 'alumno';      break;
            case 'instructor':  rol = 'instructor';  break;
            case 'coordinador': rol = 'coordinador'; break;
            default:            rol = '';
        }

        document.getElementById('roles_hidden').value = rol;

        // Mostrar/ocultar campos de alumno
        document.getElementById('campos_alumno').style.display =
            tipo === 'alumno' ? '' : 'none';

        // Mostrar/ocultar campos de instructor
        document.getElementById('campos_instructor').style.display =
            tipo === 'instructor' ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('tipo_usuario').addEventListener('change', sincronizarRol);
        sincronizarRol();
    });
</script>
@endsection
