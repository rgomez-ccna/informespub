<?php

namespace App\Http\Controllers;

use App\Models\Publicador;
use Illuminate\Http\Request;

class PublicadorController extends Controller
{
    public function index(Request $request)
    {
        $nombre = $request->get('nombre');

        $publicadors = Publicador::where('nombre', 'like', '%' . $nombre . '%')
                        ->orderBy('grupo', 'ASC')
                        ->get();

        return view('pub.index', compact('publicadors'));
    }

    public function create()
{
    return view('pub.form');
}



    public function show($id)
    {
        $publicador = Publicador::findOrFail($id);
        return view('pub.show', compact('publicador'));
    }

    public function edit($id)
    {
        $publicador = Publicador::findOrFail($id);
        return view('pub.form', compact('publicador'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'grupo' => 'nullable|string|max:100'
        ]);
    
        $camposCheckbox = ['hombre','mujer','oo','ungido','anciano','sv','precursor'];
        foreach($camposCheckbox as $campo){
            $request[$campo] = $request->has($campo) ? 1 : 0;
        }
    
        Publicador::create($request->all());
    
        return redirect()->route('pub.index')->with('success','Publicador creado correctamente.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'grupo' => 'nullable|string|max:100'
        ]);
    
        $camposCheckbox = ['hombre','mujer','oo','ungido','anciano','sv','precursor'];
        foreach($camposCheckbox as $campo){
            $request[$campo] = $request->has($campo) ? 1 : 0;
        }
    
        $publicador = Publicador::findOrFail($id);
        $publicador->update($request->all());
    
        return redirect()->route('pub.index')->with('success','Publicador actualizado correctamente.');
    }
    

    public function destroy($id)
    {
        Publicador::destroy($id);
        return redirect()->route('pub.index')->with('success','Publicador eliminado correctamente.');
    }




    ///RESUMEN 
    // Vista tipo listado agrupado por grupo
public function listado()
{
    $publicadores = Publicador::orderBy('grupo')->get()->groupBy('grupo');
    return view('pub.listado', compact('publicadores'));
}

// Vista tipo tarjeta S-21 (detalles)
public function s21($id)
{
    $publicador = Publicador::findOrFail($id);

    $registros = $publicador->registros()
        ->orderBy('a_servicio', 'desc')
        ->orderByRaw("FIELD(mes, 'Septiembre','Octubre','Noviembre','Diciembre','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto')")
        ->get()
        ->groupBy('a_servicio');

    return view('pub.s21', compact('publicador', 'registros'));
}

}
