<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ActividadComplementariaController;
use App\Http\Controllers\InscripcionController;


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
    Route::post('/inscripciones', [InscripcionController::class, 'store'])->name('inscripciones.store');

    Route::post('/inscripciones', [InscripcionController::class, 'store'])->name('inscripciones.store');
    Route::get('/mis-inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');

    });
