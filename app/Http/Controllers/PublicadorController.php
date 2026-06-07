<?php

namespace App\Http\Controllers;

use App\Models\Publicador;
use App\Models\Registro;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicadorController extends Controller
{

    private function congregacionActualId()
    {
        return auth()->check()
            ? auth()->user()->congregacion_id
            : session('free_congregacion_id');
    }

    // Secretario y colaborador administran datos de su congregación
    private function puedeGestionarDatos()
    {
        abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador']), 403);
    }

    // Consulta base protegida por congregación
    private function publicadoresQuery()
    {
        return Publicador::where('congregacion_id', $this->congregacionActualId());
    }

    // Buscar publicador sin permitir acceso a otra congregación
    private function buscarPublicadorSeguro($id)
    {
        return $this->publicadoresQuery()->findOrFail($id);
    }

    // Consulta base de registros protegida por congregación
    private function registrosQuery()
    {
        return Registro::where('congregacion_id', $this->congregacionActualId());
    }

    private function ordenRolGrupo($publicador): int
    {
        return match ($publicador->rol) {
            'Sup. de Grupo' => 1,
            'Sup. Auxiliar' => 2,
            default => 3,
        };
    }

    public function index(Request $request)
    {
        $this->puedeGestionarDatos();

        $nombre = $request->get('nombre');

        $publicadors = $this->publicadoresQuery()
            ->with('registros')
            ->where('nombre', 'like', '%' . $nombre . '%')
            ->get()
            ->sortBy([
                fn ($a, $b) => strnatcasecmp($a->grupo ?? '', $b->grupo ?? ''),
                fn ($a, $b) => $this->ordenRolGrupo($a) <=> $this->ordenRolGrupo($b),
                fn ($a, $b) => strcasecmp($a->nombre, $b->nombre),
            ]);

        $lastReportStatuses = [];
        $publisherActivityStatuses = [];

        foreach ($publicadors as $pub) {
            $lastReportStatuses[$pub->id] = $this->lastReportStatus($pub->id);
            $publisherActivityStatuses[$pub->id] = $this->publisherActivityStatus($pub->id);
        }

        $gruposDisponibles = $this->publicadoresQuery()
            ->whereNotNull('grupo')
            ->where('grupo', '!=', '')
            ->distinct()
            ->orderBy('grupo')
            ->pluck('grupo');

        return view('pub.index', compact(
            'publicadors',
            'lastReportStatuses',
            'publisherActivityStatuses',
            'gruposDisponibles'
        ));
    }

    public function create()
    {
        $this->puedeGestionarDatos();

        return view('pub.form');
    }

    public function show($id)
    {
        $this->puedeGestionarDatos();

        $publicador = $this->buscarPublicadorSeguro($id);

        return view('pub.show', compact('publicador'));
    }

    public function edit($id)
    {
        $this->puedeGestionarDatos();

        $publicador = $this->buscarPublicadorSeguro($id);

        return view('pub.form', compact('publicador'));
    }

    public function store(Request $request)
    {
        $this->puedeGestionarDatos();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'grupo' => 'nullable|string|max:100',
        ]);

        $data = $request->all();
        $data['congregacion_id'] = auth()->user()->congregacion_id;

        $camposCheckbox = ['hombre', 'mujer', 'oo', 'ungido', 'anciano', 'sv', 'precursor'];

        foreach ($camposCheckbox as $campo) {
            $data[$campo] = $request->has($campo) ? 1 : 0;
        }

        Publicador::create($data);

        return redirect()->route('pub.index')->with('success', 'Publicador creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $this->puedeGestionarDatos();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'grupo' => 'nullable|string|max:100',
        ]);

        $publicador = $this->buscarPublicadorSeguro($id);

        $data = $request->all();
        $data['congregacion_id'] = auth()->user()->congregacion_id;

        $camposCheckbox = ['hombre', 'mujer', 'oo', 'ungido', 'anciano', 'sv', 'precursor'];

        foreach ($camposCheckbox as $campo) {
            $data[$campo] = $request->has($campo) ? 1 : 0;
        }

        $publicador->update($data);

        return redirect()->route('pub.index')->with('success', 'Publicador actualizado correctamente.');
    }

    public function destroy($id)
    {
        $this->puedeGestionarDatos();

        $publicador = $this->buscarPublicadorSeguro($id);
        $publicador->delete();

        return redirect()->route('pub.index')->with('success', 'Publicador eliminado correctamente.');
    }

    // --------------------
    // Estado de INFORME
    private function lastReportStatus($publicadorId)
    {
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $lastReport = $this->registrosQuery()
            ->where('id_publicador', $publicadorId)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->first();

        return $lastReport ? 'success' : 'danger';
    }

    // --------------------
    // Estado de ACTIVIDAD
    private function publisherActivityStatus($publicadorId)
    {
        $reports = $this->registrosQuery()
            ->where('id_publicador', $publicadorId)
            ->whereBetween('created_at', [now()->subMonths(7), now()])
            ->orderBy('created_at')
            ->get();

        if ($reports->isEmpty()) return 'inactivo';

        $consecutive = 1;
        $prevDate = Carbon::parse($reports->first()->created_at);

        for ($i = 1; $i < $reports->count(); $i++) {
            $date = Carbon::parse($reports[$i]->created_at);
            $diff = $prevDate->diffInMonths($date);

            if ($diff == 1 || ($prevDate->month == 12 && $date->month == 1)) {
                $consecutive++;
            } else {
                $consecutive = 1;
            }

            if ($consecutive == 6) return 'activo';

            $prevDate = $date;
        }

        return ($prevDate->diffInMonths(now()) < 6) ? 'irregular' : 'inactivo';
    }

    /// RESUMEN
    // Vista tipo listado agrupado por grupo
    public function listado()
    {
        $this->puedeGestionarDatos();

        $publicadores = $this->publicadoresQuery()
            ->get()
            ->sortBy([
                fn ($a, $b) => strnatcasecmp($a->grupo ?? '', $b->grupo ?? ''),
                fn ($a, $b) => $this->ordenRolGrupo($a) <=> $this->ordenRolGrupo($b),
                fn ($a, $b) => strcasecmp($a->nombre, $b->nombre),
            ])
            ->groupBy('grupo');

        $linkActual = null;

        if (session('free_token')) {
            $linkActual = \App\Models\LinkAcceso::where('token', session('free_token'))->first();
        }

        return view('pub.listado', compact('publicadores', 'linkActual'));
    }

    // Vista tipo tarjeta S-21 (detalles)
    public function s21($id)
    {
        $this->puedeGestionarDatos();

        $publicador = $this->buscarPublicadorSeguro($id);

        $registros = $publicador->registros()
            ->where('congregacion_id', auth()->user()->congregacion_id)
            ->orderBy('a_servicio', 'desc')
            ->orderByRaw("FIELD(mes, 'Septiembre','Octubre','Noviembre','Diciembre','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto')")
            ->get()
            ->groupBy('a_servicio');

        return view('pub.s21', compact('publicador', 'registros'));
    }

    // Buscar publicadores por nombre
    public function buscar(Request $request)
    {
        $this->puedeGestionarDatos();

        $termino = $request->get('q');

        $publicadores = $this->publicadoresQuery()
            ->where('nombre', 'like', '%' . $termino . '%')
            ->orderBy('nombre')
            ->limit(10)
            ->pluck('nombre');

        return response()->json($publicadores);
    }

    // FUNCIONES MASIVAS
    public function renombrarGrupo(Request $request)
    {
        $this->puedeGestionarDatos();

        $request->validate([
            'grupo_actual' => 'required|string|max:100',
            'grupo_nuevo'  => 'required|string|max:100',
        ]);

        $this->publicadoresQuery()
            ->where('grupo', $request->grupo_actual)
            ->update(['grupo' => $request->grupo_nuevo]);

        return redirect()->route('pub.index')->with('success', 'Grupo renombrado correctamente.');
    }

    public function fusionarGrupo(Request $request)
    {
        $this->puedeGestionarDatos();

        $request->validate([
            'grupo_actual'  => 'required|string|max:100',
            'grupo_destino' => 'required|string|max:100',
        ]);

        if ($request->grupo_actual === $request->grupo_destino) {
            return redirect()->route('pub.index')->with('success', 'No hubo cambios.');
        }

        $this->publicadoresQuery()
            ->where('grupo', $request->grupo_actual)
            ->update(['grupo' => $request->grupo_destino]);

        return redirect()->route('pub.index')->with('success', 'Grupo fusionado correctamente.');
    }

    public function cambiarGrupoMasivo(Request $request)
    {
        $this->puedeGestionarDatos();

        $request->validate([
            'publicadores'   => 'required|array|min:1',
            'publicadores.*' => 'exists:publicadors,id',
            'grupo_destino'  => 'required|string|max:100',
        ]);

        $this->publicadoresQuery()
            ->whereIn('id', $request->publicadores)
            ->update(['grupo' => $request->grupo_destino]);

        return redirect()->route('pub.index')
            ->with('success', 'Publicadores movidos correctamente.');
    }

    // Totales por mes: precursores regulares y auxiliares
    public function s21Totales()
    {
        $this->puedeGestionarDatos();

        $ordenMeses = [
            'Septiembre','Octubre','Noviembre','Diciembre',
            'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto'
        ];

        $registros = $this->registrosQuery()
            ->orderBy('a_servicio', 'desc')
            ->orderByRaw("FIELD(mes, 'Septiembre','Octubre','Noviembre','Diciembre','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto')")
            ->get();

        // Precursor regular:
        // Se detecta por el informe mensual, no por el estado actual del publicador.
        // En tu sistema: regular = tiene horas, no es auxiliar, no usa actividad.
        $precursoresRegulares = $registros
            ->filter(function ($r) {
                return is_null($r->aux)
                    && is_null($r->actividad)
                    && !is_null($r->horas);
            })
            ->groupBy('a_servicio')
            ->map(function ($registrosAnio) use ($ordenMeses) {
                return collect($ordenMeses)->map(function ($mes) use ($registrosAnio) {
                    $items = $registrosAnio->where('mes', $mes);

                    return (object) [
                        'mes' => $mes,
                        'cursos' => $items->sum('cursos'),
                        'horas' => $items->sum('horas'),
                        'notas_cantidad' => $items->pluck('id_publicador')->unique()->count(),
                    ];
                });
            });

        // Precursor auxiliar:
        // Se detecta por el campo aux del informe mensual.
        $precursoresAuxiliares = $registros
            ->filter(function ($r) {
                return $r->aux === '(Auxiliar)';
            })
            ->groupBy('a_servicio')
            ->map(function ($registrosAnio) use ($ordenMeses) {
                return collect($ordenMeses)->map(function ($mes) use ($registrosAnio) {
                    $items = $registrosAnio->where('mes', $mes);

                    return (object) [
                        'mes' => $mes,
                        'cursos' => $items->sum('cursos'),
                        'horas' => $items->sum('horas'),
                        'notas_cantidad' => $items->pluck('id_publicador')->unique()->count(),
                    ];
                });
            });

        // Publicadores:
        // Se cuentan solo los que informaron actividad ese mes.
        // No incluye auxiliares ni regulares, porque esos ya están separados.
        $publicadoresActivos = $registros
            ->filter(function ($r) {
                return (int) $r->actividad === 1
                    && is_null($r->aux)
                    && is_null($r->horas);
            })
            ->groupBy('a_servicio')
            ->map(function ($registrosAnio) use ($ordenMeses) {
                return collect($ordenMeses)->map(function ($mes) use ($registrosAnio) {
                    $items = $registrosAnio->where('mes', $mes);

                    return (object) [
                        'mes' => $mes,
                        'cursos' => $items->sum('cursos'),
                        'cantidad' => $items->pluck('id_publicador')->unique()->count(),
                    ];
                });
            });

        return view('pub.s21_totales', compact(
            'precursoresRegulares',
            'precursoresAuxiliares',
            'publicadoresActivos',
        ));
    }
}