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

                            <div class="form-group" id="campo_num_control">
                                <label>Número de Control</label>
                                <input type="text" name="num_control" class="form-control" value="{{ old('num_control') }}">
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
@endsection