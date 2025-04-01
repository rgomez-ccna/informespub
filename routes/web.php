<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuarioController;


// Ruta pública para la página de bienvenida
Route::get('/', function () {
    // Redirige a 'home' si el usuario está autenticado
    if (Auth::check()) {
        return redirect()->route('home');
    }
    // Si no está autenticado, muestra la vista de bienvenida
    return view('welcome');
})->name('welcome');



// Rutas protegidas con autenticación
Route::middleware(['auth'])->group(function () {
    // RUTA HOME
    Route::get('/home', [HomeController::class, 'index'])->name('home'); // Esta ruta será la que use el usuario autenticado.

    // USUARIOS
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');

    // Ruta para comprobar sesión
    Route::get('/check-session', function () {
        if (auth()->check()) {
            return response()->json(['session' => true], 200); // Sesión activa
        } else {
            return response()->json(['session' => false], 401); // Sesión expirada
        }
    })->name('check.session');
});

// Rutas de autenticación (login, registro, etc.)
Auth::routes();
