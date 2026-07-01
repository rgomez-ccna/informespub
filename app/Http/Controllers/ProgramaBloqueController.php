<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\ProgramaBloque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramaBloqueController extends Controller
{
    public function index(Programa $programa)
    {
        $this->autorizarPrograma($programa);

        $programa->load(['campos' => function ($q) {
            $q->where('activo', true)->orderBy('orden');
        }]);

        $bloques = ProgramaBloque::with(['registros.valores.campo'])
            ->where('programa_id', $programa->id)
            ->where('congregacion_id', Auth::user()->congregacion_id)
            ->orderByDesc('id')
            ->get();

        return view('programas.bloques.index', compact('programa', 'bloques'));
    }

    public function create(Programa $programa)
    {
        $this->autorizarPrograma($programa);

        $ultimoBloque = ProgramaBloque::where('programa_id', $programa->id)
            ->where('congregacion_id', Auth::user()->congregacion_id)
            ->latest('id')
            ->first();

        return view('programas.bloques.create', compact('programa', 'ultimoBloque'));
    }

    public function store(Request $request, Programa $programa)
    {
        $this->autorizarPrograma($programa);

        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
        ]);

        ProgramaBloque::create([
            'congregacion_id' => Auth::user()->congregacion_id,
            'programa_id' => $programa->id,
            'user_id' => Auth::id(),
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'observaciones' => $request->observaciones,
            'activo' => true,
            'orden' => 0,
        ]);

        return redirect()
            ->route('programas.bloques.index', $programa)
            ->with('success', 'Planilla creada correctamente.');
    }

    public function edit(Programa $programa, ProgramaBloque $bloque)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);

        return view('programas.bloques.edit', compact('programa', 'bloque'));
    }

    public function update(Request $request, Programa $programa, ProgramaBloque $bloque)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);

        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $bloque->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'observaciones' => $request->observaciones,
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()
            ->route('programas.bloques.index', $programa)
            ->with('success', 'Planilla actualizada correctamente.');
    }

    public function destroy(Programa $programa, ProgramaBloque $bloque)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);

        $bloque->delete();

        return redirect()
            ->route('programas.bloques.index', $programa)
            ->with('success', 'Planilla eliminada correctamente.');
    }

    private function autorizarPrograma(Programa $programa): void
    {
        if ($programa->congregacion_id !== Auth::user()->congregacion_id) {
            abort(403);
        }
    }

    private function autorizarBloque(Programa $programa, ProgramaBloque $bloque): void
    {
        if (
            $bloque->programa_id !== $programa->id ||
            $bloque->congregacion_id !== Auth::user()->congregacion_id
        ) {
            abort(404);
        }
    }
}
