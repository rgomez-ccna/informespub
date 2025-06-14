<?php

namespace App\Http\Controllers;

use App\Models\DiscursoPublico;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DiscursoPublicoController extends Controller
{
    public function index()
    {
        // VISITAS
        $inicioVisita = DiscursoPublico::where('tipo', 'visita')
            ->where('es_nuevo_programa_visita', true)
            ->orderByDesc('fecha')->first();

        $visitas = $inicioVisita
            ? DiscursoPublico::where('tipo', 'visita')
                ->where('fecha', '>=', $inicioVisita->fecha)
                ->orderBy('fecha')
                ->get()
            : collect();

        // SALIDAS
        $inicioSalida = DiscursoPublico::where('tipo', 'salida')
            ->where('es_nuevo_programa_salida', true)
            ->orderByDesc('fecha')->first();

        $salidas = $inicioSalida
            ? DiscursoPublico::where('tipo', 'salida')
                ->where('fecha', '>=', $inicioSalida->fecha)
                ->orderBy('fecha')
                ->get()
            : collect();

        return view('tablero.discursos.index', compact('visitas', 'salidas'));
    }

    public function create()
    {
        return view('tablero.discursos.form');
    }

    public function edit(DiscursoPublico $discurso)
    {
        return view('tablero.discursos.form', ['registro' => $discurso]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['es_nuevo_programa_visita'] = $request->has('es_nuevo_programa_visita');
        $data['es_nuevo_programa_salida'] = $request->has('es_nuevo_programa_salida');

        DiscursoPublico::create($data);
        return to_route('discursos.index');
    }

    public function update(Request $request, DiscursoPublico $discurso)
    {
        $data = $this->validateData($request);
        $data['es_nuevo_programa_visita'] = $request->has('es_nuevo_programa_visita');
        $data['es_nuevo_programa_salida'] = $request->has('es_nuevo_programa_salida');

        $discurso->update($data);
        return to_route('discursos.index');
    }

    public function destroy(DiscursoPublico $discurso)
    {
        $discurso->delete();
        return to_route('discursos.index');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'fecha'                   => ['required', 'date'],
            'conferencia'             => ['required', 'string'],
            'disertante'              => ['required', 'string'],
            'congregacion'            => ['required', 'string'],
            'horario'                 => ['nullable', 'string'],
            'tipo'                    => ['required', 'in:visita,salida'],
            'es_nuevo_programa_visita' => ['nullable', 'boolean'],
            'es_nuevo_programa_salida' => ['nullable', 'boolean'],
        ]);
    }
}
