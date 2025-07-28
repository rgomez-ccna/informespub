<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\UsuarioController;

use App\Http\Controllers\PublicadorController;
use App\Http\Controllers\RegistroController;

use App\Http\Controllers\TableroController;
use App\Http\Controllers\LimpiezaController;
use App\Http\Controllers\LimpiezaMensualController;

// Ruta raíz
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $rol = Auth::user()->role;

    if (in_array($rol, ['usuario'])) {
        return redirect()->route('tablero.index');
    }

    // solo Para admin y superadmin // por ahora para el visita tambien
    return redirect()->route('pub.listado');
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
 
Route::get('/buscar-publicadores', [App\Http\Controllers\PublicadorController::class, 'buscar']);




// REGISTROS
Route::resource('reg', RegistroController::class)->except(['show']);
Route::get('/reg/create/{id}', [RegistroController::class, 'create'])->name('reg.create');
Route::post('/reg/create/{id}', [RegistroController::class, 'store'])->name('reg.store');
Route::get('/reg/s21/{id_publicador}', [RegistroController::class, 's21'])->name('reg.s21');
Route::get('reg/enviar-informes', [RegistroController::class, 'enviarInformes'])->name('reg.enviar-informes');


Route::get('/tablero', [TableroController::class, 'index'])->name('tablero.index');
// Rutas vacías de prueba (ajustar después con controladores reales)

Route::view('/tablero/anuncios', 'tablero.anuncios')->name('tablero.anuncios');


Route::view('/tablero/cuentas', 'tablero.cuentas')->name('tablero.cuentas');
Route::view('/tablero/territorio', 'tablero.territorio')->name('tablero.territorio');

// limpieza
Route::resource('tablero/limpieza', LimpiezaController::class)->names('limpieza');
Route::resource('limpieza-mensual', LimpiezaMensualController::class)->except(['index']); // porque se muestra en el index de limpieza
// acomodadores
Route::resource('tablero/acomodadores', App\Http\Controllers\AcomodadorController::class)->names('acomodadores');
// Salidas de ministerio
Route::resource('tablero/ministerio', App\Http\Controllers\SalidaMinisterioController::class)->names('ministerio');
// Reunion pública
Route::resource('tablero/publica', App\Http\Controllers\ReunionPublicaController::class)->names('publica');
// Discurso público VISITAS y SALIDAS
Route::resource('tablero/discursos', App\Http\Controllers\DiscursoPublicoController::class)->names('discursos');

Route::prefix('tablero/vida-ministerio')->name('vidaministerio.')->group(function () {
    Route::get('/', [App\Http\Controllers\ReunionVidaMinisterioController::class, 'index'])->name('index');
    Route::get('/crear', [App\Http\Controllers\ReunionVidaMinisterioController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ReunionVidaMinisterioController::class, 'store'])->name('store');
    Route::get('/{id}/editar', [App\Http\Controllers\ReunionVidaMinisterioController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\ReunionVidaMinisterioController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\ReunionVidaMinisterioController::class, 'destroy'])->name('destroy');
});


});


// Rutas de autenticación (solo login y logout, sin registro ni recuperación)
Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

//🔒 Y como seguridad extra (opcional, por si alguien prueba /register a mano):
Route::get('/register', function () {
    abort(403); // o redirect()->route('login')
});
