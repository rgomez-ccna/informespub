<?php

namespace App\Http\Controllers;

use App\Models\Publicador;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Registro;

class PublicadorController extends Controller
{
   

    public function index(Request $request)
    {
        $nombre = $request->get('nombre');
    
        $publicadors = Publicador::with('registros')
            ->where('nombre', 'like', '%'.$nombre.'%')
            ->orderBy('grupo','ASC')
            ->get();
    
        // Calcular estados
        $lastReportStatuses = [];
        $publisherActivityStatuses = [];
    
        foreach ($publicadors as $pub) {
            $lastReportStatuses[$pub->id] = $this->lastReportStatus($pub->id);
            $publisherActivityStatuses[$pub->id] = $this->publisherActivityStatus($pub->id);
        }
    
        return view('pub.index', compact('publicadors', 'lastReportStatuses', 'publisherActivityStatuses'));
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





    // --------------------
// Estado de INFORME
private function lastReportStatus($publicadorId)
{
    $currentMonthStart = now()->startOfMonth();
    $currentMonthEnd = now()->endOfMonth();

    $lastReport = Registro::where('id_publicador', $publicadorId)
        ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
        ->first();

    return $lastReport ? 'success' : 'danger';
}

// --------------------
// Estado de ACTIVIDAD
private function publisherActivityStatus($publicadorId)
{
    $reports = Registro::where('id_publicador', $publicadorId)
        ->whereBetween('created_at', [now()->subMonths(7), now()])
        ->orderBy('created_at')
        ->get();

    if ($reports->isEmpty()) return 'inactivo';

    $consecutive = 1;
    $prevDate = Carbon::parse($reports->first()->created_at);

    for ($i = 1; $i < $reports->count(); $i++) {
        $date = Carbon::parse($reports[$i]->created_at);
        $diff = $prevDate->diffInMonths($date);

        if ($diff == 1 || ($prevDate->month == 12 && $date->month == 1)) {
            $consecutive++;
        } else {
            $consecutive = 1;
        }

        if ($consecutive == 6) return 'activo';
        $prevDate = $date;
    }

    return ($prevDate->diffInMonths(now()) < 6) ? 'irregular' : 'inactivo';
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


//Buscar publicadores por nombre
public function buscar(Request $request)
{
    $termino = $request->get('q');

    $publicadores = Publicador::where('nombre', 'like', '%' . $termino . '%')
        ->orderBy('nombre')
        ->limit(10)
        ->pluck('nombre');

    return response()->json($publicadores);
}

}
