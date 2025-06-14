<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Limpieza;

class LimpiezaController extends Controller
{
    public function index()
{
   $registros = Limpieza::orderByDesc('id')->take(5)->get()->reverse();

    return view('tablero.limpieza.index', compact('registros'));
}

public function create()
{
    return view('tablero.limpieza.form');
}

public function store(Request $request)
{
    Limpieza::create($request->all());
    return redirect()->route('limpieza.index');
}

public function edit($id)
{
    $registro = Limpieza::findOrFail($id);
    return view('tablero.limpieza.form', compact('registro'));
}

public function update(Request $request, $id)
{
    $registro = Limpieza::findOrFail($id);
    $registro->update($request->all());
    return redirect()->route('limpieza.index');
}

public function destroy($id)
{
    Limpieza::destroy($id);
    return redirect()->route('limpieza.index');
}

}
