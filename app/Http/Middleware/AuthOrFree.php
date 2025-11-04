<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthOrFree
{
    public function handle($request, Closure $next)
    {
        // si está logueado -> ok
        if (Auth::check()) return $next($request);

        // si viene con free_access en session -> ok
        if (session('free_access') === true) return $next($request);

        return redirect()->route('login');
    }
}
