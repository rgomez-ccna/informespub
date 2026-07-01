<?php

namespace App\Http\Controllers;

use App\Models\Congregacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CongregacionController extends Controller
{
    private function soloSuperadmin()
    {
        abort_if(auth()->user()->role !== 'superadmin', 403);
    }

    private function soloSecretario()
    {
        abort_if(auth()->user()->role !== 'secretario', 403);
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
    public function datos()
    {
        $this->soloSecretario();

        $congregacion = auth()->user()->congregacion;
        abort_if(!$congregacion, 404);

        $congregacionId = $congregacion->id;

        $resumen = [
            'Usuarios' => DB::table('users')->where('congregacion_id', $congregacionId)->count(),
            'Publicadores' => DB::table('publicadors')->where('congregacion_id', $congregacionId)->count(),
            'Informes S-21' => DB::table('registros')->where('congregacion_id', $congregacionId)->count(),
            'Asistencia' => DB::table('asistencias')->where('congregacion_id', $congregacionId)->count(),
            'Tablero' => DB::table('programas')->where('congregacion_id', $congregacionId)->count()
                + DB::table('programa_bloques')->where('congregacion_id', $congregacionId)->count()
                + DB::table('programa_registros')->where('congregacion_id', $congregacionId)->count(),
            'Vida y Ministerio' => DB::table('vida_ministerios')->where('congregacion_id', $congregacionId)->count()
                + DB::table('vida_ministerio_partes')->where('congregacion_id', $congregacionId)->count()
                + DB::table('vida_ministerio_asignacions')->where('congregacion_id', $congregacionId)->count()
                + DB::table('vida_ministerio_calificacions')->where('congregacion_id', $congregacionId)->count(),
        ];

        return view('congregaciones.datos', compact('congregacion', 'resumen'));
    }

    public function destruirPropia(Request $request)
    {
        $this->soloSecretario();

        $congregacion = auth()->user()->congregacion;
        abort_if(!$congregacion, 404);

        $request->merge([
            'confirmacion' => mb_strtolower(trim((string) $request->input('confirmacion'))),
        ]);

        $request->validate([
            'confirmacion' => ['required', 'in:eliminar'],
        ], [
            'confirmacion.in' => 'Para confirmar, escribi eliminar.',
        ]);

        $nombre = $congregacion->nombre;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $congregacion->delete();

        return redirect()
            ->route('login')
            ->with('status', 'La congregacion "' . $nombre . '" y todos sus datos fueron eliminados definitivamente.');
    }
}
