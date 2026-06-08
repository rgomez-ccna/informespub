<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'disabled') {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta ha sido desactivada. Contactá al administrador.'
            ]);
        }

        if ($user->role === 'superadmin') {
            return redirect()->route('congregaciones.index');
        }

        if ($user->role === 'tablero') {
            return redirect()->route('tablero.index');
        }

        if (in_array($user->role, ['secretario', 'colaborador'])) {
            return redirect()->route('pub.listado');
        }

        return redirect()->route('tablero.index');
    }
}