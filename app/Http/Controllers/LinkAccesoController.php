<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LinkAcceso;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LinkAccesoController extends Controller
{
   // POST (solo para admins/logueados): crea link
    public function store(Request $request)
    {
        $request->validate([
            'dias' => 'nullable|integer|min:1|max:60',
            'password' => 'nullable|string|max:100',
        ]);

        $token = Str::random(64);
        $hash = $request->filled('password') ? Hash::make($request->password) : null;

        $link = LinkAcceso::create([
            'token'         => $token,
            'expires_at'    => Carbon::now()->addDays($request->input('dias', 7)),
            'password_hash' => $hash,
            'created_by'    => auth()->id(),
        ]);

        // te devuelvo la URL lista
        return back()->with('success', url('/acceso/'.$link->token));
    }

    // GET /acceso/{token} → si tiene password, pide; si no, deja pasar
    public function enter($token)
{
    $link = LinkAcceso::where('token',$token)
        ->where('expires_at','>',now())->first();
    if (!$link) abort(403);

    if ($link->password_hash) {
        return view('access.password', compact('token'));
    }

    session(['free_access' => true, 'free_token' => $token]);

    // ⬇️ ir al alias libre (evita pegar la ruta con auth)
    return redirect()->route('pub.listado.free');
}

public function verify(Request $request, $token)
{
    $request->validate(['password' => 'required|string']);

    $link = LinkAcceso::where('token',$token)
        ->where('expires_at','>',now())->first();
    if (!$link) abort(403);

    if (!Hash::check($request->password, $link->password_hash)) {
        return back()->withErrors(['password' => 'Contraseña incorrecta']);
    }

    // ⬇️ importante: ir al alias libre
   
    session(['free_access' => true, 'free_token' => $token]);
return redirect()->route('pub.listado.free');

}



    // (opcional) DELETE revocar
    public function destroy($id)
    {
        LinkAcceso::findOrFail($id)->delete();
        return back()->with('success','Link revocado.');
    }
}
