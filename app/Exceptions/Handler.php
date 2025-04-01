<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $exception, $request) {
            if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
                // Aquí puedes redirigir a la ruta que prefieras, por ejemplo a la página de inicio de sesión
                // o simplemente a la raíz del sitio. También puedes añadir un mensaje de sesión si lo deseas.
                return redirect('/login')->with('message', 'Tu sesión ha expirado, por favor intenta de nuevo.');
            }
        });
    }
}
