<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\UsuarioController;

use App\Http\Controllers\PublicadorController;
use App\Http\Controllers\RegistroController;

use App\Http\Controllers\TableroController;
use App\Http\Controllers\LimpiezaController;
use App\Http\Controllers\LimpiezaMensualController;

use App\Http\Controllers\ProgramaCapturaController;
use Illuminate\Support\Facades\Artisan;

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

Route::prefix('tablero')->name('tablero.')->group(function () {
    Route::resource('programa-capturas', ProgramaCapturaController::class)
        ->only(['index','create','store','edit','update','destroy']);

    Route::delete('programa-capturas/{id}/imagen/{idx}', [ProgramaCapturaController::class,'destroyImagen'])
        ->name('programa-capturas.imagen.destroy');
});


Route::get('/fix-storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link creado OK';
});

// 📌 Ruta para limpiar la caché y redescubrir paquetes en Laravel (ÚSALA SOLO CUANDO SEA NECESARIO)
Route::get('/reparar-laravel', function () {
    // 🔄 Borra la caché de configuración para asegurarse de que Laravel lea correctamente los archivos .env y config/*.php
    Artisan::call('config:clear');
    // 🗑️ Limpia la caché general de Laravel (incluye sesiones, rutas, etc.)
    Artisan::call('cache:clear');
    // 🔄 Regenera la caché de configuración para optimizar el rendimiento
   // Artisan::call('config:cache');
    // 🔍 Redescubre y registra los paquetes instalados en Laravel (IMPORTANTE para Socialite y otros paquetes nuevos)
    Artisan::call('package:discover --ansi');
    return '✔ Laravel ha limpiado la caché y detectado paquetes nuevamente.';


}); //Fin del grupo de rutas protegidas con autenticación y tenant

Route::get('/clear-config', function () {
        Artisan::call('config:clear');
        return 'Cache de configuración limpiada!';
    });
   // Utilidades
        Route::get('/reparar-cache', function () {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');
            Artisan::call('config:cache');
            Artisan::call('event:clear');
            Artisan::call('event:cache');
            Artisan::call('clear-compiled');
            return '✔️ Todo limpio y optimizado.';
        });

Route::get('/fix-cache', function () {
    Artisan::call('optimize:clear');   // incluye config/route/view/cache
    Artisan::call('storage:link');     // reintenta link
    return 'OK';
});
use Illuminate\Support\Facades\Storage;

Route::get('/test-img', function () {
    $path = 'NxRn6P7mGU2EUQSoqAtSRrEtJIufvlq7UmadMaw8.png'; // poné un archivo real dentro de storage/app/public
    return [
        'exists' => Storage::disk('public')->exists($path),
        'url'    => Storage::url($path),
        'direct' => asset('storage/'.$path),
    ];
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
