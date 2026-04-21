{{-- Dashboard --}}
<li class="{{ Request::is('home') ? 'active' : '' }}">
    <a class="nav-link" href="/home">
        <i class="fas fa-home"></i><span>Dashboard</span>
    </a>
</li>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- Menú para ADMIN                                                --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@role('admin')

<li class="{{ Request::is('usuarios*') || Request::is('roles*') || Request::is('admin/reportes/accesos*') || Request::is('admin/coordinadores*') ? 'active' : '' }}">
    <a class="nav-link has-dropdown" href="#">
        <i class="fas fa-user-shield"></i><span>Identidades y Acceso</span>
    </a>
    <ul class="dropdown-menu">
        <li class="{{ Request::is('usuarios*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('usuarios.index') }}">
                <i class="fas fa-users"></i><span>Panel de Usuarios</span>
            </a>
        </li>
        <li class="{{ Request::is('roles*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('roles.index') }}">
                <i class="fas fa-user-lock"></i><span>Roles y Permisos</span>
            </a>
        </li>
        <li class="{{ Request::is('admin/coordinadores*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.coordinadores.index') }}">
                <i class="fas fa-user-tie"></i><span>Panel de Coordinadores</span>
            </a>
        </li>
        <li class="{{ Request::is('admin/reportes/accesos*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.reportes.accesos') }}">
                <i class="fas fa-history"></i><span>Log de Accesos</span>
            </a>
        </li>
    </ul>
</li>

<li class="{{ Request::is('admin/carreras*') || Request::is('admin/semestres*') || Request::is('admin/departamentos*') ? 'active' : '' }}">
    <a class="nav-link has-dropdown" href="#">
        <i class="fas fa-university"></i><span>Estructura Académica</span>
    </a>
    <ul class="dropdown-menu">
        <li class="{{ Request::is('admin/carreras*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.carreras.index') }}">
                <i class="fas fa-graduation-cap"></i><span>Carreras</span>
            </a>
        </li>
        <li class="{{ Request::is('admin/semestres*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.semestres.index') }}">
                <i class="fas fa-calendar-alt"></i><span>Semestres</span>
            </a>
        </li>
        <li class="{{ Request::is('admin/departamentos*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.departamentos.index') }}">
                <i class="fas fa-building"></i><span>Departamentos</span>
            </a>
        </li>
    </ul>
</li>

<li class="{{ Request::is('admin/ubicaciones*') ? 'active' : '' }}">
    <a class="nav-link has-dropdown" href="#">
        <i class="fas fa-map-marked-alt"></i><span>Infraestructura</span>
    </a>
    <ul class="dropdown-menu">
        <li class="{{ Request::is('admin/ubicaciones*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.ubicaciones.index') }}">
                <i class="fas fa-map-marker-alt"></i><span>Ubicaciones</span>
            </a>
        </li>
    </ul>
</li>

<li class="{{ Request::is('admin/reportes*') ? 'active' : '' }}">
    <a class="nav-link has-dropdown" href="#">
        <i class="fas fa-chart-bar"></i><span>Reportes Globales</span>
    </a>
    <ul class="dropdown-menu">
        <li class="{{ Request::is('admin/reportes/alumnos*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.reportes.alumnos') }}">
                <i class="fas fa-user-graduate"></i><span>Padrón de Alumnos</span>
            </a>
        </li>
        <li class="{{ Request::is('admin/reportes/inscripciones*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.reportes.inscripciones') }}">
                <i class="fas fa-list-alt"></i><span>Monitor de Inscripciones</span>
            </a>
        </li>
    </ul>
</li>

@endrole

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- Menú para ALUMNO                                               --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@role('alumno')
<li class="{{ Request::is('actividades*') ? 'active' : '' }}">
    <a class="nav-link" href="/actividades">
        <i class="fas fa-th-list"></i><span>Catálogo de Actividades</span>
    </a>
</li>
<li class="{{ Request::is('mis-inscripciones*') ? 'active' : '' }}">
    <a class="nav-link" href="/mis-inscripciones">
        <i class="fas fa-clipboard-list"></i><span>Mis Inscripciones</span>
    </a>
</li>
@endrole

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- Menú para INSTRUCTOR                                           --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@role('instructor')
<li class="{{ Request::is('instructor/mis-grupos*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('instructor.mis-grupos') }}">
        <i class="fas fa-chalkboard-teacher"></i><span>Mis Grupos</span>
    </a>
</li>
@endrole

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- Menú para COORDINADOR                                          --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@role('coordinador')
<li class="{{ Request::is('coordinador') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.index') }}">
        <i class="fas fa-tachometer-alt"></i><span>Información</span>
    </a>
</li>
<li class="{{ Request::is('coordinador/grupos*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.grupos') }}">
        <i class="fas fa-layer-group"></i><span>Grupos y Horarios</span>
    </a>
</li>
<li class="{{ Request::is('coordinador/docentes*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.docentes') }}">
        <i class="fas fa-chalkboard-teacher"></i><span>Docentes</span>
    </a>
</li>
<li class="{{ Request::is('coordinador/alumnos*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.alumnos') }}">
        <i class="fas fa-user-graduate"></i><span>Alumnos</span>
    </a>
</li>
<li class="{{ Request::is('coordinador/actividades*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.actividades') }}">
        <i class="fas fa-list-alt"></i><span>Actividades</span>
    </a>
</li>
@endrole
