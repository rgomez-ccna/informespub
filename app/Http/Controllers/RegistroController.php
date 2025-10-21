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
    
        $publicador = Publicador::findOrFail($id);
    
        // Detectar tipo
        // Detectar tipo
        if ($publicador->precursor) {
            // 🔹 Precursor regular: siempre horas, nunca actividad
            $data['actividad'] = null;
            $data['aux'] = null; // nunca es auxiliar
        } elseif ($request->input('aux') === '(Auxiliar)') {
            // 🔹 Precursor auxiliar del mes
            $data['actividad'] = null;
        } else {
            // 🔹 Publicador común
            if ($request->has('actividad')) {
                $data['actividad'] = (int) $request->input('actividad');
            } else {
                $data['actividad'] = null; // si no marcó nada
            }
            $data['horas'] = null; // los publicadores comunes no informan horas
        }

// 🟢 Nota automática o limpieza según participación
if (isset($data['actividad'])) {
    if ($data['actividad'] === 0) {
        if (empty($data['notas'])) {
            $data['notas'] = 'No participó';
        }
    } elseif ($data['actividad'] === 1) {
        // 🔹 Si ahora marcó que predicó, limpiar nota automática anterior
        if ($registro->notas === 'No participó' || trim($data['notas']) === '') {
            $data['notas'] = null;
        }
    }
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
    $publicador = $registro->publicador;

    $request->validate([
        'a_servicio' => 'required|string|max:10',
        'mes' => 'required|string|max:20',
    ]);

    $data = $request->all();

    // Detectar si es auxiliar o no
  // Detectar tipo
if ($publicador->precursor) {
    // 🔹 Precursor regular: siempre horas, nunca actividad
    $data['actividad'] = null;
    $data['aux'] = null; // nunca es auxiliar
} elseif ($request->input('aux') === '(Auxiliar)') {
    // 🔹 Precursor auxiliar del mes
    $data['actividad'] = null;
} else {
    // 🔹 Publicador común
    if ($request->has('actividad')) {
        $data['actividad'] = (int) $request->input('actividad');
    } else {
        $data['actividad'] = null; // si no marcó nada
    }
    $data['horas'] = null; // los publicadores comunes no informan horas
}

// 🟢 Nota automática o limpieza según participación
if (isset($data['actividad'])) {
    if ($data['actividad'] === 0) {
        if (empty($data['notas'])) {
            $data['notas'] = 'No participó';
        }
    } elseif ($data['actividad'] === 1) {
        // 🔹 Si ahora marcó que predicó, limpiar nota automática anterior
        if ($registro->notas === 'No participó' || trim($data['notas']) === '') {
            $data['notas'] = null;
        }
    }
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



//     public function enviarInformes(Request $request)
// {
//     // Obtener publicadores con sus registros
//     $publicadores = Publicador::with(['registros' => function($q) use ($request) {
//         if ($request->filled('mes') && $request->filled('anho')) {
//             $q->where('mes', $request->mes)->where('a_servicio', $request->anho);
//         }
//     }])->orderBy('nombre')->get();

//     return view('reg.enviar-informes', compact('publicadores'));
// }

public function enviarInformes(Request $request)
{
    // Obtener publicadores con sus registros filtrados por mes y año
    $publicadores = Publicador::with(['registros' => function($q) use ($request) {
        if ($request->filled('mes') && $request->filled('anho')) {
            $q->where('mes', $request->mes)
              ->where('a_servicio', $request->anho)
              ->where(function($q2) {
                  // 🔹 Solo cuenta informes válidos
                  $q2->where('actividad', 1)          // Publicadores que predicaron
                     ->orWhere('aux', '(Auxiliar)')   // Auxiliares del mes
                     ->orWhereNotNull('horas');       // Precursores regulares
              });
        }
    }])
    ->orderBy('nombre')
    ->get();

    return view('reg.enviar-informes', compact('publicadores'));
}


}
