<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use App\Models\Publicador;
use Illuminate\Http\Request;

class RegistroController extends Controller
{

private function congregacionActualId()
{
    return auth()->check()
        ? auth()->user()->congregacion_id
        : session('free_congregacion_id');
}

    // Secretario y colaborador administran registros de su congregación
    private function puedeGestionarDatos()
    {
        abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador']), 403);
    }

    // Consulta base de publicadores protegida por congregación
    private function publicadoresQuery()
    {
        return Publicador::where('congregacion_id', $this->congregacionActualId());
    }

    // Consulta base de registros protegida por congregación
    private function registrosQuery()
    {
        return Registro::where('congregacion_id', $this->congregacionActualId());
    }

    // Buscar publicador sin permitir acceso a otra congregación
    private function buscarPublicadorSeguro($id)
    {
        return $this->publicadoresQuery()->findOrFail($id);
    }

    // Buscar registro sin permitir acceso a otra congregación
    private function buscarRegistroSeguro($id)
    {
        return $this->registrosQuery()->findOrFail($id);
    }

    // Mostrar los registros agrupados por publicador
    public function index()
    {
        $this->puedeGestionarDatos();

        $publicadors = $this->publicadoresQuery()
            ->with('registros')
            ->orderBy('grupo')
            ->get();

        return view('reg.index', compact('publicadors'));
    }

    // Mostrar la tarjeta S-21 de un publicador
    public function s21($id_publicador)
    {
        $this->puedeGestionarDatos();

        $publicador = $this->buscarPublicadorSeguro($id_publicador);

        $registros = $this->registrosQuery()
            ->where('id_publicador', $publicador->id)
            ->orderBy('a_servicio', 'desc')
            ->orderByRaw("FIELD(mes, 'septiembre', 'octubre', 'noviembre', 'diciembre', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto')")
            ->get();

        return view('reg.s21', compact('publicador', 'registros'));
    }

    // Formulario para crear registro
    public function create($id)
    {
        $this->puedeGestionarDatos();

        $publicador = $this->buscarPublicadorSeguro($id);

        // Solo mostrar auxiliar si NO es precursor regular
        $showAux = empty($publicador->precursor);

        return view('reg.form', compact('publicador', 'showAux'));
    }

    // Guardar nuevo registro
    public function store(Request $request, $id)
    {
        $this->puedeGestionarDatos();

        $publicador = $this->buscarPublicadorSeguro($id);

        $request->validate([
            'a_servicio' => 'required|string|max:10',
            'mes' => 'required|string|max:20',
        ]);

        $data = $request->all();
        $data['id_publicador'] = $publicador->id;
        $data['congregacion_id'] = auth()->user()->congregacion_id;

        // Detectar tipo
        if ($publicador->precursor) {
            // Precursor regular: siempre horas, nunca actividad
            $data['actividad'] = null;
            $data['aux'] = null;
        } elseif ($request->input('aux') === '(Auxiliar)') {
            // Precursor auxiliar del mes
            $data['actividad'] = null;
        } else {
            // Publicador común
            if ($request->has('actividad')) {
                $data['actividad'] = (int) $request->input('actividad');
            } else {
                $data['actividad'] = null;
            }

            // Los publicadores comunes no informan horas
            $data['horas'] = null;
        }

        // Nota automática o limpieza según participación
        if (isset($data['actividad'])) {
            if ($data['actividad'] === 0) {
                if (empty($data['notas'])) {
                    $data['notas'] = 'No participó';
                }
            } elseif ($data['actividad'] === 1) {
                if (empty($data['notas']) || $data['notas'] === 'No participó') {
                    $data['notas'] = null;
                }
            }
        }

        Registro::create($data);

        return redirect()->route('reg.s21', $publicador->id)
            ->with('success', 'Informe cargado correctamente.');
    }

    // Formulario para editar
    public function edit($id)
    {
        $this->puedeGestionarDatos();

        $registro = $this->buscarRegistroSeguro($id);
        $publicador = $this->buscarPublicadorSeguro($registro->id_publicador);

        $showAux = empty($publicador->precursor);

        return view('reg.form', compact('registro', 'publicador', 'showAux'));
    }

    // Actualizar informe
    public function update(Request $request, $id)
    {
        $this->puedeGestionarDatos();

        $registro = $this->buscarRegistroSeguro($id);
        $publicador = $this->buscarPublicadorSeguro($registro->id_publicador);

        $request->validate([
            'a_servicio' => 'required|string|max:10',
            'mes' => 'required|string|max:20',
        ]);

        $data = $request->all();
        $data['congregacion_id'] = auth()->user()->congregacion_id;
        $data['id_publicador'] = $publicador->id;

        // Detectar tipo
        if ($publicador->precursor) {
            // Precursor regular: siempre horas, nunca actividad
            $data['actividad'] = null;
            $data['aux'] = null;
        } elseif ($request->input('aux') === '(Auxiliar)') {
            // Precursor auxiliar del mes
            $data['actividad'] = null;
        } else {
            // Publicador común
            if ($request->has('actividad')) {
                $data['actividad'] = (int) $request->input('actividad');
            } else {
                $data['actividad'] = null;
            }

            // Los publicadores comunes no informan horas
            $data['horas'] = null;
        }

        // Nota automática o limpieza según participación
        if (isset($data['actividad'])) {
            if ($data['actividad'] === 0) {
                if (empty($data['notas'])) {
                    $data['notas'] = 'No participó';
                }
            } elseif ($data['actividad'] === 1) {
                if ($registro->notas === 'No participó' || trim($data['notas'] ?? '') === '') {
                    $data['notas'] = null;
                }
            }
        }

        $registro->update($data);

        return redirect()->route('reg.s21', $registro->id_publicador)
            ->with('success', 'Informe actualizado correctamente.');
    }

    // Eliminar informe
    public function destroy($id)
    {
        $this->puedeGestionarDatos();

        $registro = $this->buscarRegistroSeguro($id);
        $registro->delete();

        return redirect()->back()->with('success', 'Registro eliminado correctamente.');
    }

    public function enviarInformes(Request $request)
    {
        $this->puedeGestionarDatos();

        // Obtener publicadores de la congregación actual con sus registros filtrados por mes y año
        $publicadores = $this->publicadoresQuery()
            ->with(['registros' => function ($q) use ($request) {
                $q->where('congregacion_id', auth()->user()->congregacion_id);

                if ($request->filled('mes') && $request->filled('anho')) {
                    $q->where('mes', $request->mes)
                        ->where('a_servicio', $request->anho)
                        ->where(function ($q2) {
                            // Solo cuenta informes válidos
                            $q2->where('actividad', 1)
                                ->orWhere('aux', '(Auxiliar)')
                                ->orWhereNotNull('horas');
                        });
                }
            }])
            ->orderBy('nombre')
            ->get();

        return view('reg.enviar-informes', compact('publicadores'));
    }
}