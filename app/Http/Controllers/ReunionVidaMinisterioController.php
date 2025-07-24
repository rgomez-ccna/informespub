<?php

namespace App\Http\Controllers;

use App\Models\ReunionVidaMinisterio;
use Illuminate\Http\Request;

class ReunionVidaMinisterioController extends Controller
{
   public function index(Request $request)
{
    $query = ReunionVidaMinisterio::orderBy('fecha', 'asc');

    // Filtrado por fechas (con valores por defecto al mes actual)
    $desde = $request->input('desde') ?? now()->startOfMonth()->toDateString();
    $hasta = $request->input('hasta') ?? now()->copy()->addMonths(2)->endOfMonth()->toDateString();

    // Si modo = uno y tiene ID → mostrar solo uno
    if ($request->modo === 'uno' && $request->id) {
        $registros = ReunionVidaMinisterio::where('id', $request->id)->get();
    } else {
        $registros = $query
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->get();
    }

    return view('tablero.vidaministerio.index', compact('registros', 'desde', 'hasta'));
}


    public function create()
    {
        $programa = null;
        return view('tablero.vidaministerio.form', compact('programa'));
    }

    public function store(Request $request)
    {
        ReunionVidaMinisterio::create([
            'fecha' => $request->fecha,
            'lectura_semanal' => $request->lectura_semanal,
            'presidente' => $request->presidente,
            'presidente_ayudante' => $request->presidente_ayudante, // <-- FALTABA
            'consejero_auxiliar' => $request->consejero_auxiliar,
            'consejero_ayudante' => $request->consejero_ayudante,   // <-- FALTABA
            'cancion_inicio' => $request->cancion_inicio,
            'oracion_inicio' => $request->oracion_inicio,

            'tesoro_titulo' => $request->tesoro_titulo,
            'tesoro_disertante' => $request->tesoro_disertante,
            'perlas_disertante' => $request->perlas_disertante,
            'lectura_lector_principal' => $request->lectura_lector_principal,
            'lectura_lector_auxiliar' => $request->lectura_lector_auxiliar,

            'nombre_sala_auxiliar' => $request->nombre_sala_auxiliar,

            'asignaciones_maestros' => $request->asignaciones_maestros,
            'cancion_medio' => $request->cancion_medio,
            'vida_cristiana' => $request->vida_cristiana,

            'estudio_conductor' => $request->estudio_conductor,
            'estudio_lector' => $request->estudio_lector,

            'cancion_final' => $request->cancion_final,
            'oracion_final' => $request->oracion_final,
        ]);

        return redirect()->route('vidaministerio.index')->with('ok', 'Programa guardado correctamente.');
    }

    public function edit($id)
    {
        $programa = ReunionVidaMinisterio::findOrFail($id);
        return view('tablero.vidaministerio.form', compact('programa'));
    }

    public function update(Request $request, $id)
    {
        $registro = ReunionVidaMinisterio::findOrFail($id);

        $registro->update([
            'fecha' => $request->fecha,
            'lectura_semanal' => $request->lectura_semanal,
            'presidente' => $request->presidente,
            'presidente_ayudante' => $request->presidente_ayudante, // <-- FALTABA
            'consejero_auxiliar' => $request->consejero_auxiliar,
            'consejero_ayudante' => $request->consejero_ayudante,   // <-- FALTABA
            'cancion_inicio' => $request->cancion_inicio,
            'oracion_inicio' => $request->oracion_inicio,

            'tesoro_titulo' => $request->tesoro_titulo,
            'tesoro_disertante' => $request->tesoro_disertante,
            'perlas_disertante' => $request->perlas_disertante,
            'lectura_lector_principal' => $request->lectura_lector_principal,
            'lectura_lector_auxiliar' => $request->lectura_lector_auxiliar,

            'nombre_sala_auxiliar' => $request->nombre_sala_auxiliar,

            'asignaciones_maestros' => $request->asignaciones_maestros,
            'cancion_medio' => $request->cancion_medio,
            'vida_cristiana' => $request->vida_cristiana,

            'estudio_conductor' => $request->estudio_conductor,
            'estudio_lector' => $request->estudio_lector,

            'cancion_final' => $request->cancion_final,
            'oracion_final' => $request->oracion_final,
        ]);

        return redirect()->route('vidaministerio.index')->with('ok', 'Programa actualizado correctamente.');
    }

    public function destroy($id)
    {
        $registro = ReunionVidaMinisterio::findOrFail($id);
        $registro->delete();
        return back()->with('ok', 'Programa eliminado correctamente.');
    }
}
