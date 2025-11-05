<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;

class AsistenciaController extends Controller
{
    public function index()
{
    $asistencias = Asistencia::orderBy('a_servicio','desc')
        ->orderByRaw("FIELD(mes, 'Septiembre','Octubre','Noviembre','Diciembre','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto')")
        ->get()
        ->groupBy('tipo') // FS / ES
        ->map(function($tipo){
            return $tipo->groupBy('a_servicio');
        });

    return view('asistencia.index',compact('asistencias'));
}

public function create()
{
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    $mesActual = now()->month;
    $añoServicio = ($mesActual >= 9) ? now()->year + 1 : now()->year;

    return view('asistencia.form',compact('añoServicio','meses'));
}


    public function store(Request $request)
    {
        $request->validate([
            'a_servicio'=>'required',
            'mes'=>'required',
            'tipo'=>'required',
            'reuniones'=>'required|numeric',
            'total'=>'required|numeric'
        ]);

        Asistencia::create($request->all());

        return redirect()->route('asist.index')->with('success','Cargado');
    }

    public function edit(Asistencia $asistencia)
{
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    $mesActual = now()->month;
    $añoServicio = ($mesActual >= 9) ? now()->year + 1 : now()->year;

    return view('asistencia.form',compact('asistencia','añoServicio','meses'));
}


    public function update(Request $request, Asistencia $asistencia)
    {
        $request->validate([
            'a_servicio'=>'required',
            'mes'=>'required',
            'tipo'=>'required',
            'reuniones'=>'required|numeric',
            'total'=>'required|numeric'
        ]);

        $asistencia->update($request->all());

        return redirect()->route('asist.index')->with('success','Actualizado');
    }

}
