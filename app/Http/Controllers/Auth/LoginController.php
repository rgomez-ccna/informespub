<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // Redirección por defecto (solo si no se define manualmente)
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Redirección dinámica según el rol
    protected function authenticated(Request $request, $user)
    {
        // 1. Bloquear cuentas deshabilitadas
        if ($user->role === 'disabled') {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta ha sido desactivada. Contactá al administrador.'
            ]);
        }

        // 2. Redirigir usuario o visita directo al tablero
        if (in_array($user->role, ['usuario', 'visita'])) {
            return redirect()->route('tablero.index');
        }

        // 3. Admin y superadmin al home normal
        return redirect()->intended($this->redirectTo);
    }
}
