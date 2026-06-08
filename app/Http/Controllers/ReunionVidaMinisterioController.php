<?php

namespace App\Http\Controllers;

use App\Models\ReunionVidaMinisterio;
use Illuminate\Http\Request;

class ReunionVidaMinisterioController extends Controller
{
    private function puedeVerTablero()
    {
        abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador', 'tablero']), 403);
    }

    private function puedeGestionarTablero()
    {
        abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador']), 403);
    }

    private function programasQuery()
    {
        return ReunionVidaMinisterio::where('congregacion_id', auth()->user()->congregacion_id);
    }

    private function buscarProgramaSeguro($id)
    {
        return $this->programasQuery()->findOrFail($id);
    }

    public function index(Request $request)
    {
        $this->puedeVerTablero();

        $desde = $request->input('desde') ?? now()->startOfMonth()->toDateString();
        $hasta = $request->input('hasta') ?? now()->copy()->addMonths(2)->endOfMonth()->toDateString();

        if ($request->modo === 'uno' && $request->id) {
            $registros = $this->programasQuery()
                ->where('id', $request->id)
                ->get();
        } else {
            $registros = $this->programasQuery()
                ->whereDate('fecha', '>=', $desde)
                ->whereDate('fecha', '<=', $hasta)
                ->orderBy('fecha', 'asc')
                ->get();
        }

        return view('tablero.vidaministerio.index', compact('registros', 'desde', 'hasta'));
    }

    public function create()
    {
        $this->puedeGestionarTablero();

        $programa = null;

        return view('tablero.vidaministerio.form', compact('programa'));
    }

    public function store(Request $request)
    {
        $this->puedeGestionarTablero();

        ReunionVidaMinisterio::create([
            'congregacion_id' => auth()->user()->congregacion_id,

            'fecha' => $request->fecha,
            'lectura_semanal' => $request->lectura_semanal,
            'presidente' => $request->presidente,
            'presidente_ayudante' => $request->presidente_ayudante,
            'consejero_auxiliar' => $request->consejero_auxiliar,
            'consejero_ayudante' => $request->consejero_ayudante,
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
        $this->puedeGestionarTablero();

        $programa = $this->buscarProgramaSeguro($id);

        return view('tablero.vidaministerio.form', compact('programa'));
    }

    public function update(Request $request, $id)
    {
        $this->puedeGestionarTablero();

        $registro = $this->buscarProgramaSeguro($id);

        $registro->update([
            'congregacion_id' => auth()->user()->congregacion_id,

            'fecha' => $request->fecha,
            'lectura_semanal' => $request->lectura_semanal,
            'presidente' => $request->presidente,
            'presidente_ayudante' => $request->presidente_ayudante,
            'consejero_auxiliar' => $request->consejero_auxiliar,
            'consejero_ayudante' => $request->consejero_ayudante,
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
        $this->puedeGestionarTablero();

        $registro = $this->buscarProgramaSeguro($id);
        $registro->delete();

        return back()->with('ok', 'Programa eliminado correctamente.');
    }
}