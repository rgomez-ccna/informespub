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
            'fecha' => ['required', 'date'],
            'presidente' => ['required', 'string'],
            'lector' => ['required', 'string'],
        ]);
        ReunionPublica::create($data);
        return to_route('publica.index');
    }

    public function edit(ReunionPublica $reunion)
    {
        return view('tablero.publica.form', ['registro' => $reunion]);
    }

    public function update(Request $request, ReunionPublica $reunion)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'presidente' => ['required', 'string'],
            'lector' => ['required', 'string'],
        ]);
        $reunion->update($data);
        return to_route('publica.index');
    }

    public function destroy(ReunionPublica $reunion)
    {
        $reunion->delete();
        return to_route('publica.index');
    }
}
