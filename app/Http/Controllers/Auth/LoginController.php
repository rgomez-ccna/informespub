<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'disabled') {
            auth()->logout(); // Cierra la sesión del usuario "disabled".
            return redirect()->route('login')->with('message', 'Tu cuenta ha sido deshabilitada. Contacta al administrador para obtener ayuda.');
        }
    
        // Establecer el ID del usuario en la sesión después de que haya iniciado sesión
        session(['vendedor_id' => $user->id]);
    
        // Continúa con la lógica predeterminada o redirige según lo necesites
        return redirect()->intended($this->redirectTo);
    }
}
