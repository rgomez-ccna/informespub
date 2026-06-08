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

        $bloques = ProgramaBloque::where('programa_id', $programa->id)
            ->where('congregacion_id', Auth::user()->congregacion_id)
            ->orderByDesc('fecha_inicio')
            ->orderByDesc('id')
            ->paginate(20);

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
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'observaciones' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer'],
        ]);

        $bloque = ProgramaBloque::create([
            'congregacion_id' => Auth::user()->congregacion_id,
            'programa_id' => $programa->id,
            'user_id' => Auth::id(),
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'observaciones' => $request->observaciones,
            'activo' => true,
            'orden' => $request->orden ?? 0,
        ]);

        return redirect()
            ->route('programas.bloques.registros.index', [$programa, $bloque])
            ->with('success', 'Bloque creado correctamente.');
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
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'observaciones' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer'],
        ]);

        $bloque->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'observaciones' => $request->observaciones,
            'activo' => $request->boolean('activo'),
            'orden' => $request->orden ?? 0,
        ]);

        return redirect()
            ->route('programas.bloques.index', $programa)
            ->with('success', 'Bloque actualizado correctamente.');
    }

    public function destroy(Programa $programa, ProgramaBloque $bloque)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);

        $bloque->delete();

        return redirect()
            ->route('programas.bloques.index', $programa)
            ->with('success', 'Bloque eliminado correctamente.');
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