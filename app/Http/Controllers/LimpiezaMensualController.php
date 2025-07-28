<?php

namespace App\Http\Controllers;

use App\Models\LimpiezaMensual;
use Illuminate\Http\Request;

class LimpiezaMensualController extends Controller
{
    public function create()
    {
        return view('tablero.limpieza_mensual.form');
    }

    public function store(Request $request)
    {
        LimpiezaMensual::create($request->all());
        return redirect()->route('limpieza.index');
    }

    public function edit($id)
    {
        $registro = LimpiezaMensual::findOrFail($id);
        return view('tablero.limpieza_mensual.form', compact('registro'));
    }

    public function update(Request $request, $id)
    {
        $registro = LimpiezaMensual::findOrFail($id);
        $registro->update($request->all());
        return redirect()->route('limpieza.index');
    }

    public function destroy($id)
    {
        LimpiezaMensual::destroy($id);
        return redirect()->route('limpieza.index');
    }
}
