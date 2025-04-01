<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuarioController;

use App\Http\Controllers\PublicadorController;
use App\Http\Controllers\RegistroController;

// Ruta raíz
Route::get('/', function () {
    return Auth::check() ? redirect()->route('pub.listado') : redirect()->route('login');
});


// Rutas protegidas con autenticación
Route::middleware(['auth'])->group(function () {
    // RUTA HOME
    Route::get('/pub', [PublicadorController::class, 'index'])->name('pub.listado'); // Esta ruta será la que use el usuario autenticado.

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



    
// Publicadores agrupados por grupo (como lista tipo reporte)
Route::get('/pub/listado', [PublicadorController::class, 'listado'])->name('pub.listado');

// Tarjeta S-21 (detalle de registros por publicador)
Route::get('/pub/s21/{id}', [PublicadorController::class, 's21'])->name('pub.s21');


 // PUBLICADORES
 Route::resource('pub', PublicadorController::class);




// REGISTROS
Route::resource('reg', RegistroController::class)->except(['show']);
Route::get('/reg/create/{id}', [RegistroController::class, 'create'])->name('reg.create');
Route::post('/reg/create/{id}', [RegistroController::class, 'store'])->name('reg.store');
Route::get('/reg/s21/{id_publicador}', [RegistroController::class, 's21'])->name('reg.s21');
Route::get('reg/enviar-informes', [RegistroController::class, 'enviarInformes'])->name('reg.enviar-informes');


});

// Rutas de autenticación (login, registro, etc.)
Auth::routes();
