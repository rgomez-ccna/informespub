<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinkAcceso;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LinkAccesoController extends Controller
{
    // POST: crea link público para la congregación del usuario logueado
    public function store(Request $request)
    {
        abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador']), 403);

        $request->validate([
            'dias' => 'nullable|integer|min:1|max:60',
            'password' => 'nullable|string|max:100',
        ]);

        $token = Str::random(64);
        $hash = $request->filled('password') ? Hash::make($request->password) : null;

        $link = LinkAcceso::create([
            'congregacion_id' => auth()->user()->congregacion_id,
            'token' => $token,
            'expires_at' => Carbon::now()->addDays($request->input('dias', 7)),
            'password_hash' => $hash,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', url('/acceso/' . $link->token));
    }

    public function enter($token)
    {
        $link = LinkAcceso::with('congregacion')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$link) {
            abort(403);
        }

        if ($link->password_hash) {
            return view('access.password', compact('token'));
        }

        session([
            'free_access' => true,
            'free_token' => $token,
            'free_congregacion_id' => $link->congregacion_id,
            'free_congregacion_nombre' => $link->congregacion->nombre ?? null,
        ]);

        return redirect()->route('pub.listado.free');
    }

    public function verify(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $link = LinkAcceso::with('congregacion')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$link) {
            abort(403);
        }

        if (!Hash::check($request->password, $link->password_hash)) {
            return back()->withErrors(['password' => 'Contraseña incorrecta']);
        }

        session([
            'free_access' => true,
            'free_token' => $token,
            'free_congregacion_id' => $link->congregacion_id,
            'free_congregacion_nombre' => $link->congregacion->nombre ?? null,
        ]);

        return redirect()->route('pub.listado.free');
    }

    public function destroy($id)
    {
        $link = LinkAcceso::where('congregacion_id', auth()->user()->congregacion_id)
            ->findOrFail($id);

        $link->delete();

        return back()->with('success', 'Link revocado.');
    }
}