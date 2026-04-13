@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Dashboard</h3>
        </div>
        <div class="section-body">

            {{-- ══════════════════════════════════════════════════════ --}}
            {{-- DASHBOARD DEL ADMINISTRADOR                           --}}
            {{-- ══════════════════════════════════════════════════════ --}}
            @role('admin')
            @php
                $cant_usuarios      = \App\Models\User::count();
                $cant_roles         = \Spatie\Permission\Models\Role::count();
                $cant_carreras      = \App\Models\Carrera::count();
                $cant_semestres     = \App\Models\Semestre::count();
                $cant_departamentos = \App\Models\Departamento::count();
                $cant_ubicaciones   = \App\Models\Ubicacion::count();
                $cant_alumnos       = \App\Models\Alumno::count();
                $cant_inscritos     = \App\Models\Inscripcion::whereIn('estatus', ['inscrito', 'cursando'])->count();
            @endphp

            {{-- Fila 1: Identidades y Acceso --}}
            <p class="text-muted mb-1">
                <small><i class="fas fa-user-shield mr-1"></i> Identidades y Acceso</small>
            </p>
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-c-blue order-card">
                        <div class="card-block">
                            <h5>Usuarios</h5>
                            <h2 class="text-right">
                                <i class="fa fa-users f-left"></i>
                                <span>{{ $cant_usuarios }}</span>
                            </h2>
                            <p class="m-b-0 text-right">
                                <a href="{{ route('usuarios.index') }}" class="text-white">Ver más</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-c-green order-card">
                        <div class="card-block">
                            <h5>Roles</h5>
                            <h2 class="text-right">
                                <i class="fa fa-user-lock f-left"></i>
                                <span>{{ $cant_roles }}</span>
                            </h2>
                            <p class="m-b-0 text-right">
                                <a href="{{ route('roles.index') }}" class="text-white">Ver más</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-c-pink order-card">
                        <div class="card-block">
                            <h5>Alumnos Registrados</h5>
                            <h2 class="text-right">
                                <i class="fa fa-user-graduate f-left"></i>
                                <span>{{ $cant_alumnos }}</span>
                            </h2>
                            <p class="m-b-0 text-right">
                                <a href="{{ route('admin.reportes.alumnos') }}" class="text-white">Ver padrón</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-c-pink order-card">
                        <div class="card-block">
                            <h5>Inscritos Activos</h5>
                            <h2 class="text-right">
                                <i class="fa fa-list-alt f-left"></i>
                                <span>{{ $cant_inscritos }}</span>
                            </h2>
                            <p class="m-b-0 text-right">
                                <a href="{{ route('admin.reportes.inscripciones') }}" class="text-white">Ver monitor</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fila 2: Estructura Académica e Infraestructura --}}
            <p class="text-muted mb-1 mt-2">
                <small><i class="fas fa-university mr-1"></i> Estructura Académica e Infraestructura</small>
            </p>
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-c-blue order-card">
                        <div class="card-block">
                            <h5>Carreras</h5>
                            <h2 class="text-right">
                                <i class="fa fa-graduation-cap f-left"></i>
                                <span>{{ $cant_carreras }}</span>
                            </h2>
                            <p class="m-b-0 text-right">
                                <a href="{{ route('admin.carreras.index') }}" class="text-white">Gestionar</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-c-green order-card">
                        <div class="card-block">
                            <h5>Semestres</h5>
                            <h2 class="text-right">
                                <i class="fa fa-calendar-alt f-left"></i>
                                <span>{{ $cant_semestres }}</span>
                            </h2>
                            <p class="m-b-0 text-right">
                                <a href="{{ route('admin.semestres.index') }}" class="text-white">Gestionar</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-c-pink order-card">
                        <div class="card-block">
                            <h5>Departamentos</h5>
                            <h2 class="text-right">
                                <i class="fa fa-building f-left"></i>
                                <span>{{ $cant_departamentos }}</span>
                            </h2>
                            <p class="m-b-0 text-right">
                                <a href="{{ route('admin.departamentos.index') }}" class="text-white">Gestionar</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-c-pink order-card">
                        <div class="card-block">
                            <h5>Ubicaciones</h5>
                            <h2 class="text-right">
                                <i class="fa fa-map-marker-alt f-left"></i>
                                <span>{{ $cant_ubicaciones }}</span>
                            </h2>
                            <p class="m-b-0 text-right">
                                <a href="{{ route('admin.ubicaciones.index') }}" class="text-white">Gestionar</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endrole

            {{-- ══════════════════════════════════════════════════════ --}}
            {{-- DASHBOARD PARA OTROS ROLES (vista original)           --}}
            {{-- ══════════════════════════════════════════════════════ --}}
            @unlessrole('admin')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 col-xl-4">
                                    <div class="card bg-c-blue order-card">
                                        <div class="card-block">
                                            <h5>Usuarios</h5>
                                            @php $cant_u = \App\Models\User::count(); @endphp
                                            <h2 class="text-right">
                                                <i class="fa fa-users f-left"></i>
                                                <span>{{ $cant_u }}</span>
                                            </h2>
                                            <p class="m-b-0 text-right">
                                                <a href="/usuarios" class="text-white">Ver más</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xl-4">
                                    <div class="card bg-c-green order-card">
                                        <div class="card-block">
                                            <h5>Roles</h5>
                                            @php $cant_r = \Spatie\Permission\Models\Role::count(); @endphp
                                            <h2 class="text-right">
                                                <i class="fa fa-user-lock f-left"></i>
                                                <span>{{ $cant_r }}</span>
                                            </h2>
                                            <p class="m-b-0 text-right">
                                                <a href="/roles" class="text-white">Ver más</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xl-4">
                                    <div class="card bg-c-pink order-card">
                                        <div class="card-block">
                                            <h5>Blogs</h5>
                                            @php $cant_b = \App\Models\Blog::count(); @endphp
                                            <h2 class="text-right">
                                                <i class="fa fa-blog f-left"></i>
                                                <span>{{ $cant_b }}</span>
                                            </h2>
                                            <p class="m-b-0 text-right">
                                                <a href="/blogs" class="text-white">Ver más</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endunlessrole

        </div>
    </section>
@endsection
