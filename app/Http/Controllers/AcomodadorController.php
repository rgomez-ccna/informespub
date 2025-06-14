<?php

namespace App\Http\Controllers;

use App\Models\Acomodador;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AcomodadorController extends Controller
{
    /* ---------- LISTA: solo el mes más reciente ---------- */
    public function index()
{
    $inicio = Acomodador::where('es_nuevo_programa', true)
                ->orderByDesc('fecha')
                ->first();

    if (!$inicio) {
        $registros = collect();
        $texto = null;
    } else {
        $registros = Acomodador::where('fecha', '>=', $inicio->fecha)
                        ->orderBy('fecha')
                        ->get();

        // Nota final del último registro con nota no nula
        $texto = Acomodador::where('fecha', '>=', $inicio->fecha)
                    ->whereNotNull('nota_final')
                    ->orderByDesc('id')
                    ->value('nota_final');
    }

    return view('tablero.acomodadores.index', compact('registros', 'texto'));
}


    /* ---------- FORMULARIOS ---------- */
    public function create()
    {
        return view('tablero.acomodadores.form');
    }

    public function edit(Acomodador $acomodador)
    {
        return view('tablero.acomodadores.form', ['registro' => $acomodador]);
    }

    /* ---------- GUARDAR / ACTUALIZAR ---------- */
    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['es_nuevo_programa'] = $request->has('es_nuevo_programa'); // <-- esta línea
        Acomodador::create($data);
        return to_route('acomodadores.index');
    }


    public function update(Request $request, Acomodador $acomodador)
    {
        $data = $this->validateData($request);
        $data['es_nuevo_programa'] = $request->has('es_nuevo_programa'); // <-- esta línea
        $acomodador->update($data);
        return to_route('acomodadores.index');
    }


    /* ---------- ELIMINAR ---------- */
    public function destroy(Acomodador $acomodador)
    {
        $acomodador->delete();
        return to_route('acomodadores.index');
    }

    /* ---------- VALIDACIÓN REUTILIZABLE ---------- */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'fecha'      => ['required','date'],
            'acceso_1'   => ['required','string','max:100'],
            'acceso_2'   => ['required','string','max:100'],
            'auditorio'  => ['required','string','max:100'],
            'es_nuevo_programa' => ['nullable', 'boolean'],
            'nota_final' => ['nullable','string'],
        ]);
    }
}
