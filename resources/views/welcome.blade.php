<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades Complementarias | ITOaxaca</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Lato', sans-serif; }

        /* Navbar */
        .navbar-top {
            position: fixed; top: 0; width: 100%; z-index: 100;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(8px);
            padding: 12px 0;
        }
        .navbar-top .logo { height: 48px; }
        .navbar-top .nombre-inst {
            color: #fff; font-weight: 700; font-size: 1.1rem;
            letter-spacing: 1px;
        }

        /* Hero */
        .hero {
            position: relative;
            height: 100vh;
            display: flex; align-items: center;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0;
            background: url('{{ asset("img/banda_guerra.jpg") }}') center/cover no-repeat;
            filter: brightness(0.45);
        }
        .hero-content {
            position: relative; z-index: 2;
            color: #fff;
        }
        .hero-content h1 {
            font-size: 3rem; font-weight: 900;
            line-height: 1.2;
            text-shadow: 0 2px 12px rgba(0,0,0,0.5);
        }
        .hero-content p {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.88);
            max-width: 540px;
        }
        .btn-hero-primary {
            background: #cd67ef; color: #fff;
            border: none; border-radius: 30px;
            padding: 13px 36px; font-size: 1rem; font-weight: 700;
            transition: background 0.2s;
        }
        .btn-hero-primary:hover { background: #9d4ac7; color: #fff; }
        .btn-hero-outline {
            background: transparent; color: #fff;
            border: 2px solid #fff; border-radius: 30px;
            padding: 11px 34px; font-size: 1rem; font-weight: 700;
            transition: all 0.2s;
        }
        .btn-hero-outline:hover { background: #fff; color: #333; }

        /* Actividades section */
        .section-actividades { padding: 80px 0; background: #f8f9fa; }
        .section-actividades h2 {
            font-size: 2rem; font-weight: 900;
            color: #2d3436; margin-bottom: 12px;
        }
        .section-actividades .subtitle {
            color: #636e72; font-size: 1.05rem; max-width: 600px; margin: 0 auto 48px;
        }

        /* Cards de actividades */
        .act-card {
            border: none; border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(0,0,0,0.1);
            transition: transform 0.25s, box-shadow 0.25s;
            height: 100%;
        }
        .act-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.15);
        }
        .act-card img {
            width: 100%; height: 220px; object-fit: cover;
        }
        .act-card .card-body { padding: 20px; }
        .act-card h5 { font-weight: 700; color: #2d3436; }
        .act-card p { color: #636e72; font-size: 0.95rem; }
        .badge-credito {
            background: #bf67ef; color: #fff;
            border-radius: 20px; padding: 4px 14px;
            font-size: 0.8rem; font-weight: 700;
        }

        /* CTA section */
        .section-cta {
            padding: 80px 0;
            background: linear-gradient(135deg, #ad67ef 0%, #9f4ac7 100%);
            color: #fff; text-align: center;
        }
        .section-cta h2 { font-size: 2.2rem; font-weight: 900; }
        .section-cta p { font-size: 1.1rem; color: rgba(255,255,255,0.85); }

        /* Steps */
        .step-icon {
            width: 64px; height: 64px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; margin: 0 auto 16px;
        }

        /* Footer */
        footer {
            background: #2d3436; color: rgba(255,255,255,0.6);
            padding: 24px 0; text-align: center; font-size: 0.9rem;
        }
        footer img { height: 36px; margin-bottom: 8px; }
    </style>
</head>
<body>

{{-- Navbar fija --}}
<nav class="navbar-top">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/LOGOITO.png') }}" class="logo mr-3" alt="ITO">
            <span class="nombre-inst">Instituto Tecnológico de Oaxaca</span>
        </div>
        <div>
            @auth
                <a href="/home" class="btn btn-hero-primary btn-sm">
                    <i class="fas fa-tachometer-alt mr-1"></i> Mi Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-hero-outline btn-sm mr-2">
                    Iniciar sesión
                </a>
                <a href="{{ route('register') }}" class="btn btn-hero-primary btn-sm">
                    Registrarse
                </a>
            @endauth
        </div>
    </div>
</nav>

{{-- Hero con imagen de banda de guerra --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content">
        <h1>Descubre las<br>Actividades Complementarias<br>del ITO</h1>
        <p class="mt-3 mb-4">
            Desarrolla nuevas habilidades, vive experiencias únicas y acumula los
            créditos que necesitas para graduarte. ¡Tu crecimiento integral comienza aquí!
        </p>
        @auth
            <a href="/actividades" class="btn btn-hero-primary mr-3">
                <i class="fas fa-th-list mr-2"></i> Ver Actividades
            </a>
            <a href="/mis-inscripciones" class="btn btn-hero-outline">
                <i class="fas fa-clipboard-list mr-2"></i> Mis Inscripciones
            </a>
        @else
            <a href="{{ route('register') }}" class="btn btn-hero-primary mr-3">
                <i class="fas fa-user-plus mr-2"></i> Únete ahora
            </a>
            <a href="{{ route('login') }}" class="btn btn-hero-outline">
                <i class="fas fa-sign-in-alt mr-2"></i> Iniciar sesión
            </a>
        @endauth
    </div>
</section>

{{-- Actividades destacadas --}}
<section class="section-actividades">
    <div class="container">
        <h2 class="text-center">Actividades que transforman</h2>
        <p class="subtitle text-center">
            Desde artes y deportes hasta tecnología y emprendimiento —
            hay una actividad perfecta para ti.
        </p>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="act-card card">
                    <img src="{{ asset('img/flor_baile.jpg') }}" alt="Baile">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5>Arte y Cultura</h5>
                            <span class="badge-credito">1-2 créditos</span>
                        </div>
                        <p>Expresa tu creatividad a través del baile folklórico, música y artes escénicas. Representa al ITO en eventos regionales.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="act-card card">
                    <img src="{{ asset('img/karate.png') }}" alt="Karate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5>Deportes y Salud</h5>
                            <span class="badge-credito">1-2 créditos</span>
                        </div>
                        <p>Fortalece tu disciplina y condición física con actividades deportivas como karate, fútbol, voleibol y más.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="act-card card">
                    <img src="{{ asset('img/banda_guerra.jpg') }}" alt="Banda de Guerra">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5>Banda de Guerra</h5>
                            <span class="badge-credito">2 créditos</span>
                        </div>
                        <p>Forma parte de la tradición del ITO. Desarrolla disciplina, trabajo en equipo y representación institucional.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Cómo funciona --}}
<section class="py-5" style="background: #fff;">
    <div class="container">
        <h2 class="text-center font-weight-bold mb-5" style="font-size:2rem;">¿Cómo funciona?</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="step-icon bg-primary text-white">
                    <i class="fas fa-search"></i>
                </div>
                <h5 class="font-weight-bold">1. Explora</h5>
                <p class="text-muted">Busca actividades disponibles para tu carrera y elige la que más te interese.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="step-icon bg-success text-white">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h5 class="font-weight-bold">2. Inscríbete</h5>
                <p class="text-muted">Elige el grupo con el horario que mejor se adapte a ti y regístrate.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="step-icon bg-warning text-white">
                    <i class="fas fa-trophy"></i>
                </div>
                <h5 class="font-weight-bold">3. Gradúate</h5>
                <p class="text-muted">Completa las actividades, acumula tus créditos y cumple el requisito de graduación.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="section-cta">
    <div class="container">
        <h2 class="mb-3">¡Empieza hoy mismo!</h2>
        <p class="mb-4">
            Regístrate y explora todas las actividades complementarias disponibles.<br>
            Tu desarrollo integral es parte de tu formación como ingeniero.
        </p>
        @auth
            <a href="/actividades" class="btn btn-light btn-lg rounded-pill px-5 font-weight-bold">
                <i class="fas fa-th-list mr-2"></i> Ver catálogo
            </a>
        @else
            <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5 font-weight-bold mr-3">
                <i class="fas fa-user-plus mr-2"></i> Crear cuenta
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg rounded-pill px-5 font-weight-bold">
                Iniciar sesión
            </a>
        @endauth
    </div>
</section>

{{-- Footer --}}
<footer>
    <div class="container">
        <img src="{{ asset('img/LOGOITO.png') }}" alt="ITO"><br>
        <span>© {{ date('Y') }} Instituto Tecnológico de Oaxaca — Plataforma de Actividades Complementarias</span>
    </div>
</footer>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>
</html>