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

        /* Burbujas */
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

        /* Tarjeta */
        .auth-card {
            position:relative; z-index:1;
            width:100%; max-width:940px; min-height:570px;
            margin:24px 16px; border-radius:24px; overflow:hidden;
            box-shadow: 0 4px 6px rgba(139,92,246,.08), 0 20px 50px rgba(139,92,246,.15), 0 0 0 1px rgba(139,92,246,.07);
            display:flex;
        }

        /* Panel morado */
        .side-panel {
            flex:0 0 38%;
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

        /* Panel formularios */
        .forms-panel { flex:1; background:#fff; overflow:hidden; position:relative; }
        .forms-track { display:flex; width:200%; height:100%; transition:transform .55s cubic-bezier(.77,0,.175,1); }
        body.show-register .forms-track { transform:translateX(-50%); }
        .form-slide { width:50%; min-width:0; overflow-y:auto; }
        #slideLogin    { display:flex; flex-direction:column; justify-content:center; min-height:570px; padding:44px 44px; }
        #slideRegister { padding:30px 44px 36px; min-height:570px; }

        /* Tipografía */
        .form-slide h3   { color:#1e1b4b; font-size:1.5rem; font-weight:700; margin-bottom:4px; }
        .form-slide .sub { color:#6b7280; font-size:.82rem; margin-bottom:22px; }
        .form-slide label{ color:#374151; font-size:.8rem; font-weight:600; margin-bottom:4px; display:block; }

        /* Inputs */
        .form-slide .form-control {
            background:#f9fafb; border:1.5px solid #e5e7eb; border-radius:10px;
            color:#111827; font-size:.9rem; padding:10px 14px; width:100%;
            transition:border-color .2s, box-shadow .2s;
        }
        .form-slide .form-control:focus {
            background:#fff; border-color:#7c3aed;
            box-shadow:0 0 0 3px rgba(124,58,237,.12); outline:none; color:#111827;
        }
        .form-slide .form-control::placeholder { color:#9ca3af; }

        /* Botón */
        .btn-auth { width:100%; padding:13px; border:none; border-radius:50px; background:linear-gradient(135deg,#7c3aed,#6d28d9); color:#fff; font-size:.95rem; font-weight:700; cursor:pointer; transition:opacity .2s,transform .15s; box-shadow:0 4px 16px rgba(109,40,217,.35); margin-top:4px; }
        .btn-auth:hover  { opacity:.9; transform:translateY(-1px); }
        .btn-auth:active { transform:translateY(0); }

        /* Divisor */
        .divider { display:flex; align-items:center; gap:10px; margin:18px 0 12px; }
        .divider span { flex:1; height:1px; background:#e5e7eb; }
        .divider em   { color:#9ca3af; font-size:.75rem; font-style:normal; }

        /* Pie */
        .footer-txt { text-align:center; color:#6b7280; font-size:.82rem; }
        .footer-txt a { color:#7c3aed; font-weight:700; text-decoration:none; }
        .footer-txt a:hover { color:#5b21b6; text-decoration:underline; }

        /* Caja carrera */
        .carrera-box { background:linear-gradient(135deg,#f5f3ff,#ede9fe); border:1.5px solid #c4b5fd; border-radius:12px; padding:14px 16px; margin-bottom:15px; }
        .carrera-box > label { color:#5b21b6 !important; font-size:.85rem; font-weight:700; margin-bottom:8px; }

        /* ══════════════════════════════════════════════════════
           SELECT PERSONALIZADO — resuelve el truncado nativo
           ══════════════════════════════════════════════════════ */
        .custom-select-wrap {
            position: relative;
            width: 100%;
        }
        /* Input oculto que guarda el valor real */
        .custom-select-wrap input[type="hidden"] { display:none; }

        /* Botón que simula el select */
        .cs-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px 14px;
            background: #fff;
            border: 1.5px solid #a78bfa;
            border-radius: 10px;
            color: #9ca3af;        /* color placeholder */
            font-size: .9rem;
            cursor: pointer;
            user-select: none;
            transition: border-color .2s, box-shadow .2s;
            text-align: left;
        }
        .cs-trigger.has-value { color: #111827; }
        .cs-trigger:focus,
        .cs-trigger.open {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124,58,237,.12);
            outline: none;
        }
        .cs-trigger .cs-arrow {
            flex-shrink: 0;
            margin-left: 8px;
            transition: transform .2s;
            color: #6b7280;
            font-size: .75rem;
        }
        .cs-trigger.open .cs-arrow { transform: rotate(180deg); }

        /* Texto del trigger — ocupa todo el ancho disponible, NO se trunca */
        .cs-trigger .cs-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;   /* solo elipsis si el panel es MUY pequeño */
            min-width: 0;
        }

        /* Lista desplegable */
        .cs-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1.5px solid #c4b5fd;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(109,40,217,.15);
            z-index: 9999;
            max-height: 220px;
            overflow-y: auto;
        }
        .cs-dropdown.open { display: block; }

        .cs-option {
            padding: 10px 14px;
            font-size: .88rem;
            color: #111827;
            cursor: pointer;
            white-space: normal;      /* ← texto completo, puede hacer salto de línea */
            line-height: 1.4;
            transition: background .15s;
        }
        .cs-option:hover           { background: #f5f3ff; color: #5b21b6; }
        .cs-option.selected        { background: #ede9fe; color: #5b21b6; font-weight:600; }
        .cs-option[data-value=""]  { color: #9ca3af; font-style: italic; }

        /* Errores */
        .err-box { background:#fef2f2; border:1.5px solid #fca5a5; border-radius:10px; color:#b91c1c; padding:10px 14px; font-size:.82rem; margin-bottom:16px; }
        .err-box ul { margin:0; padding-left:16px; }

        /* Misc */
        .link-p { color:#7c3aed; text-decoration:none; font-size:.8rem; }
        .link-p:hover { color:#5b21b6; }
        .chk { color:#6b7280 !important; font-size:.82rem; font-weight:400 !important; }

        /* Responsive */
        @media (max-width:640px) {
            .side-panel { display:none; }
            .auth-card  { margin:0; border-radius:0; min-height:100vh; }
            #slideLogin    { padding:36px 22px; min-height:100vh; }
            #slideRegister { padding:24px 22px; }
        }
        @media (min-width:641px) and (max-width:860px) {
            .side-panel { flex:0 0 34%; }
            #slideLogin    { padding:36px 28px; }
            #slideRegister { padding:26px 28px; }
        }
    </style>
</head>

@php $carrerasLayout = \App\Models\Carrera::orderBy('nombre')->get(); @endphp

<body class="{{ request()->routeIs('register') ? 'show-register' : '' }}">

<div class="bg-circles">
    <span></span><span></span><span></span><span></span><span></span>
</div>

<div class="auth-card">

    {{-- Panel izquierdo --}}
    <div class="side-panel">
        <div class="logo-wrap">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
        </div>
        <div class="side-content" id="sideLogin"
             style="{{ request()->routeIs('register') ? 'display:none' : '' }}">
            <h2>¡Bienvenido!</h2>
            <p>Accede a tu cuenta para gestionar tus actividades complementarias.</p>
            <a href="#" class="btn-outline-white" id="btnShowReg">Crear cuenta</a>
        </div>
        <div class="side-content" id="sideReg"
             style="{{ request()->routeIs('register') ? '' : 'display:none' }}">
            <h2>¿Ya tienes cuenta?</h2>
            <p>Inicia sesión para continuar con tus actividades registradas.</p>
            <a href="#" class="btn-outline-white" id="btnShowLog">Iniciar sesión</a>
        </div>
    </div>

    {{-- Panel formularios --}}
    <div class="forms-panel">
        <div class="forms-track">

            {{-- ══ LOGIN ══ --}}
            <div class="form-slide" id="slideLogin">
                <h3>Iniciar Sesión</h3>
                <p class="sub">Accede con tu correo institucional</p>

                @if ($errors->any() && !request()->routeIs('register'))
                    <div class="err-box">
                        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Correo electrónico</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="tucorreo@ejemplo.com"
                               value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="form-group mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0">Contraseña</label>
                            <a href="{{ route('password.request') }}" class="link-p">¿Olvidaste tu contraseña?</a>
                        </div>
                        <input type="password" name="password" class="form-control"
                               placeholder="••••••••" required>
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

            {{-- ══ REGISTRO ══ --}}
            <div class="form-slide" id="slideRegister">
                <h3>Crear Cuenta</h3>
                <p class="sub">Regístrate como alumno</p>

                @if ($errors->any() && request()->routeIs('register'))
                    <div class="err-box">
                        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- SELECT PERSONALIZADO DE CARRERA --}}
                    <div class="carrera-box">
                        <label><i class="fas fa-graduation-cap mr-1"></i>Carrera <span style="color:#dc2626">*</span></label>

                        {{-- Input oculto que se envía con el form --}}
                        <input type="hidden" name="id_carrera" id="inputCarrera" value="{{ old('id_carrera', '') }}" required>

                        <div class="custom-select-wrap" id="csWrap">
                            {{-- Botón visible --}}
                            <div class="cs-trigger {{ old('id_carrera') ? 'has-value' : '' }}"
                                 id="csTrigger" tabindex="0" role="combobox" aria-expanded="false">
                                <span class="cs-text" id="csText">
                                    @php
                                        $oldCarrera = $carrerasLayout->firstWhere('id_carrera', old('id_carrera'));
                                    @endphp
                                    {{ $oldCarrera ? $oldCarrera->nombre : '— Selecciona tu carrera —' }}
                                </span>
                                <i class="fas fa-chevron-down cs-arrow"></i>
                            </div>

                            {{-- Dropdown --}}
                            <div class="cs-dropdown" id="csDropdown" role="listbox">
                                <div class="cs-option" data-value="" role="option">— Selecciona tu carrera —</div>
                                @foreach($carrerasLayout as $c)
                                    <div class="cs-option {{ old('id_carrera') == $c->id_carrera ? 'selected' : '' }}"
                                         data-value="{{ $c->id_carrera }}" role="option">
                                        {{ $c->nombre }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Nombre --}}
                    <div class="form-group mb-2">
                        <label>Nombre(s) <span style="color:#dc2626">*</span></label>
                        <input type="text" name="nombre" class="form-control"
                               placeholder="Ej. Juan Carlos" value="{{ old('nombre') }}" required>
                    </div>

                    {{-- Apellidos --}}
                    <div class="row">
                        <div class="col-6 pr-1">
                            <div class="form-group mb-2">
                                <label>Ap. Paterno <span style="color:#dc2626">*</span></label>
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

                    {{-- Correo + Núm. Control --}}
                    <div class="row">
                        <div class="col-7 pr-1">
                            <div class="form-group mb-2">
                                <label>Correo <span style="color:#dc2626">*</span></label>
                                <input type="email" name="email" class="form-control"
                                       placeholder="correo@ito.edu.mx" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="col-5 pl-1">
                            <div class="form-group mb-2">
                                <label>Núm. Control</label>
                                <input type="number" name="num_control" class="form-control"
                                       placeholder="20310001" value="{{ old('num_control') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Teléfono --}}
                    <div class="form-group mb-2">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               placeholder="9511234567" value="{{ old('telefono') }}">
                    </div>

                    {{-- Contraseñas --}}
                    <div class="row">
                        <div class="col-6 pr-1">
                            <div class="form-group mb-3">
                                <label>Contraseña <span style="color:#dc2626">*</span></label>
                                <input type="password" name="password" class="form-control"
                                       placeholder="Mín. 6 caracteres" required>
                            </div>
                        </div>
                        <div class="col-6 pl-1">
                            <div class="form-group mb-3">
                                <label>Confirmar <span style="color:#dc2626">*</span></label>
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
            </div>

        </div>{{-- /forms-track --}}
    </div>{{-- /forms-panel --}}

</div>{{-- /auth-card --}}

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script>
/* ── Cambio login / registro ──────────────────────────── */
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

/* ── Select personalizado de carrera ─────────────────── */
var trigger   = document.getElementById('csTrigger');
var dropdown  = document.getElementById('csDropdown');
var csText    = document.getElementById('csText');
var hiddenVal = document.getElementById('inputCarrera');

// Abrir / cerrar al hacer click en el trigger
trigger.addEventListener('click', function(e) {
    e.stopPropagation();
    var isOpen = dropdown.classList.contains('open');
    closeDropdown();
    if (!isOpen) openDropdown();
});

// Teclado: Enter / Espacio abren; Escape cierra
trigger.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); trigger.click(); }
    if (e.key === 'Escape') closeDropdown();
});

// Seleccionar una opción
dropdown.querySelectorAll('.cs-option').forEach(function(opt) {
    opt.addEventListener('click', function(e) {
        e.stopPropagation();
        var val   = this.dataset.value;
        var label = this.textContent.trim();

        // Actualizar hidden input
        hiddenVal.value = val;

        // Actualizar texto visible
        csText.textContent = label;
        trigger.classList.toggle('has-value', val !== '');

        // Marcar seleccionado
        dropdown.querySelectorAll('.cs-option').forEach(function(o){ o.classList.remove('selected'); });
        this.classList.add('selected');

        closeDropdown();
        trigger.focus();
    });
});

// Cerrar si se hace click fuera
document.addEventListener('click', function() { closeDropdown(); });

function openDropdown() {
    dropdown.classList.add('open');
    trigger.classList.add('open');
    trigger.setAttribute('aria-expanded', 'true');
}
function closeDropdown() {
    dropdown.classList.remove('open');
    trigger.classList.remove('open');
    trigger.setAttribute('aria-expanded', 'false');
}
</script>
</body>
</html>
