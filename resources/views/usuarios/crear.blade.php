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
                    <div class="card-header">
                        <h4>Crear Nuevo Usuario</h4>
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

                        <form action="{{ route('usuarios.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Apellido Paterno</label>
                                <input type="text" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Apellido Materno</label>
                                <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno') }}">
                            </div>

                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                            </div>

                            <div class="form-group">
                                <label>Correo electrónico</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Tipo de usuario <span class="text-danger">*</span></label>
                                <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                                    <option value="">-- Selecciona --</option>
                                    <option value="alumno"      {{ old('tipo_usuario') == 'alumno'      ? 'selected' : '' }}>Alumno</option>
                                    <option value="instructor"  {{ old('tipo_usuario') == 'instructor'  ? 'selected' : '' }}>Instructor</option>
                                    <option value="coordinador" {{ old('tipo_usuario') == 'coordinador' ? 'selected' : '' }}>Coordinador</option>
                                </select>
                                <small class="form-text text-muted">
                                    El rol se asignará automáticamente según el tipo seleccionado.
                                </small>
                            </div>

                            {{-- Campos extra para ALUMNO --}}
                            <div id="campos_alumno" style="display:none">
                                <hr>
                                <h6 class="text-primary"><i class="fa fa-user-graduate"></i> Datos del Alumno</h6>
                                <div class="form-group">
                                    <label>Número de Control <span class="text-danger">*</span></label>
                                    <input type="text" name="num_control" class="form-control"
                                           maxlength="9"
                                           placeholder="Ej: 21ITI001"
                                           value="{{ old('num_control') }}">
                                    <small class="form-text text-muted">Máximo 9 caracteres.</small>
                                </div>
                                <hr>
                            </div>

                            {{-- Campos extra para INSTRUCTOR --}}
                            <div id="campos_instructor" style="display:none">
                                <hr>
                                <h6 class="text-primary"><i class="fa fa-chalkboard-teacher"></i> Datos del Instructor</h6>
                                <div class="form-group">
                                    <label>Departamento <span class="text-danger">*</span></label>
                                    <select name="id_departamento" class="form-control">
                                        <option value="">-- Selecciona --</option>
                                        @foreach ($departamentos as $dep)
                                            <option value="{{ $dep->id_departamento }}"
                                                {{ old('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                                {{ $dep->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Especialidad</label>
                                    <input type="text" name="especialidad" class="form-control"
                                           placeholder="Ej: Desarrollo Web, Cultura Física..."
                                           value="{{ old('especialidad') }}">
                                </div>
                                <hr>
                            </div>

                            <div class="form-group">
                                <label>Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Confirmar contraseña</label>
                                <input type="password" name="confirm-password" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Rol asignado</label>
                                <input type="text" class="form-control" id="rol_asignado" readonly value="Seleccione un tipo de usuario">
                                <small class="form-text text-muted">
                                    Este campo es automático y se sincroniza con el tipo de usuario.
                                </small>
                            </div>

                            {{-- Campo oculto para enviar el rol automáticamente --}}
                            <input type="hidden" name="roles" id="roles_hidden" value="">

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Guardar Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@section('scripts')
<script>
    function sincronizarRol() {
        const tipo = document.getElementById('tipo_usuario').value;
        let rol = '';
        let rolTexto = 'Seleccione un tipo de usuario';

        switch (tipo) {
            case 'alumno':
                rol = 'alumno';
                rolTexto = 'Alumno';
                break;
            case 'instructor':
                rol = 'instructor';
                rolTexto = 'Instructor';
                break;
            case 'coordinador':
                rol = 'coordinador';
                rolTexto = 'Coordinador';
                break;
            default:
                rol = '';
                rolTexto = 'Seleccione un tipo de usuario';
        }

        document.getElementById('roles_hidden').value = rol;
        document.getElementById('rol_asignado').value = rolTexto;

        // Mostrar/ocultar sección alumno
        document.getElementById('campos_alumno').style.display =
            tipo === 'alumno' ? 'block' : 'none';

        // Mostrar/ocultar sección instructor
        document.getElementById('campos_instructor').style.display =
            tipo === 'instructor' ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('tipo_usuario').addEventListener('change', sincronizarRol);
        sincronizarRol();
    });
</script>
@endsection
@endsection
