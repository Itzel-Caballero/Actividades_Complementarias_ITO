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
                                <label>Número de Control</label>
                                <input type="text" name="num_control" class="form-control" value="{{ old('num_control') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                            </div>

                            <div class="form-group">
                                <label>Correo electrónico</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>

<<<<<<< HEAD
<div class="form-group">
    <label>Tipo de usuario</label>
    <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
        <option value="">-- Selecciona --</option>
        <option value="alumno"     {{ old('tipo_usuario') == 'alumno'     ? 'selected' : '' }}>Alumno</option>
        <option value="instructor" {{ old('tipo_usuario') == 'instructor' ? 'selected' : '' }}>Instructor</option>
        <option value="admin"      {{ old('tipo_usuario') == 'admin'      ? 'selected' : '' }}>Admin</option>
    </select>
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
=======
                            <div class="form-group">
                                <label>Tipo de usuario</label>
                                {{-- Añadimos el ID 'tipo_usuario' para el script --}}
                                <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                                    <option value="">-- Selecciona --</option>
                                    <option value="alumno"     {{ old('tipo_usuario') == 'alumno'     ? 'selected' : '' }}>Alumno</option>
                                    <option value="instructor" {{ old('tipo_usuario') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                                    <option value="admin"      {{ old('tipo_usuario') == 'admin'      ? 'selected' : '' }}>Admin</option>
                                     <option value="coordinador"      {{ old('tipo_usuario') == 'coordinador'      ? 'selected' : '' }}>Coordinador</option>
                                </select>
                            </div>

                            {{-- CAMPO CARRERA (Oculto por defecto) --}}
                            <div class="form-group" id="campo_carrera" style="display: none; background: #f4f6f9; padding: 15px; border-radius: 5px; border-left: 5px solid #6777ef;">
                                <label>Asignar Carrera <span class="text-danger">*</span></label>
                                <select name="id_carrera" class="form-control">
                                    <option value="">-- Selecciona una carrera --</option>
                                    <option value="1">Ingeniería en Sistemas Computacionales</option>
                                    <option value="2">Ingeniería en Gestión Empresarial</option>
                                    <option value="3">Ingeniería Civil</option>
                                    <option value="4">Ingeniería Industrial</option>
                                    <option value="5">Ingeniería Mecánica</option>
                                    <option value="6">Ingeniería Electrónica</option>
                                    <option value="7">Ingeniería Eléctrica</option>
                                    <option value="8">Ingeniería Química</option>
                                    <option value="9">Licenciatura en Administración</option>
                                    <option value="10">Licenciatura en Contaduría</option>
                                </select>
                            </div>
>>>>>>> 6684bd81b35d346dfbc15d05ac3906f3469b852e

                            <div class="form-group">
                                <label>Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Confirmar contraseña</label>
                                <input type="password" name="confirm-password" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Rol</label>
                                <select name="roles" class="form-control" required>
                                    <option value="">-- Selecciona un rol --</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol }}" {{ old('roles') == $rol ? 'selected' : '' }}>{{ $rol }}</option>
                                    @endforeach
                                </select>
                            </div>

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

<<<<<<< HEAD
@section('scripts')
<script>
    function toggleCamposExtra() {
        var tipo = document.getElementById('tipo_usuario').value;
        document.getElementById('campos_instructor').style.display =
            tipo === 'instructor' ? 'block' : 'none';
    }
    document.getElementById('tipo_usuario').addEventListener('change', toggleCamposExtra);
    // Mostrar al cargar si hay old() con valor
    toggleCamposExtra();
</script>
@endsection
=======
{{-- Script para mostrar/ocultar carrera --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectTipo = document.getElementById('tipo_usuario');
        const divCarrera = document.getElementById('campo_carrera');

        function toggleCarrera() {
            if (selectTipo.value === 'alumno') {
                divCarrera.style.display = 'block';
            } else {
                divCarrera.style.display = 'none';
            }
        }

        selectTipo.addEventListener('change', toggleCarrera);
        
        // Ejecutar al cargar por si hay un error de validación y el valor quedó marcado
        toggleCarrera();
    });
</script>
>>>>>>> 6684bd81b35d346dfbc15d05ac3906f3469b852e
@endsection