<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\ProgramaCampo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProgramaController extends Controller
{
    public function tablero()
    {
        $programas = Programa::where('congregacion_id', Auth::user()->congregacion_id)
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('tablero.index', compact('programas'));
    }

    public function index()
    {
        $programas = Programa::where('congregacion_id', Auth::user()->congregacion_id)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(20);

        return view('programas.index', compact('programas'));
    }

    public function create()
    {
        return view('programas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer'],
        ]);

        Programa::create([
            'congregacion_id' => Auth::user()->congregacion_id,
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->nombre),
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo', true),
            'orden' => $request->orden ?? 0,
        ]);

        return redirect()
            ->route('programas.index')
            ->with('success', 'Programa creado correctamente.');
    }

    public function show(Programa $programa)
    {
        $this->autorizarPrograma($programa);

        $programa->load(['campos' => function ($q) {
            $q->orderBy('orden');
        }]);

        return view('programas.show', compact('programa'));
    }

    public function edit(Programa $programa)
    {
        $this->autorizarPrograma($programa);

        $programa->load(['campos' => function ($q) {
            $q->orderBy('orden');
        }]);

        return view('programas.edit', compact('programa'));
    }

    public function update(Request $request, Programa $programa)
    {
        $this->autorizarPrograma($programa);

        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer'],
        ]);

        $programa->update([
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->nombre),
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo'),
            'orden' => $request->orden ?? 0,
        ]);

        return redirect()
            ->route('programas.edit', $programa)
            ->with('success', 'Programa actualizado correctamente.');
    }

    public function destroy(Programa $programa)
    {
        $this->autorizarPrograma($programa);

        $programa->delete();

        return redirect()
            ->route('programas.index')
            ->with('success', 'Programa eliminado correctamente.');
    }

    public function storeCampo(Request $request, Programa $programa)
    {
        $this->autorizarPrograma($programa);

        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:texto,textarea,numero,fecha,hora,select,checkbox'],
            'opciones' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer'],
        ]);

        $opciones = null;

        if ($request->tipo === 'select' && $request->filled('opciones')) {
            $opciones = collect(explode("\n", $request->opciones))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->toArray();
        }

        ProgramaCampo::create([
            'programa_id' => $programa->id,
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->nombre),
            'tipo' => $request->tipo,
            'opciones' => $opciones,
            'obligatorio' => $request->boolean('obligatorio'),
            'visible_en_listado' => $request->boolean('visible_en_listado', true),
            'buscable' => $request->boolean('buscable'),
            'activo' => true,
            'orden' => $request->orden ?? 0,
        ]);

        return redirect()
            ->route('programas.edit', $programa)
            ->with('success', 'Campo agregado correctamente.');
    }

    public function destroyCampo(Programa $programa, ProgramaCampo $campo)
    {
        $this->autorizarPrograma($programa);

        if ($campo->programa_id !== $programa->id) {
            abort(404);
        }

        $campo->delete();

        return redirect()
            ->route('programas.edit', $programa)
            ->with('success', 'Campo eliminado correctamente.');
    }

    private function autorizarPrograma(Programa $programa): void
    {
        if ($programa->congregacion_id !== Auth::user()->congregacion_id) {
            abort(403);
        }
    }
}