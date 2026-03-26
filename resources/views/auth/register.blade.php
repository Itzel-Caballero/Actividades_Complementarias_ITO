@extends('layouts.auth_app')
@section('title')
    Registro de Usuario
@endsection
@section('content')
    {{-- La propiedad max-height y overflow permiten que el formulario sea desplazable si es muy largo --}}
    <div class="card" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 15px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37); margin-bottom: 30px; max-height: 85vh; overflow-y: auto;">
        
        <div class="card-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <h4 style="color: #ffffff; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Crear Cuenta</h4>
        </div>

        <div class="card-body pt-1">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger p-2" style="background: rgba(220, 53, 69, 0.2); border: none; color: #ff8585;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    {{-- Tipo de usuario --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label style="color: #e0e0e0;">Tipo de Usuario <span class="text-danger">*</span></label>
                            <select name="tipo_usuario" id="tipo_usuario"
                                    class="form-control{{ $errors->has('tipo_usuario') ? ' is-invalid' : '' }}"
                                    style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;">
                                <option value="" style="color: black;">-- Selecciona --</option>
                                <option value="alumno" {{ old('tipo_usuario') == 'alumno' ? 'selected' : '' }} style="color: black;">Alumno</option>
                                <option value="instructor" {{ old('tipo_usuario') == 'instructor' ? 'selected' : '' }} style="color: black;">Instructor</option>
                                <option value="admin" {{ old('tipo_usuario') == 'admin' ? 'selected' : '' }} style="color: black;">Administrador</option>
                                <option value="coordinador" {{ old('tipo_usuario') == 'coordinador' ? 'selected' : '' }} style="color: black;">Coordinador</option>
                            </select>
                        </div>
                    </div>

                    {{-- Campo Carrera (Aparece solo si es Alumno) --}}
                    <div class="col-md-12" id="campo_carrera" style="display: none;">
                        <div class="form-group" style="background: rgba(111, 66, 193, 0.1); padding: 10px; border-radius: 8px; border: 1px dashed #6f42c1;">
                            <label style="color: #bb86fc; font-weight: bold;"><i class="fas fa-graduation-cap"></i> Carrera <span class="text-danger">*</span></label>
                            <select name="id_carrera" class="form-control" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: white;">
                                <option value="" style="color: black;">-- Selecciona tu carrera --</option>
                                <option value="1" style="color: black;">Ingeniería en Sistemas Computacionales</option>
                                <option value="2" style="color: black;">Ingeniería en Gestión Empresarial</option>
                                <option value="3" style="color: black;">Ingeniería Civil</option>
                                <option value="4" style="color: black;">Ingeniería Industrial</option>
                                <option value="5" style="color: black;">Ingeniería Mecánica</option>
                                <option value="6" style="color: black;">Ingeniería Electrónica</option>
                                <option value="7" style="color: black;">Ingeniería Eléctrica</option>
                                <option value="8" style="color: black;">Ingeniería Química</option>
                                <option value="9" style="color: black;">Licenciatura en Administración</option>
                                <option value="10" style="color: black;">Licenciatura en Contaduría</option>
                            </select>
                        </div>
                    </div>

                    {{-- Nombre --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label style="color: #e0e0e0;">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;" value="{{ old('nombre') }}" placeholder="Ej. Juan Carlos" required>
                        </div>
                    </div>

                    {{-- Apellidos --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color: #e0e0e0;">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" name="apellido_paterno" class="form-control" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;" value="{{ old('apellido_paterno') }}" placeholder="Ej. García" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color: #e0e0e0;">Apellido Materno</label>
                            <input type="text" name="apellido_materno" class="form-control" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;" value="{{ old('apellido_materno') }}" placeholder="Ej. López">
                        </div>
                    </div>

                    {{-- Correo --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color: #e0e0e0;">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;" value="{{ old('email') }}" placeholder="tucorreo@ejemplo.com" required>
                        </div>
                    </div>

                    {{-- Número de control --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color: #e0e0e0;">Número de Control</label>
                            <input type="number" name="num_control" class="form-control" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;" value="{{ old('num_control') }}" placeholder="Ej. 20310001">
                        </div>
                    </div>

                    {{-- Teléfono --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label style="color: #e0e0e0;">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;" value="{{ old('telefono') }}" placeholder="Ej. 9511234567">
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color: #e0e0e0;">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;" placeholder="Mínimo 6 caracteres" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color: #e0e0e0;">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;" placeholder="Repite tu contraseña" required>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" style="background: #6f42c1; border: none; box-shadow: 0 4px 14px 0 rgba(111, 66, 193, 0.39);">
                                Crear Cuenta
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center" style="color: #ffffff; padding-bottom: 50px;">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color: #bb86fc; font-weight: bold;">Inicia sesión aquí</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectTipo = document.getElementById('tipo_usuario');
            const divCarrera = document.getElementById('campo_carrera');

            function toggleCarrera() {
                if (selectTipo.value === 'alumno') {
                    divCarrera.style.setProperty('display', 'block', 'important');
                } else {
                    divCarrera.style.setProperty('display', 'none', 'important');
                }
            }

            selectTipo.addEventListener('change', toggleCarrera);
            
            // Ejecutar al inicio por si hay valores previos de 'old'
            toggleCarrera();
        });
    </script>
@endsection