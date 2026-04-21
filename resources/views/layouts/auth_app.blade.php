<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title') | {{ config('app.name') }}</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #e8f4fd 0%, #f0e8ff 50%, #fce8f3 100%);
            display: flex; align-items: center; justify-content: center;
            overflow-x: hidden; overflow-y: auto;
        }

        /* Burbujas decorativas */
        .bg-circles { position:fixed; top:0; left:0; width:100%; height:100%; z-index:0; pointer-events:none; overflow:hidden; }
        .bg-circles span { position:absolute; border-radius:50%; opacity:.18; animation:floatUp linear infinite; }
        .bg-circles span:nth-child(1){ width:80px; height:80px; left:8%;  top:70%; background:#a78bfa; animation-duration:18s; }
        .bg-circles span:nth-child(2){ width:40px; height:40px; left:22%; top:80%; background:#818cf8; animation-duration:13s; animation-delay:3s; }
        .bg-circles span:nth-child(3){ width:60px; height:60px; left:55%; top:75%; background:#c084fc; animation-duration:16s; animation-delay:1s; }
        .bg-circles span:nth-child(4){ width:50px; height:50px; left:72%; top:85%; background:#f472b6; animation-duration:14s; animation-delay:5s; }
        .bg-circles span:nth-child(5){ width:30px; height:30px; left:88%; top:65%; background:#60a5fa; animation-duration:11s; animation-delay:2s; }
        @keyframes floatUp {
            0%  { transform:translateY(0) scale(1); opacity:.18; }
            80% { opacity:.22; }
            100%{ transform:translateY(-100vh) scale(1.15); opacity:0; }
        }

        /* ─── Tarjeta principal ─── */
        .auth-card {
            position:relative; z-index:1;
            width:100%; max-width:960px; min-height:570px;
            margin:24px 16px; border-radius:24px; overflow:hidden;
            box-shadow: 0 4px 6px rgba(139,92,246,.08), 0 20px 50px rgba(139,92,246,.15), 0 0 0 1px rgba(139,92,246,.07);
            display:flex;
        }

        /* ─── Panel izquierdo (morado) ─── */
        .side-panel {
            flex:0 0 36%;
            background:linear-gradient(155deg,#7c3aed 0%,#6d28d9 45%,#5b21b6 100%);
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:48px 32px; position:relative; overflow:hidden;
        }
        .side-panel::before { content:''; position:absolute; width:280px; height:280px; border-radius:50%; background:rgba(255,255,255,.08); top:-80px; left:-80px; }
        .side-panel::after  { content:''; position:absolute; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.06); bottom:-40px; right:-50px; }
        .logo-wrap { position:relative; z-index:1; width:130px; height:130px; background:rgba(255,255,255,.15); border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:20px; }
        .logo-wrap img { width:88px; }
        .side-content { position:relative; z-index:1; text-align:center; }
        .side-content h2 { color:#fff; font-size:1.45rem; font-weight:700; margin-bottom:10px; }
        .side-content p  { color:rgba(255,255,255,.78); font-size:.875rem; line-height:1.65; margin-bottom:28px; }
        .btn-outline-white { display:inline-block; padding:10px 28px; border:2px solid rgba(255,255,255,.7); border-radius:50px; color:#fff; background:transparent; font-size:.88rem; font-weight:600; text-decoration:none; cursor:pointer; transition:background .25s; }
        .btn-outline-white:hover { background:rgba(255,255,255,.2); color:#fff; text-decoration:none; }

        /* ─── Panel de formularios ─── */
        .forms-panel { flex:1; background:#fff; overflow:hidden; position:relative; }
        .forms-track { display:flex; width:200%; height:100%; transition:transform .55s cubic-bezier(.77,0,.175,1); }
        body.show-register .forms-track { transform:translateX(-50%); }
        .form-slide { width:50%; min-width:0; overflow-y:auto; }
        #slideLogin    { display:flex; flex-direction:column; justify-content:center; min-height:570px; padding:44px 44px; }
        #slideRegister { padding:28px 40px 32px; min-height:570px; }

        /* ─── Tipografía ─── */
        .form-slide h3   { color:#1e1b4b; font-size:1.5rem; font-weight:700; margin-bottom:4px; }
        .form-slide .sub { color:#6b7280; font-size:.82rem; margin-bottom:16px; }
        .form-slide label{ color:#374151; font-size:.8rem; font-weight:600; margin-bottom:3px; display:block; }

        /* ─── Inputs ─── */
        .form-slide .form-control {
            background:#f9fafb; border:1.5px solid #e5e7eb; border-radius:10px;
            color:#111827; font-size:.88rem; padding:9px 13px; width:100%;
            transition:border-color .2s, box-shadow .2s;
        }
        .form-slide .form-control:focus {
            background:#fff; border-color:#7c3aed;
            box-shadow:0 0 0 3px rgba(124,58,237,.12); outline:none; color:#111827;
        }
        .form-slide .form-control::placeholder { color:#9ca3af; }

        /* ─── Botón principal ─── */
        .btn-auth { width:100%; padding:12px; border:none; border-radius:50px; background:linear-gradient(135deg,#7c3aed,#6d28d9); color:#fff; font-size:.95rem; font-weight:700; cursor:pointer; transition:opacity .2s,transform .15s; box-shadow:0 4px 16px rgba(109,40,217,.35); margin-top:4px; }
        .btn-auth:hover  { opacity:.9; transform:translateY(-1px); }
        .btn-auth:active { transform:translateY(0); }

        /* ─── Divisor ─── */
        .divider { display:flex; align-items:center; gap:10px; margin:14px 0 10px; }
        .divider span { flex:1; height:1px; background:#e5e7eb; }
        .divider em   { color:#9ca3af; font-size:.75rem; font-style:normal; }

        /* ─── Pie ─── */
        .footer-txt { text-align:center; color:#6b7280; font-size:.82rem; }
        .footer-txt a { color:#7c3aed; font-weight:700; text-decoration:none; }
        .footer-txt a:hover { color:#5b21b6; text-decoration:underline; }

        /* ─── TABS DE TIPO DE REGISTRO ─── */
        .reg-tabs { display:flex; gap:8px; margin-bottom:16px; }
        .reg-tab {
            flex:1; padding:10px 8px; border:2px solid #e5e7eb; border-radius:12px;
            background:#f9fafb; color:#6b7280; font-size:.85rem; font-weight:600;
            cursor:pointer; text-align:center; transition:all .2s; user-select:none;
        }
        .reg-tab i { display:block; font-size:1.2rem; margin-bottom:4px; }
        .reg-tab:hover { border-color:#a78bfa; color:#7c3aed; background:#f5f3ff; }
        .reg-tab.active {
            border-color:#7c3aed; background:linear-gradient(135deg,#f5f3ff,#ede9fe);
            color:#5b21b6; box-shadow:0 2px 8px rgba(124,58,237,.18);
        }

        /* ─── Secciones de formulario según tipo ─── */
        .reg-section { display:none; }
        .reg-section.active { display:block; }

        /* ─── Caja especial para carrera/departamento ─── */
        .highlight-box {
            background:linear-gradient(135deg,#f5f3ff,#ede9fe);
            border:1.5px solid #c4b5fd; border-radius:12px;
            padding:12px 14px; margin-bottom:12px;
        }
        .highlight-box > label { color:#5b21b6 !important; font-size:.82rem; font-weight:700; margin-bottom:6px; }

        /* ─── Select personalizado (evita truncado nativo) ─── */
        .custom-select-wrap { position:relative; width:100%; }
        .cs-trigger {
            display:flex; align-items:center; justify-content:space-between;
            width:100%; padding:9px 13px; background:#fff;
            border:1.5px solid #a78bfa; border-radius:10px;
            color:#9ca3af; font-size:.88rem; cursor:pointer; user-select:none;
            transition:border-color .2s, box-shadow .2s; text-align:left;
        }
        .cs-trigger.has-value { color:#111827; }
        .cs-trigger:focus, .cs-trigger.open {
            border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.12); outline:none;
        }
        .cs-trigger .cs-arrow { flex-shrink:0; margin-left:8px; transition:transform .2s; color:#6b7280; font-size:.75rem; }
        .cs-trigger.open .cs-arrow { transform:rotate(180deg); }
        .cs-trigger .cs-text { flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; min-width:0; }
        .cs-dropdown {
            display:none; position:absolute; top:calc(100% + 4px); left:0; right:0;
            background:#fff; border:1.5px solid #c4b5fd; border-radius:10px;
            box-shadow:0 8px 24px rgba(109,40,217,.15); z-index:9999; max-height:200px; overflow-y:auto;
        }
        .cs-dropdown.open { display:block; }
        .cs-option {
            padding:9px 13px; font-size:.86rem; color:#111827; cursor:pointer;
            white-space:normal; line-height:1.4; transition:background .15s;
        }
        .cs-option:hover          { background:#f5f3ff; color:#5b21b6; }
        .cs-option.selected       { background:#ede9fe; color:#5b21b6; font-weight:600; }
        .cs-option[data-value=""] { color:#9ca3af; font-style:italic; }

        /* ─── Cajas de error ─── */
        .err-box { background:#fef2f2; border:1.5px solid #fca5a5; border-radius:10px; color:#b91c1c; padding:10px 14px; font-size:.82rem; margin-bottom:14px; }
        .err-box ul { margin:0; padding-left:16px; }

        /* ─── Misc ─── */
        .link-p { color:#7c3aed; text-decoration:none; font-size:.8rem; }
        .link-p:hover { color:#5b21b6; }
        .chk { color:#6b7280 !important; font-size:.82rem; font-weight:400 !important; }
        .req { color:#dc2626; }
        .hint { color:#9ca3af; font-size:.75rem; margin-top:3px; display:block; font-weight:400; }

        /* ─── Responsive ─── */
        @media (max-width:640px) {
            .side-panel { display:none; }
            .auth-card  { margin:0; border-radius:0; min-height:100vh; }
            #slideLogin    { padding:36px 22px; min-height:100vh; }
            #slideRegister { padding:20px 18px 28px; }
        }
        @media (min-width:641px) and (max-width:860px) {
            .side-panel { flex:0 0 32%; }
            #slideLogin    { padding:36px 28px; }
            #slideRegister { padding:22px 26px 28px; }
        }
    </style>
</head>

@php
    $carrerasLayout      = \App\Models\Carrera::orderBy('nombre')->get();
    $departamentosLayout = \App\Models\Departamento::orderBy('nombre')->get();
    $oldTipo             = old('tipo_registro', 'alumno');
@endphp

<body class="{{ request()->routeIs('register') ? 'show-register' : '' }}">

<div class="bg-circles">
    <span></span><span></span><span></span><span></span><span></span>
</div>

<div class="auth-card">

    {{-- ══ Panel izquierdo ══ --}}
    <div class="side-panel">
        <div class="logo-wrap">
            <img src="{{ asset('img/logo.png') }}" alt="Logo ITO">
        </div>
        <div class="side-content" id="sideLogin" style="{{ request()->routeIs('register') ? 'display:none' : '' }}">
            <h2>¡Bienvenido!</h2>
            <p>Accede a tu cuenta para gestionar tus actividades complementarias.</p>
            <a href="#" class="btn-outline-white" id="btnShowReg">Crear cuenta</a>
        </div>
        <div class="side-content" id="sideReg" style="{{ request()->routeIs('register') ? '' : 'display:none' }}">
            <h2>¿Ya tienes cuenta?</h2>
            <p>Inicia sesión para continuar con tus actividades registradas.</p>
            <a href="#" class="btn-outline-white" id="btnShowLog">Iniciar sesión</a>
        </div>
    </div>

    {{-- ══ Panel de formularios ══ --}}
    <div class="forms-panel">
        <div class="forms-track">

            {{-- ════════ LOGIN ════════ --}}
            <div class="form-slide" id="slideLogin">
                <h3>Iniciar Sesión</h3>
                <p class="sub">Accede con tu correo institucional</p>

                @if($errors->any() && !request()->routeIs('register'))
                    <div class="err-box">
                        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="err-box" style="background:#fff7ed;border-color:#fdba74;color:#c2410c;">
                        <i class="fas fa-ban mr-1"></i> {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Correo electrónico</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="tucorreo@ejemplo.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="form-group mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0">Contraseña</label>
                            <a href="{{ route('password.request') }}" class="link-p">¿Olvidaste tu contraseña?</a>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-group mb-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="remember" class="custom-control-input" id="remember">
                            <label class="custom-control-label chk" for="remember">Recordarme</label>
                        </div>
                    </div>
                    <button type="submit" class="btn-auth">
                        <i class="fas fa-sign-in-alt mr-2"></i>Ingresar
                    </button>
                </form>

                <div class="divider"><span></span><em>o</em><span></span></div>
                <p class="footer-txt">¿No tienes cuenta? <a href="#" id="btnShowReg2">Regístrate aquí</a></p>
            </div>

            {{-- ════════ REGISTRO ════════ --}}
            <div class="form-slide" id="slideRegister">
                <h3>Crear Cuenta</h3>
                <p class="sub">Completa el formulario según tu perfil</p>

                @if($errors->any() && request()->routeIs('register'))
                    <div class="err-box">
                        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="formRegister">
                    @csrf

                    {{-- ── TABS: Alumno / Instructor ── --}}
                    <input type="hidden" name="tipo_registro" id="tipoRegistro" value="{{ $oldTipo }}">
                    <div class="reg-tabs mb-3">
                        <div class="reg-tab {{ $oldTipo === 'alumno' ? 'active' : '' }}"
                             id="tabAlumno" onclick="switchTab('alumno')">
                            <i class="fas fa-user-graduate"></i>Alumno
                        </div>
                        <div class="reg-tab {{ $oldTipo === 'instructor' ? 'active' : '' }}"
                             id="tabInstructor" onclick="switchTab('instructor')">
                            <i class="fas fa-chalkboard-teacher"></i>Instructor
                        </div>
                    </div>

                    {{-- ════════════════════════════════
                         SECCIÓN ALUMNO
                    ════════════════════════════════ --}}
                    <div class="reg-section {{ $oldTipo === 'alumno' ? 'active' : '' }}" id="secAlumno">

                        {{-- Carrera (select personalizado) --}}
                        <div class="highlight-box">
                            <label><i class="fas fa-graduation-cap mr-1"></i>Carrera <span class="req">*</span></label>
                            <input type="hidden" name="id_carrera" id="inputCarrera" value="{{ old('id_carrera','') }}">
                            <div class="custom-select-wrap">
                                <div class="cs-trigger {{ old('id_carrera') ? 'has-value' : '' }}"
                                     id="csTriggerCarrera" tabindex="0" role="combobox" aria-expanded="false">
                                    <span class="cs-text" id="csTextCarrera">
                                        @php $oldC = $carrerasLayout->firstWhere('id_carrera', old('id_carrera')); @endphp
                                        {{ $oldC ? $oldC->nombre : '— Selecciona tu carrera —' }}
                                    </span>
                                    <i class="fas fa-chevron-down cs-arrow"></i>
                                </div>
                                <div class="cs-dropdown" id="csDropdownCarrera" role="listbox">
                                    <div class="cs-option" data-value="">— Selecciona tu carrera —</div>
                                    @foreach($carrerasLayout as $c)
                                        <div class="cs-option {{ old('id_carrera') == $c->id_carrera ? 'selected' : '' }}"
                                             data-value="{{ $c->id_carrera }}">{{ $c->nombre }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Semestre --}}
                        <div class="form-group mb-2">
                            <label>Semestre que cursas <span class="req">*</span></label>
                            <select name="semestre_actual" class="form-control" id="selectSemestre">
                                <option value="">— Selecciona tu semestre —</option>
                                @for($s = 1; $s <= 12; $s++)
                                    <option value="{{ $s }}" {{ old('semestre_actual') == $s ? 'selected' : '' }}>
                                        {{ $s }}° Semestre
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Número de control --}}
                        <div class="form-group mb-2">
                            <label>Número de control <span class="req">*</span></label>
                            <input type="text" name="num_control" class="form-control"
                                   placeholder="Ej: 20310001 ó C20310001"
                                   value="{{ old('num_control') }}" maxlength="9">
                            <span class="hint">
                                <i class="fas fa-info-circle mr-1"></i>
                                8 dígitos numéricos <em>(20310001)</em> o "C" + 8 dígitos <em>(C20310001)</em>
                            </span>
                        </div>

                    </div>{{-- /secAlumno --}}

                    {{-- ════════════════════════════════
                         SECCIÓN INSTRUCTOR
                    ════════════════════════════════ --}}
                    <div class="reg-section {{ $oldTipo === 'instructor' ? 'active' : '' }}" id="secInstructor">

                        {{-- Departamento (select personalizado) --}}
                        <div class="highlight-box">
                            <label><i class="fas fa-building mr-1"></i>Departamento <span class="req">*</span></label>
                            <input type="hidden" name="id_departamento" id="inputDepartamento" value="{{ old('id_departamento','') }}">
                            <div class="custom-select-wrap">
                                <div class="cs-trigger {{ old('id_departamento') ? 'has-value' : '' }}"
                                     id="csTriggerDepto" tabindex="0" role="combobox" aria-expanded="false">
                                    <span class="cs-text" id="csTextDepto">
                                        @php $oldD = $departamentosLayout->firstWhere('id_departamento', old('id_departamento')); @endphp
                                        {{ $oldD ? $oldD->nombre : '— Selecciona tu departamento —' }}
                                    </span>
                                    <i class="fas fa-chevron-down cs-arrow"></i>
                                </div>
                                <div class="cs-dropdown" id="csDropdownDepto" role="listbox">
                                    <div class="cs-option" data-value="">— Selecciona tu departamento —</div>
                                    @foreach($departamentosLayout as $d)
                                        <div class="cs-option {{ old('id_departamento') == $d->id_departamento ? 'selected' : '' }}"
                                             data-value="{{ $d->id_departamento }}">{{ $d->nombre }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Especialidad --}}
                        <div class="form-group mb-2">
                            <label>Especialidad <span class="req">*</span></label>
                            <input type="text" name="especialidad" class="form-control"
                                   placeholder="Ej: Desarrollo Web, Cultura Física, Robótica…"
                                   value="{{ old('especialidad') }}">
                        </div>

                    </div>{{-- /secInstructor --}}

                    {{-- ════════════════════════════════
                         CAMPOS COMUNES
                    ════════════════════════════════ --}}
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label>Nombre(s) <span class="req">*</span></label>
                            <input type="text" name="nombre" class="form-control"
                                   placeholder="Ej: Juan Carlos" value="{{ old('nombre') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 pr-1">
                            <div class="form-group mb-2">
                                <label>Ap. Paterno <span class="req">*</span></label>
                                <input type="text" name="apellido_paterno" class="form-control"
                                       placeholder="García" value="{{ old('apellido_paterno') }}" required>
                            </div>
                        </div>
                        <div class="col-6 pl-1">
                            <div class="form-group mb-2">
                                <label>Ap. Materno</label>
                                <input type="text" name="apellido_materno" class="form-control"
                                       placeholder="López" value="{{ old('apellido_materno') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-7 pr-1">
                            <div class="form-group mb-2">
                                <label>Correo electrónico <span class="req">*</span></label>
                                <input type="email" name="email" class="form-control"
                                       placeholder="correo@ito.edu.mx" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="col-5 pl-1">
                            <div class="form-group mb-2">
                                <label>Teléfono <span class="req">*</span></label>
                                <input type="text" name="telefono" class="form-control"
                                       placeholder="9511234567" value="{{ old('telefono') }}"
                                       maxlength="10" inputmode="numeric">
                                <span class="hint">Exactamente 10 dígitos</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 pr-1">
                            <div class="form-group mb-3">
                                <label>Contraseña <span class="req">*</span></label>
                                <input type="password" name="password" class="form-control"
                                       placeholder="Mín. 6 caracteres" required>
                            </div>
                        </div>
                        <div class="col-6 pl-1">
                            <div class="form-group mb-3">
                                <label>Confirmar <span class="req">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       placeholder="Repite" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth">
                        <i class="fas fa-user-plus mr-2"></i>Crear Cuenta
                    </button>
                </form>

                <div class="divider"><span></span><em>o</em><span></span></div>
                <p class="footer-txt">¿Ya tienes cuenta? <a href="#" id="btnShowLog2">Inicia sesión aquí</a></p>
            </div>{{-- /slideRegister --}}

        </div>{{-- /forms-track --}}
    </div>{{-- /forms-panel --}}

</div>{{-- /auth-card --}}

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script>
/* ══════════════════════════════════════════════════════
   Cambio Login ↔ Registro
══════════════════════════════════════════════════════ */
var body    = document.body;
var sideLog = document.getElementById('sideLogin');
var sideReg = document.getElementById('sideReg');

function goRegister() {
    body.classList.add('show-register');
    sideLog.style.display = 'none';
    sideReg.style.display = 'block';
}
function goLogin() {
    body.classList.remove('show-register');
    sideLog.style.display = 'block';
    sideReg.style.display = 'none';
}
document.getElementById('btnShowReg').addEventListener('click',  function(e){ e.preventDefault(); goRegister(); });
document.getElementById('btnShowReg2').addEventListener('click', function(e){ e.preventDefault(); goRegister(); });
document.getElementById('btnShowLog').addEventListener('click',  function(e){ e.preventDefault(); goLogin(); });
document.getElementById('btnShowLog2').addEventListener('click', function(e){ e.preventDefault(); goLogin(); });

/* ══════════════════════════════════════════════════════
   Tabs Alumno / Instructor
══════════════════════════════════════════════════════ */
function switchTab(tipo) {
    // Actualizar hidden input
    document.getElementById('tipoRegistro').value = tipo;

    // Tabs visuales
    document.getElementById('tabAlumno').classList.toggle('active',     tipo === 'alumno');
    document.getElementById('tabInstructor').classList.toggle('active', tipo === 'instructor');

    // Secciones
    document.getElementById('secAlumno').classList.toggle('active',     tipo === 'alumno');
    document.getElementById('secInstructor').classList.toggle('active', tipo === 'instructor');

    // Requeridos dinámicos según tipo
    // Alumno
    var numCtrl   = document.querySelector('input[name="num_control"]');
    var idCarrera = document.getElementById('inputCarrera');
    var semestre  = document.getElementById('selectSemestre');
    // Instructor
    var idDepto      = document.getElementById('inputDepartamento');
    var especialidad = document.querySelector('input[name="especialidad"]');

    if (tipo === 'alumno') {
        numCtrl   && (numCtrl.required   = true);
        semestre  && (semestre.required  = true);
    } else {
        numCtrl   && (numCtrl.required   = false);
        semestre  && (semestre.required  = false);
    }
}

/* ══════════════════════════════════════════════════════
   Fábrica de select personalizado
   Recibe: triggerId, dropdownId, textId, hiddenId
══════════════════════════════════════════════════════ */
function initCustomSelect(triggerId, dropdownId, textId, hiddenId) {
    var trigger  = document.getElementById(triggerId);
    var dropdown = document.getElementById(dropdownId);
    var csText   = document.getElementById(textId);
    var hidden   = document.getElementById(hiddenId);

    if (!trigger || !dropdown) return;

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        var isOpen = dropdown.classList.contains('open');
        closeAll();
        if (!isOpen) { dropdown.classList.add('open'); trigger.classList.add('open'); trigger.setAttribute('aria-expanded','true'); }
    });

    trigger.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); trigger.click(); }
        if (e.key === 'Escape') closeAll();
    });

    dropdown.querySelectorAll('.cs-option').forEach(function(opt) {
        opt.addEventListener('click', function(e) {
            e.stopPropagation();
            var val = this.dataset.value;
            hidden.value = val;
            csText.textContent = this.textContent.trim();
            trigger.classList.toggle('has-value', val !== '');
            dropdown.querySelectorAll('.cs-option').forEach(function(o){ o.classList.remove('selected'); });
            this.classList.add('selected');
            closeAll();
            trigger.focus();
        });
    });
}

function closeAll() {
    document.querySelectorAll('.cs-dropdown.open').forEach(function(d){ d.classList.remove('open'); });
    document.querySelectorAll('.cs-trigger.open').forEach(function(t){ t.classList.remove('open'); t.setAttribute('aria-expanded','false'); });
}
document.addEventListener('click', closeAll);

// Inicializar ambos selects
initCustomSelect('csTriggerCarrera', 'csDropdownCarrera', 'csTextCarrera', 'inputCarrera');
initCustomSelect('csTriggerDepto',   'csDropdownDepto',   'csTextDepto',   'inputDepartamento');

/* ══════════════════════════════════════════════════════
   Solo dígitos en campo teléfono
══════════════════════════════════════════════════════ */
document.querySelector('input[name="telefono"]').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').slice(0, 10);
});

/* ══════════════════════════════════════════════════════
   Formateo automático num_control:
   - Convierte la primera letra a mayúscula si es "c"
   - Solo permite: [C][dígitos] hasta 9 chars  o  [dígitos] hasta 8 chars
══════════════════════════════════════════════════════ */
var numCtrlInput = document.querySelector('input[name="num_control"]');
if (numCtrlInput) {
    numCtrlInput.addEventListener('input', function() {
        var v = this.value.toUpperCase();
        // Si empieza con C, permitir C + hasta 8 dígitos
        if (v.startsWith('C')) {
            v = 'C' + v.slice(1).replace(/\D/g, '').slice(0, 8);
        } else {
            // Solo dígitos, hasta 8
            v = v.replace(/\D/g, '').slice(0, 8);
        }
        this.value = v;
    });
}
</script>
</body>
</html>
