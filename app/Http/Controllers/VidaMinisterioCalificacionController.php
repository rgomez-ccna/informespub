<?php

namespace App\Http\Controllers;

use App\Models\Publicador;
use App\Models\VidaMinisterioCalificacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VidaMinisterioCalificacionController extends Controller
{
    private function puedeGestionar(): void
    {
        abort_if(!auth()->check(), 403);
        abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador']), 403);
    }

    private function congregacionId(): int
    {
        return auth()->user()->congregacion_id;
    }

    private function tiposAsignacion(): array
    {
        return [
            'presidente' => 'Presidente',
            'ayudante_auditorio' => 'Ayudante auditorio',
            'consejero_auxiliar' => 'Consejero auxiliar',
            'ayudante_auxiliar' => 'Ayudante auxiliar',
            'oracion' => 'Oración',
            'tesoro' => 'Tesoros',
            'perlas' => 'Perlas',
            'lectura_biblia' => 'Lectura Biblia',
            'maestro_estudiante' => 'Estudiante',
            'maestro_ayudante' => 'Ayudante',
            'vida_cristiana' => 'Vida cristiana',
            'estudio_conductor' => 'Conductor estudio',
            'estudio_lector' => 'Lector estudio',
        ];
    }

    public function index()
    {
        $this->puedeGestionar();

        $tipos = $this->tiposAsignacion();

        $publicadores = Publicador::where('congregacion_id', $this->congregacionId())
            ->where(function ($q) {
                $q->whereNull('estado')
                    ->orWhere('estado', '')
                    ->orWhere('estado', 'activo');
            })
            ->orderBy('grupo')
            ->orderBy('nombre')
            ->get();

        $calificaciones = VidaMinisterioCalificacion::where('congregacion_id', $this->congregacionId())
            ->where('activo', true)
            ->get()
            ->groupBy('publicador_id')
            ->map(fn ($items) => $items->pluck('tipo_asignacion')->toArray());

        return view('vida_ministerio.calificaciones.index', compact(
            'publicadores',
            'tipos',
            'calificaciones'
        ));
    }

    public function store(Request $request)
    {
        $this->puedeGestionar();

        $tiposPermitidos = array_keys($this->tiposAsignacion());

        $request->validate([
            'calificaciones' => ['nullable', 'array'],
            'calificaciones.*' => ['nullable', 'array'],
            'calificaciones.*.*' => ['string', Rule::in($tiposPermitidos)],
        ]);

        $publicadoresPermitidos = Publicador::where('congregacion_id', $this->congregacionId())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $seleccionadas = $request->input('calificaciones', []);

        VidaMinisterioCalificacion::where('congregacion_id', $this->congregacionId())->delete();

        $insert = [];
        $ahora = now();

        foreach ($seleccionadas as $publicadorId => $tipos) {
            $publicadorId = (int) $publicadorId;

            if (!in_array($publicadorId, $publicadoresPermitidos, true)) {
                continue;
            }

            foreach (array_unique($tipos ?? []) as $tipo) {
                if (!in_array($tipo, $tiposPermitidos, true)) {
                    continue;
                }

                $insert[] = [
                    'congregacion_id' => $this->congregacionId(),
                    'publicador_id' => $publicadorId,
                    'tipo_asignacion' => $tipo,
                    'activo' => true,
                    'observacion' => null,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ];
            }
        }

        if (!empty($insert)) {
            VidaMinisterioCalificacion::insert($insert);
        }

        return redirect()
            ->route('vida-ministerio.calificaciones.index')
            ->with('success', 'Calificaciones guardadas correctamente.');
    }
}