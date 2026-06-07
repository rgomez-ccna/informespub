<?php

namespace App\Http\Controllers;

use App\Models\Congregacion;
use Illuminate\Http\Request;

class CongregacionController extends Controller
{
    private function soloSuperadmin()
    {
        abort_if(auth()->user()->role !== 'superadmin', 403);
    }

    public function index()
    {
        $this->soloSuperadmin();

        $congregaciones = Congregacion::withCount(['users', 'publicadors', 'registros'])
            ->orderBy('nombre')
            ->get();

        return view('congregaciones.index', compact('congregaciones'));
    }

    public function create()
    {
        $this->soloSuperadmin();

        return view('congregaciones.form');
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'activa' => 'nullable|boolean',
        ]);

        $data['activa'] = $request->has('activa');

        Congregacion::create($data);

        return redirect()->route('congregaciones.index')
            ->with('success', 'Congregación creada correctamente.');
    }

    public function edit(Congregacion $congregacion)
    {
        $this->soloSuperadmin();

        return view('congregaciones.form', compact('congregacion'));
    }

    public function update(Request $request, Congregacion $congregacion)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'activa' => 'nullable|boolean',
        ]);

        $data['activa'] = $request->has('activa');

        $congregacion->update($data);

        return redirect()->route('congregaciones.index')
            ->with('success', 'Congregación actualizada correctamente.');
    }

    public function destroy(Congregacion $congregacion)
    {
        $this->soloSuperadmin();

        $congregacion->delete();

        return redirect()->route('congregaciones.index')
            ->with('success', 'Congregación eliminada correctamente.');
    }
}