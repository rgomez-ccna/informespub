<?php
namespace App\Http\Controllers; 
use App\Models\ReunionPublica;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReunionPublicaController extends Controller
{
    public function index()
    {
        $registros = ReunionPublica::orderBy('fecha')->get();
        return view('tablero.publica.index', compact('registros'));
    }

    public function create()
    {
        return view('tablero.publica.form');
    }

   public function store(Request $request)
{
    $data = $request->validate([
        'fecha' => 'required|date',
        'presidente' => 'required|string',
        'lector' => 'required|string',
        'es_nuevo_programa' => 'boolean',
    ]);

    ReunionPublica::create($data);

    return back()->with('ok', 'Programa guardado correctamente.');
}


 public function edit($id)
{
    $registro = ReunionPublica::findOrFail($id);
    return view('tablero.publica.form', compact('registro'));
}


   public function update(Request $request, $id)
{
    $data = $request->validate([
        'fecha' => 'required|date',
        'presidente' => 'required|string',
        'lector' => 'required|string',
        'es_nuevo_programa' => 'boolean',
    ]);

    $registro = ReunionPublica::findOrFail($id);
    $registro->update($data);

    return back()->with('ok', 'Programa actualizado correctamente.');
}


   public function destroy($id)
{
    $registro = ReunionPublica::findOrFail($id);
    $registro->delete();

    return back()->with('ok', 'Eliminado correctamente.');
}

}
