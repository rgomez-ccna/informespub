<?php

namespace App\Http\Controllers;

use App\Models\SalidaMinisterio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalidaMinisterioController extends Controller
{
    // INDEX: mostrar salidas de la semana activa
   public function index()
{
    $todas = SalidaMinisterio::orderBy('fecha')->orderBy('hora')->get();

    $bloques = [];
    $bloqueActual = [];
    $contador = 0;

    foreach ($todas as $registro) {
        if ($registro->es_nueva_semana) {
            if (!empty($bloqueActual)) {
                $bloques[] = collect($bloqueActual)->groupBy('fecha');
                $bloqueActual = [];
            }
        }

        $bloqueActual[] = $registro;
    }

    if (!empty($bloqueActual)) {
        $bloques[] = collect($bloqueActual)->groupBy('fecha');
    }

    return view('tablero.ministerio.index', compact('bloques'));
}



    // CREAR
    public function create()
    {
        return view('tablero.ministerio.form');
    }


    // GUARDAR
    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['es_nueva_semana'] = $request->has('es_nueva_semana');
        $data['es_fila_info'] = $request->has('es_fila_info');

        SalidaMinisterio::create($data);
        return to_route('ministerio.index');
    }

 // EDITAR
public function edit(\App\Models\SalidaMinisterio $ministerio)
{
    return view('tablero.ministerio.form', ['registro' => $ministerio]);
}

// ACTUALIZAR
public function update(\Illuminate\Http\Request $request, \App\Models\SalidaMinisterio $ministerio)
{
    $data = $this->validateData($request);
    $data['es_nueva_semana'] = $request->has('es_nueva_semana');
    $data['es_fila_info']    = $request->has('es_fila_info');

    $ministerio->update($data);
    return to_route('ministerio.index');
}


    // ELIMINAR
    public function destroy(SalidaMinisterio $ministerio)
    {
        $ministerio->delete();
        return to_route('ministerio.index');
    }


    // VALIDACIÓN
    private function validateData(Request $request): array
    {
        return $request->validate([
            'fecha'           => ['required', 'date'],
            'hora'            => ['nullable', 'string', 'max:20'],
            'conductor'       => ['nullable', 'string', 'max:100'],
            'punto_encuentro' => ['nullable', 'string', 'max:255'],
            'territorio'      => ['nullable', 'string', 'max:100'],
            'es_nueva_semana' => ['nullable', 'boolean'],
            'es_fila_info'    => ['nullable', 'boolean'],
        ]);
    }
}
