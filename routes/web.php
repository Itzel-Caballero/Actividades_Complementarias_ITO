<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ActividadComplementariaController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\CoordinadorController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\Admin\CarreraController;
use App\Http\Controllers\Admin\SemestreController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\UbicacionController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\PanelCoordinadoresController;
use App\Http\Controllers\AlumnoController;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('alumnos/crear', [AlumnoController::class, 'create'])->name('alumnos.create');
    Route::post('alumnos/guardar', [AlumnoController::class, 'store'])->name('alumnos.store');
    Route::delete('alumnos/{id}/baja', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/debug-user', function () {
    $user = Auth::user();
    if (!$user) return 'No hay usuario autenticado';
    return [
        'id_usuario' => $user->id,
        'nombre'     => $user->nombre,
        'roles'      => $user->getRoleNames(),
        'permisos'   => $user->getAllPermissions()->pluck('name'),
        'puede_ver'  => $user->can('ver-rol'),
    ];
})->middleware('auth');


Route::middleware(['auth', 'usuario.activo'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/alumno/dashboard',     fn() => view('home'))->name('alumno.dashboard');
    Route::get('/admin/dashboard',      fn() => view('home'))->name('admin.dashboard');

    // ─── Instructor ───────────────────────────────────────────────────────
    Route::prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/mis-grupos',   [InstructorController::class, 'misGrupos'])->name('mis-grupos');
        Route::get('/perfil',       [InstructorController::class, 'editarPerfil'])->name('perfil');
        Route::put('/perfil',       [InstructorController::class, 'actualizarPerfil'])->name('perfil.update');
        Route::get('/calificar/{id_inscripcion}',  [InstructorController::class, 'calificar'])->name('calificar');
        Route::put('/calificar/{id_inscripcion}',  [InstructorController::class, 'guardarCalificacion'])->name('guardarCalificacion');
    });

    // ─── Recursos generales ───────────────────────────────────────────────
    Route::resource('roles',       RolController::class);
    Route::resource('usuarios',    UsuarioController::class);
    Route::patch('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');
    Route::resource('blogs',       BlogController::class);
    Route::resource('actividades', ActividadComplementariaController::class);
    Route::resource('grupos',      GrupoController::class);
    Route::patch('/grupos/{grupo}/asignar-instructor', [GrupoController::class, 'asignarInstructor'])->name('grupos.asignar-instructor');

    Route::post('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update');

    // ─── Perfil completo del alumno ───────────────────────────────────────
    Route::get('/alumno/perfil',    [PerfilController::class, 'show'])->name('alumno.perfil');
    Route::put('/alumno/perfil',    [PerfilController::class, 'updateCompleto'])->name('alumno.perfil.update');

    Route::post('/inscripciones', [InscripcionController::class, 'store'])->name('inscripciones.store');
    Route::get('/mis-inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');
    Route::post('/inscripciones/{inscripcion}/baja', [InscripcionController::class, 'darBaja'])->name('inscripciones.baja');

    // ─── Coordinador ──────────────────────────────────────────────────────
    Route::prefix('coordinador')->name('coordinador.')->group(function () {
        Route::get('/',  [CoordinadorController::class, 'index'])->name('index');
        Route::get('/grupos',             [CoordinadorController::class, 'grupos'])->name('grupos');
        Route::get('/grupos/crear',       [CoordinadorController::class, 'createGrupo'])->name('grupos.create');
        Route::post('/grupos',            [CoordinadorController::class, 'storeGrupo'])->name('grupos.store');
        Route::get('/grupos/{id}/editar', [CoordinadorController::class, 'editGrupo'])->name('grupos.edit');
        Route::put('/grupos/{id}',        [CoordinadorController::class, 'updateGrupo'])->name('grupos.update');
        Route::delete('/grupos/{id}',     [CoordinadorController::class, 'destroyGrupo'])->name('grupos.destroy');
        Route::post('/grupos/{id}/instructor', [CoordinadorController::class, 'asignarInstructor'])->name('grupos.asignar_instructor');
        Route::get('/actividades', [CoordinadorController::class, 'actividades'])->name('actividades');
        Route::get('/docentes',    [CoordinadorController::class, 'docentes'])->name('docentes');
        Route::get('/alumnos',     [CoordinadorController::class, 'alumnos'])->name('alumnos');
        Route::post('/alumnos/{inscripcion}/baja', [CoordinadorController::class, 'darBajaAlumno'])->name('alumnos.baja');
        Route::get('/api/instructores',           [CoordinadorController::class, 'buscarInstructores'])->name('api.instructores');
        Route::get('/api/instructores-actividad', [CoordinadorController::class, 'instructoresPorActividad'])->name('api.instructores_actividad');
    });

    // ─── Admin ────────────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('carreras',      CarreraController::class);
        Route::resource('semestres',     SemestreController::class);
        Route::resource('departamentos', DepartamentoController::class);
        Route::resource('ubicaciones',   UbicacionController::class);
        Route::get('/reportes/alumnos',       [ReporteController::class, 'alumnos'])->name('reportes.alumnos');
        Route::get('/reportes/inscripciones', [ReporteController::class, 'inscripciones'])->name('reportes.inscripciones');
        Route::get('/reportes/accesos',       [ReporteController::class, 'accesos'])->name('reportes.accesos');
        Route::post('/admin/semestres', [SemestreController::class, 'store'])->name('admin.semestres.store');
        Route::get('/coordinadores',              [PanelCoordinadoresController::class, 'index'])->name('coordinadores.index');
        Route::post('/coordinadores/asignar',     [PanelCoordinadoresController::class, 'asignar'])->name('coordinadores.asignar');
        Route::delete('/coordinadores/{id}/quitar', [PanelCoordinadoresController::class, 'quitar'])->name('coordinadores.quitar');
    });

});
