{{-- Dashboard --}}
<li class="side-menus {{ Request::is('home') ? 'active' : '' }}">
    <a class="nav-link" href="/home">
        <i class="fas fa-home"></i><span>Dashboard</span>
    </a>
</li>

{{-- Menú para ADMIN --}}
@role('admin')
<li class="side-menus {{ Request::is('usuarios*') ? 'active' : '' }}">
    <a class="nav-link" href="/usuarios">
        <i class="fas fa-users"></i><span>Usuarios</span>
    </a>
</li>
<li class="side-menus {{ Request::is('roles*') ? 'active' : '' }}">
    <a class="nav-link" href="/roles">
        <i class="fas fa-user-lock"></i><span>Roles</span>
    </a>
</li>
<li class="side-menus {{ Request::is('blogs*') ? 'active' : '' }}">
    <a class="nav-link" href="/blogs">
        <i class="fas fa-blog"></i><span>Blogs</span>
    </a>
</li>
@endrole

{{-- Menú para ALUMNO --}}
@role('alumno')
<li class="side-menus {{ Request::is('actividades*') ? 'active' : '' }}">
    <a class="nav-link" href="/actividades">
        <i class="fas fa-th-list"></i><span>Catálogo de Actividades</span>
    </a>
</li>
<li class="side-menus {{ Request::is('mis-inscripciones*') ? 'active' : '' }}">
    <a class="nav-link" href="/mis-inscripciones">
        <i class="fas fa-clipboard-list"></i><span>Mis Inscripciones</span>
    </a>
</li>
@endrole

{{-- Menú para INSTRUCTOR --}}
@role('instructor')
<li class="side-menus {{ Request::is('actividades*') ? 'active' : '' }}">
    <a class="nav-link" href="/actividades">
        <i class="fas fa-chalkboard-teacher"></i><span>Actividades</span>
    </a>
</li>
@endrole

{{-- Menú para COORDINADOR --}}
@role('coordinador')
<li class="side-menus {{ Request::is('coordinador') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.index') }}">
        <i class="fas fa-tachometer-alt"></i><span>Información</span>
    </a>
</li>
<li class="side-menus {{ Request::is('coordinador/grupos*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.grupos') }}">
        <i class="fas fa-layer-group"></i><span>Grupos y Horarios</span>
    </a>
</li>
<li class="side-menus {{ Request::is('coordinador/docentes*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.docentes') }}">
        <i class="fas fa-chalkboard-teacher"></i><span>Docentes</span>
    </a>
</li>
<li class="side-menus {{ Request::is('coordinador/alumnos*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.alumnos') }}">
        <i class="fas fa-user-graduate"></i><span>Alumnos</span>
    </a>
</li>
<li class="side-menus {{ Request::is('coordinador/actividades*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('coordinador.actividades') }}">
        <i class="fas fa-list-alt"></i><span>Actividades</span>
    </a>
</li>
@endrole
