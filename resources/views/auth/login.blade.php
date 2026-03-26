@extends('layouts.auth_app')
@section('title')
    Iniciar Sesión
@endsection
@section('content')
    {{-- He añadido estilos en línea para forzar la transparencia y el desenfoque --}}
    <div class="card" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 15px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);">
        
        <div class="card-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <h4 style="color: #ffffff; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Iniciar Sesión</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
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

                <div class="form-group">
                    {{-- Cambié el color del label a blanco para que se lea --}}
                    <label for="email" style="color: #e0e0e0;">Correo Electrónico</label>
                    <input id="email" type="email"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                           style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;"
                           name="email"
                           placeholder="tucorreo@ejemplo.com"
                           value="{{ old('email') }}"
                           tabindex="1" autofocus required>
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-block">
                        <label for="password" class="control-label" style="color: #e0e0e0;">Contraseña</label>
                        <div class="float-right">
                            {{-- El link ahora es de un color púrpura claro para resaltar --}}
                            <a href="{{ route('password.request') }}" class="text-small" style="color: #bb86fc;">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>
                    <input id="password" type="password"
                           placeholder="••••••••"
                           style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: white;"
                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                           name="password" tabindex="2" required>
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="remember" class="custom-control-input"
                               tabindex="3" id="remember">
                        <label class="custom-control-label" for="remember" style="color: #e0e0e0;">Recordarme</label>
                    </div>
                </div>

                <div class="form-group">
                    {{-- Botón con color púrpura vibrante --}}
                    <button type="submit" class="btn btn-primary btn-lg btn-block" 
                            style="background: #6f42c1; border: none; box-shadow: 0 4px 14px 0 rgba(111, 66, 193, 0.39);" tabindex="4">
                        Ingresar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Texto de abajo también en blanco --}}
    <div class="mt-5 text-center" style="color: #ffffff;">
        ¿No tienes cuenta? <a href="{{ route('register') }}" style="color: #bb86fc; font-weight: bold;">Regístrate aquí</a>
    </div>
@endsection