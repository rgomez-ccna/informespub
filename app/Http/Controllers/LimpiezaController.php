<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Limpieza;
use App\Models\LimpiezaMensual;
use Illuminate\Pagination\Paginator;

class LimpiezaController extends Controller
{




public function index(Request $request)
{
    $porPagina = $request->get('per_page', 10);
    $porPaginaMensual = $request->get('per_page_mensual', 10);

    // normal
    $registros = Limpieza::orderBy('id', 'asc')->paginate($porPagina);

    // paginación personalizada con nombre distinto
    $mensual = LimpiezaMensual::orderBy('fecha', 'asc')
        ->paginate($porPaginaMensual, ['*'], 'page_mensual');

        $observacionGeneral = LimpiezaMensual::whereNotNull('observacion_general')
    ->where('observacion_general', '!=', '')
    ->orderByDesc('id')
    ->first()?->observacion_general;


return view('tablero.limpieza.index', compact('registros', 'porPagina', 'mensual', 'porPaginaMensual', 'observacionGeneral'));
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
