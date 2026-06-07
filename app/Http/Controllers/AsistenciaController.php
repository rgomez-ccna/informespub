<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;

class AsistenciaController extends Controller
{


private function puedeVerDatos()
{
    if (auth()->check()) {
        abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador', 'tablero']), 403);
        return;
    }

    abort_unless(session('free_access') && session('free_congregacion_id'), 403);
}

private function puedeGestionarDatos()
{
    abort_if(!auth()->check(), 403);
    abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador']), 403);
}


private function congregacionActualId()
{
    return auth()->check()
        ? auth()->user()->congregacion_id
        : session('free_congregacion_id');
}



  private function asistenciasQuery()
{
    return Asistencia::where('congregacion_id', $this->congregacionActualId());
}

    private function buscarAsistenciaSegura($id)
    {
        return $this->asistenciasQuery()->findOrFail($id);
    }

    public function index()
    {
        $this->puedeGestionarDatos();

        $asistencias = $this->asistenciasQuery()
            ->orderBy('a_servicio', 'desc')
            ->orderByRaw("FIELD(mes, 'Septiembre','Octubre','Noviembre','Diciembre','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto')")
            ->get()
            ->groupBy('tipo')
            ->map(function ($tipo) {
                return $tipo->groupBy('a_servicio');
            });

        return view('asistencia.index', compact('asistencias'));
    }

    public function create()
    {
        $this->puedeGestionarDatos();

        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        $mesActual = now()->month;
        $añoServicio = ($mesActual >= 9) ? now()->year + 1 : now()->year;

        return view('asistencia.form', compact('añoServicio', 'meses'));
    }

    public function store(Request $request)
    {
        $this->puedeGestionarDatos();

        $request->validate([
            'a_servicio' => 'required',
            'mes' => 'required',
            'tipo' => 'required',
            'reuniones' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['congregacion_id'] = auth()->user()->congregacion_id;

        Asistencia::create($data);

        return redirect()->route('asist.index')->with('success', 'Cargado');
    }

    public function edit(Asistencia $asistencia)
    {
        $this->puedeGestionarDatos();

        $asistencia = $this->buscarAsistenciaSegura($asistencia->id);

        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        $mesActual = now()->month;
        $añoServicio = ($mesActual >= 9) ? now()->year + 1 : now()->year;

        return view('asistencia.form', compact('asistencia', 'añoServicio', 'meses'));
    }

    public function update(Request $request, Asistencia $asistencia)
    {
        $this->puedeGestionarDatos();

        $asistencia = $this->buscarAsistenciaSegura($asistencia->id);

        $request->validate([
            'a_servicio' => 'required',
            'mes' => 'required',
            'tipo' => 'required',
            'reuniones' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['congregacion_id'] = auth()->user()->congregacion_id;

        $asistencia->update($data);

        return redirect()->route('asist.index')->with('success', 'Actualizado');
    }

public function modal()
{
    $this->puedeVerDatos();

    $asistencias = $this->asistenciasQuery()
        ->orderBy('a_servicio', 'desc')
        ->orderByRaw("FIELD(mes, 'Septiembre','Octubre','Noviembre','Diciembre','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto')")
        ->get()
        ->groupBy('tipo')
        ->map(fn ($tipo) => $tipo->groupBy('a_servicio'));

    return view('asistencia.modal', compact('asistencias'));
}

}