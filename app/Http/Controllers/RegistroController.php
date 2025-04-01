<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use App\Models\Publicador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistroController extends Controller
{
    // Mostrar los registros agrupados por publicador
    public function index()
    {
        $publicadors = Publicador::with('registros')->orderBy('grupo')->get();
        return view('reg.index', compact('publicadors'));
    }

    // Mostrar la tarjeta S-21 de un publicador (informe)
    public function s21($id_publicador)
    {
        $publicador = Publicador::findOrFail($id_publicador);
        $registros = Registro::where('id_publicador', $id_publicador)
            ->orderBy('a_servicio', 'desc')
            ->orderByRaw("FIELD(mes, 'septiembre', 'octubre', 'noviembre', 'diciembre', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto')")
            ->get();

        return view('reg.s21', compact('publicador', 'registros'));
    }

    // Formulario para crear registro
    public function create($id)
    {
        $publicador = Publicador::findOrFail($id);
        $showAux = empty($publicador->precursor); // Solo mostrar auxiliar si NO es precursor regular
        return view('reg.form', compact('publicador', 'showAux'));
    }

    // Guardar nuevo registro
    public function store(Request $request, $id)
    {
        $request->validate([
            'a_servicio' => 'required|string|max:10',
            'mes' => 'required|string|max:20',
        ]);

        $data = $request->all();
        $data['id_publicador'] = $id;

        // Si no es auxiliar, la actividad es el checkbox
        if (!$request->has('aux') || $request->input('aux') == "") {
            $data['actividad'] = $request->has('actividad') ? 1 : 0;
            $data['horas'] = null; // No registran horas
        }

        Registro::create($data);

        return redirect()->route('reg.s21', $id)->with('success', 'Informe cargado correctamente.');
    }

    // Formulario para editar
    public function edit($id)
    {
        $registro = Registro::findOrFail($id);
        $publicador = $registro->publicador;
        $showAux = empty($publicador->precursor);
        return view('reg.form', compact('registro', 'publicador', 'showAux'));
    }

    // Actualizar informe
    public function update(Request $request, $id)
    {
        $registro = Registro::findOrFail($id);

        $request->validate([
            'a_servicio' => 'required|string|max:10',
            'mes' => 'required|string|max:20',
        ]);

        $data = $request->all();

        if (!$request->has('aux') || $request->input('aux') == "") {
            $data['actividad'] = $request->has('actividad') ? 1 : 0;
            $data['horas'] = null;
        }

        $registro->update($data);

        return redirect()->route('reg.s21', $registro->id_publicador)->with('success', 'Informe actualizado correctamente.');
    }

    // Eliminar informe
    public function destroy($id)
    {
        $registro = Registro::findOrFail($id);
        $registro->delete();

        return redirect()->back()->with('success', 'Registro eliminado correctamente.');
    }
}
