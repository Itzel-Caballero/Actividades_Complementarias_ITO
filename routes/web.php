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


Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación de Laravel (login, register, password reset, etc.)
Auth::routes();

Route::get('/debug-user', function () {
    $user = Auth::user();
    if (!$user) return 'No hay usuario autenticado';

    return [
        'id_usuario'  => $user->id,
        'nombre'      => $user->nombre,
        'roles'       => $user->getRoleNames(),
        'permisos'    => $user->getAllPermissions()->pluck('name'),
        'puede_ver'   => $user->can('ver-rol'),
    ];
})->middleware('auth');


// ─── Dashboards por rol (protegidos con auth) ─────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/alumno/dashboard', function () {
        return view('home');
    })->name('alumno.dashboard');

    Route::get('/instructor/dashboard', function () {
        return view('home');
    })->name('instructor.dashboard');

    Route::get('/admin/dashboard', function () {
        return view('home');
    })->name('admin.dashboard');

    // Rutas existentes del proyecto
    Route::resource('roles',    RolController::class);
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('blogs',    BlogController::class);

    Route::resource('actividades', ActividadComplementariaController::class);
    Route::resource('grupos', GrupoController::class);
    Route::patch('/grupos/{grupo}/asignar-instructor', [GrupoController::class, 'asignarInstructor'])->name('grupos.asignar-instructor');

    Route::post('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update');

    Route::post('/inscripciones', [InscripcionController::class, 'store'])->name('inscripciones.store');
    Route::get('/mis-inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');
    Route::post('/inscripciones/{inscripcion}/baja', [InscripcionController::class, 'darBaja'])->name('inscripciones.baja');

    // ─── Rutas del Coordinador ────────────────────────────────────────────
    Route::prefix('coordinador')->name('coordinador.')->group(function () {

        Route::get('/',  [CoordinadorController::class, 'index'])->name('index');

        // Grupos y horarios
        Route::get('/grupos',              [CoordinadorController::class, 'grupos'])->name('grupos');
        Route::get('/grupos/crear',        [CoordinadorController::class, 'createGrupo'])->name('grupos.create');
        Route::post('/grupos',             [CoordinadorController::class, 'storeGrupo'])->name('grupos.store');
        Route::get('/grupos/{id}/editar',  [CoordinadorController::class, 'editGrupo'])->name('grupos.edit');
        Route::put('/grupos/{id}',         [CoordinadorController::class, 'updateGrupo'])->name('grupos.update');
        Route::delete('/grupos/{id}',      [CoordinadorController::class, 'destroyGrupo'])->name('grupos.destroy');
        Route::post('/grupos/{id}/instructor', [CoordinadorController::class, 'asignarInstructor'])->name('grupos.asignar_instructor');

        // Actividades
        Route::get('/actividades', [CoordinadorController::class, 'actividades'])->name('actividades');

        // Docentes
        Route::get('/docentes', [CoordinadorController::class, 'docentes'])->name('docentes');

        // Alumnos
        Route::get('/alumnos',  [CoordinadorController::class, 'alumnos'])->name('alumnos');

        // AJAX: búsqueda de instructores
        Route::get('/api/instructores', [CoordinadorController::class, 'buscarInstructores'])->name('api.instructores');
    });

});
