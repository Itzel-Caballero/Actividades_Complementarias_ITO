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
                                <div class="col-md-6" id="campo_num_control" style="{{ old('tipo_usuario', $user->tipo_usuario) == 'alumno' ? '' : 'display:none' }}">
                                    <div class="form-group">
                                        <label>Número de Control</label>
                                        <input type="text" name="num_control" class="form-control"
                                               value="{{ old('num_control', $user->num_control) }}">
                                    </div>
                                </div>
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

                            {{-- ─── Tipo y Rol ───────────────────────────────────── --}}
                            <hr>
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user-shield mr-1"></i> Tipo y Rol
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Rol <span class="text-danger">*</span></label>
                                        <select name="roles" class="form-control" required>
                                            <option value="">-- Selecciona un rol --</option>
                                            @foreach ($roles as $rol)
                                                <option value="{{ $rol }}"
                                                    {{ isset($userRole[$rol]) ? 'selected' : '' }}>
                                                    {{ ucfirst($rol) }}
                                                </option>
                                            @endforeach
                                        </select>
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
    document.getElementById('tipo_usuario').addEventListener('change', function () {
        const tipo = this.value;
        
        // Mostrar/ocultar campos de instructor
        document.getElementById('campos_instructor').style.display =
            tipo === 'instructor' ? 'block' : 'none';
        
        // Mostrar/ocultar campo de número de control (solo para alumnos)
        document.getElementById('campo_num_control').style.display =
            tipo === 'alumno' ? '' : 'none';
    });
    
    // Ejecutar al cargar para establecer el estado inicial
    document.addEventListener('DOMContentLoaded', function() {
        const tipo = document.getElementById('tipo_usuario').value;
        document.getElementById('campo_num_control').style.display =
            tipo === 'alumno' ? '' : 'none';
    });
</script>
@endsection
