<?php

namespace App\Http\Controllers;

use App\Models\Publicador;
use App\Models\VidaMinisterio;
use App\Models\VidaMinisterioAsignacion;
use App\Models\VidaMinisterioCalificacion;
use App\Models\VidaMinisterioParte;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\ImportarWolVidaMinisterioService;
use Illuminate\Http\Client\RequestException;

class VidaMinisterioController extends Controller
{
    private function puedeVer(): void
    {
        abort_if(!auth()->check(), 403);
        abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador', 'tablero']), 403);
    }

    private function puedeGestionar(): void
    {
        abort_if(!auth()->check(), 403);
        abort_if(!in_array(auth()->user()->role, ['secretario', 'colaborador']), 403);
    }

    private function congregacionId(): int
    {
        return auth()->user()->congregacion_id;
    }

    private function programasQuery()
    {
        return VidaMinisterio::where('congregacion_id', $this->congregacionId());
    }

    private function autorizarPrograma(VidaMinisterio $programa): void
    {
        abort_if($programa->congregacion_id !== $this->congregacionId(), 404);
    }

    private function tiposAsignacion(): array
    {
        return [
            'presidente',
            'ayudante_auditorio',
            'consejero_auxiliar',
            'ayudante_auxiliar',
            'oracion',
            'tesoro',
            'perlas',
            'lectura_biblia',
            'maestro_estudiante',
            'maestro_ayudante',
            'vida_cristiana',
            'estudio_conductor',
            'estudio_lector',
        ];
    }

    private function seccionesPermitidas(): array
    {
        return [
            'encabezado',
            'tesoros',
            'maestros',
            'vida',
            'final',
        ];
    }

    

    public function index(Request $request)
    {
        $this->puedeVer();

        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->copy()->addMonths(2)->endOfMonth()->toDateString());

        $programas = $this->programasQuery()
            ->with(['partes.asignaciones.publicador'])
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')
            ->get();

        return view('vida_ministerio.index', compact('programas', 'desde', 'hasta'));
    }

public function importarWol(Request $request, ImportarWolVidaMinisterioService $importador)
{
    $this->puedeGestionar();

    $data = $request->validate([
        'anio' => ['required', 'integer', 'min:2026', 'max:2035'],
        'periodo' => ['required', 'string', 'in:enero,marzo,mayo,julio,septiembre,noviembre'],
    ]);

    try {
        $resultado = $importador->importarPeriodo(
            anio: (int) $data['anio'],
            periodo: $data['periodo'],
            congregacionId: $this->congregacionId(),
            userId: auth()->id()
        );

        foreach ($resultado['programa_ids'] as $programaId) {
            $programa = $this->programasQuery()
                ->whereKey($programaId)
                ->first();

            if (!$programa) {
                continue;
            }

            $this->recalcularNumeros($programa);
            $this->calcularHorarios($programa);
        }

        $rango = $this->rangoVistaPeriodoJw((int) $data['anio'], $data['periodo']);

        return redirect()
            ->route('vida-ministerio.index', [
                'desde' => $rango['desde'],
                'hasta' => $rango['hasta'],
            ])
            ->withInput($request->only('anio', 'periodo'))
            ->with(
                'success',
                'Importación completada. Semanas creadas: ' .
                $resultado['creadas'] .
                '. Semanas actualizadas: ' .
                $resultado['existentes'] .
                '. Ahora estás viendo ' . $rango['label'] . '.'
            );

    } catch (\Illuminate\Http\Client\RequestException $e) {
        report($e);

        $status = $e->response?->status();

        $mensaje = match ($status) {
            404 => 'Ese período todavía no está publicado en JW.org. Probá con un período anterior o esperá a que esté disponible.',
            403 => 'JW.org rechazó la solicitud. Intentá nuevamente más tarde.',
            429 => 'JW.org recibió demasiadas solicitudes. Esperá unos minutos e intentá nuevamente.',
            default => 'No se pudo importar desde JW.org. Revisá conexión o intentá nuevamente.',
        };

        return back()
            ->withInput()
            ->withErrors([
                'importar_wol' => $mensaje,
            ]);

    } catch (\Throwable $e) {
        report($e);

        return back()
            ->withInput()
            ->withErrors([
                'importar_wol' => app()->environment('local')
                    ? $e->getMessage()
                    : 'No se pudo importar desde JW.org. Revisá conexión o intentá nuevamente.',
            ]);
    }
}

private function rangoVistaPeriodoJw(int $anio, string $periodo): array
{
    $periodos = [
        'enero' => [
            'label' => 'Enero-Febrero ' . $anio,
            'mes_inicio' => 1,
            'mes_fin' => 2,
        ],
        'marzo' => [
            'label' => 'Marzo-Abril ' . $anio,
            'mes_inicio' => 3,
            'mes_fin' => 4,
        ],
        'mayo' => [
            'label' => 'Mayo-Junio ' . $anio,
            'mes_inicio' => 5,
            'mes_fin' => 6,
        ],
        'julio' => [
            'label' => 'Julio-Agosto ' . $anio,
            'mes_inicio' => 7,
            'mes_fin' => 8,
        ],
        'septiembre' => [
            'label' => 'Septiembre-Octubre ' . $anio,
            'mes_inicio' => 9,
            'mes_fin' => 10,
        ],
        'noviembre' => [
            'label' => 'Noviembre-Diciembre ' . $anio,
            'mes_inicio' => 11,
            'mes_fin' => 12,
        ],
    ];

    $config = $periodos[$periodo];

    return [
        'label' => $config['label'],
        'desde' => \Carbon\Carbon::create($anio, $config['mes_inicio'], 1)
            ->startOfMonth()
            ->toDateString(),
        'hasta' => \Carbon\Carbon::create($anio, $config['mes_fin'], 1)
            ->endOfMonth()
            ->toDateString(),
    ];
}


public function create(Request $request)
{
    $this->puedeGestionar();

    $programa = null;
    $publicadoresPorTipo = $this->publicadoresPorTipo();

    $fechaReferencia = $request->input('fecha', now()->toDateString());
    $historial = $this->historialAnteriorHasta($fechaReferencia);

    return view('vida_ministerio.create', compact(
        'programa',
        'publicadoresPorTipo',
        'historial',
        'fechaReferencia'
    ));
}

    

   public function store(Request $request)
{
    $this->puedeGestionar();

    $data = $request->validate([
        'fecha' => [
            'required',
            'date',
            Rule::unique('vida_ministerios', 'fecha')
                ->where(fn ($q) => $q->where('congregacion_id', $this->congregacionId())),
        ],
        'hora_inicio' => ['nullable', 'date_format:H:i'],
        'lectura_semanal' => ['nullable', 'string', 'max:255'],
        'nombre_sala_auxiliar' => ['nullable', 'string', 'max:255'],
        'cancion_inicio' => ['nullable', 'string', 'max:50'],
        'cancion_medio' => ['nullable', 'string', 'max:50'],
        'cancion_final' => ['nullable', 'string', 'max:50'],
        'estado' => ['nullable', 'in:normal,aviso'],
        'observaciones' => ['nullable', 'string'],

        'partes_base' => ['nullable', 'array'],
        'partes_base.*.titulo' => ['nullable', 'string'],
        'partes_base.*.duracion_minutos' => ['nullable', 'integer', 'min:0', 'max:300'],
        'partes_base.*.tipo_asignacion' => ['nullable', Rule::in($this->tiposAsignacion())],
        'partes_base.*.seccion' => ['nullable', Rule::in($this->seccionesPermitidas())],
        'partes_base.*.slots' => ['nullable', 'array'],
        'partes_base.*.slots.*.publicador_id' => ['nullable', 'integer'],
        'partes_base.*.slots.*.rol' => ['nullable', 'string', 'max:50'],
        'partes_base.*.slots.*.sala' => ['nullable', 'string', 'max:50'],
        'partes_base.*.slots.*.tipo_asignacion' => ['nullable', Rule::in($this->tiposAsignacion())],

        'nuevas_partes' => ['nullable', 'array'],
        'nuevas_partes.*.seccion' => ['nullable', Rule::in(['maestros', 'vida'])],
        'nuevas_partes.*.tipo_asignacion' => ['nullable', Rule::in(['maestro_estudiante', 'vida_cristiana'])],
        'nuevas_partes.*.titulo' => ['nullable', 'string'],
        'nuevas_partes.*.duracion_minutos' => ['nullable', 'integer', 'min:0', 'max:300'],
        'nuevas_partes.*.slots' => ['nullable', 'array'],
        'nuevas_partes.*.slots.*.publicador_id' => ['nullable', 'integer'],
        'nuevas_partes.*.slots.*.rol' => ['nullable', 'string', 'max:50'],
        'nuevas_partes.*.slots.*.sala' => ['nullable', 'string', 'max:50'],
        'nuevas_partes.*.slots.*.tipo_asignacion' => ['nullable', Rule::in($this->tiposAsignacion())],
    ]);

    $programa = DB::transaction(function () use ($data, $request) {
        $programa = VidaMinisterio::create([
            'congregacion_id' => $this->congregacionId(),
            'user_id' => auth()->id(),
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'] ?? null,
            'lectura_semanal' => $data['lectura_semanal'] ?? null,
            'nombre_sala_auxiliar' => $data['nombre_sala_auxiliar'] ?? null,
            'cancion_inicio' => $data['cancion_inicio'] ?? null,
            'cancion_medio' => $data['cancion_medio'] ?? null,
            'cancion_final' => $data['cancion_final'] ?? null,
            'estado' => $data['estado'] ?? 'normal',
            'observaciones' => $data['observaciones'] ?? null,
        ]);

        $partesBaseCreadas = $this->crearPartesBaseDesdeRequest($programa, $request);
        $nuevasPartesCreadas = $this->guardarPartes($programa, $request);

        $this->recalcularNumeros($programa);
        $this->calcularHorarios($programa);

        $this->guardarAsignaciones(
            $programa,
            $request,
            $nuevasPartesCreadas,
            $partesBaseCreadas
        );

        return $programa;
    });

    return redirect()
        ->route('vida-ministerio.edit', $programa)
        ->with('success', 'Programa creado correctamente.');
}





public function edit(VidaMinisterio $programa)
{
    $this->puedeGestionar();
    $this->autorizarPrograma($programa);

    $programa->load([
        'partes.asignaciones.publicador',
        'asignaciones.publicador',
    ]);

    $publicadoresPorTipo = $this->publicadoresPorTipo();
    $asignacionesActuales = $programa->asignaciones->groupBy('vida_ministerio_parte_id');
    $fechaReferencia = $programa->fecha->toDateString();
    $historial = $this->historialAnteriorHasta($fechaReferencia);

    return view('vida_ministerio.edit', compact(
        'programa',
        'publicadoresPorTipo',
        'asignacionesActuales',
        'historial',
        'fechaReferencia'
    ));
}

    public function update(Request $request, VidaMinisterio $programa)
{
    $this->puedeGestionar();
    $this->autorizarPrograma($programa);

    $data = $request->validate([
        'fecha' => [
            'required',
            'date',
            Rule::unique('vida_ministerios', 'fecha')
                ->where(fn ($q) => $q->where('congregacion_id', $this->congregacionId()))
                ->ignore($programa->id),
        ],

        'hora_inicio' => ['nullable', 'date_format:H:i'],
        'lectura_semanal' => ['nullable', 'string', 'max:255'],
        'nombre_sala_auxiliar' => ['nullable', 'string', 'max:255'],
        'cancion_inicio' => ['nullable', 'string', 'max:50'],
        'cancion_medio' => ['nullable', 'string', 'max:50'],
        'cancion_final' => ['nullable', 'string', 'max:50'],
        'estado' => ['nullable', 'in:normal,aviso'],
        'observaciones' => ['nullable', 'string'],

        /*
        |--------------------------------------------------------------------------
        | Partes base
        |--------------------------------------------------------------------------
        | Se usan sobre todo cuando el formulario compartido crea partes base
        | faltantes. En edición normalmente ya existen, pero lo dejamos soportado.
        */

        'partes_base' => ['nullable', 'array'],
        'partes_base.*.titulo' => ['nullable', 'string'],
        'partes_base.*.duracion_minutos' => ['nullable', 'integer', 'min:0', 'max:300'],
        'partes_base.*.tipo_asignacion' => ['nullable', Rule::in($this->tiposAsignacion())],
        'partes_base.*.seccion' => ['nullable', Rule::in($this->seccionesPermitidas())],

        'partes_base.*.slots' => ['nullable', 'array'],
        'partes_base.*.slots.*.publicador_id' => ['nullable', 'integer'],
        'partes_base.*.slots.*.rol' => ['nullable', 'string', 'max:50'],
        'partes_base.*.slots.*.sala' => ['nullable', 'string', 'max:50'],
        'partes_base.*.slots.*.tipo_asignacion' => ['nullable', Rule::in($this->tiposAsignacion())],

        /*
        |--------------------------------------------------------------------------
        | Partes existentes
        |--------------------------------------------------------------------------
        */

        'partes' => ['nullable', 'array'],
        'partes.*.titulo' => ['nullable', 'string'],
        'partes.*.duracion_minutos' => ['nullable', 'integer', 'min:0', 'max:300'],
        'partes.*.tipo_asignacion' => ['nullable', Rule::in($this->tiposAsignacion())],
        'partes.*.seccion' => ['nullable', Rule::in($this->seccionesPermitidas())],
        'partes.*.eliminar' => ['nullable'],

        /*
        |--------------------------------------------------------------------------
        | Nuevas partes agregadas desde la tabla
        |--------------------------------------------------------------------------
        */

        'nuevas_partes' => ['nullable', 'array'],
        'nuevas_partes.*.seccion' => ['nullable', Rule::in(['maestros', 'vida'])],
        'nuevas_partes.*.tipo_asignacion' => ['nullable', Rule::in(['maestro_estudiante', 'vida_cristiana'])],
        'nuevas_partes.*.titulo' => ['nullable', 'string'],
        'nuevas_partes.*.duracion_minutos' => ['nullable', 'integer', 'min:0', 'max:300'],

        'nuevas_partes.*.slots' => ['nullable', 'array'],
        'nuevas_partes.*.slots.*.publicador_id' => ['nullable', 'integer'],
        'nuevas_partes.*.slots.*.rol' => ['nullable', 'string', 'max:50'],
        'nuevas_partes.*.slots.*.sala' => ['nullable', 'string', 'max:50'],
        'nuevas_partes.*.slots.*.tipo_asignacion' => ['nullable', Rule::in($this->tiposAsignacion())],

        /*
        |--------------------------------------------------------------------------
        | Asignaciones de partes existentes
        |--------------------------------------------------------------------------
        */

        'asignaciones' => ['nullable', 'array'],
        'asignaciones.*' => ['nullable', 'array'],
        'asignaciones.*.*.publicador_id' => ['nullable', 'integer'],
        'asignaciones.*.*.rol' => ['nullable', 'string', 'max:50'],
        'asignaciones.*.*.sala' => ['nullable', 'string', 'max:50'],
        'asignaciones.*.*.tipo_asignacion' => ['nullable', Rule::in($this->tiposAsignacion())],
    ], [], [
        'fecha' => 'fecha',
        'hora_inicio' => 'hora de inicio',
        'lectura_semanal' => 'lectura semanal',
        'nombre_sala_auxiliar' => 'nombre de sala auxiliar',
        'cancion_inicio' => 'canción inicial',
        'cancion_medio' => 'canción intermedia',
        'cancion_final' => 'canción final',
        'estado' => 'tipo de semana',
        'observaciones' => 'observaciones',
    ]);

    DB::transaction(function () use ($programa, $data, $request) {
        $programa->update([
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'] ?? null,
            'lectura_semanal' => $data['lectura_semanal'] ?? null,
            'nombre_sala_auxiliar' => $data['nombre_sala_auxiliar'] ?? null,
            'cancion_inicio' => $data['cancion_inicio'] ?? null,
            'cancion_medio' => $data['cancion_medio'] ?? null,
            'cancion_final' => $data['cancion_final'] ?? null,
            'estado' => $data['estado'] ?? $programa->estado,
            'observaciones' => $data['observaciones'] ?? null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 1. Crear partes base faltantes si vienen desde el formulario
        |--------------------------------------------------------------------------
        */

        $partesBaseCreadas = $this->crearPartesBaseDesdeRequest($programa, $request);

        /*
        |--------------------------------------------------------------------------
        | 2. Actualizar partes existentes y crear partes nuevas
        |--------------------------------------------------------------------------
        */

        $nuevasPartesCreadas = $this->guardarPartes($programa, $request);

        /*
        |--------------------------------------------------------------------------
        | 3. Recalcular número y horario en el orden real
        |--------------------------------------------------------------------------
        */

        $this->recalcularNumeros($programa);
        $this->calcularHorarios($programa);

        /*
        |--------------------------------------------------------------------------
        | 4. Guardar asignaciones de existentes, base nuevas y nuevas partes
        |--------------------------------------------------------------------------
        */

        $this->guardarAsignaciones(
            $programa,
            $request,
            $nuevasPartesCreadas,
            $partesBaseCreadas
        );
    });

    return redirect()
        ->route('vida-ministerio.edit', $programa)
        ->with('success', 'Programa actualizado correctamente.');
}

    public function destroy(VidaMinisterio $programa)
    {
        $this->puedeGestionar();
        $this->autorizarPrograma($programa);

        $programa->delete();

        return redirect()
            ->route('vida-ministerio.index')
            ->with('success', 'Programa eliminado correctamente.');
    }

   public function pdf(VidaMinisterio $programa)
{
    $this->puedeVer();
    $this->autorizarPrograma($programa);

    $programa->load(['partes.asignaciones.publicador', 'congregacion']);

    $programas = collect([$programa]);
    $tituloPeriodo = $this->tituloPeriodoPdf($programas);

    $pdf = Pdf::loadView('vida_ministerio.pdf', compact('programas', 'tituloPeriodo'))
        ->setPaper('a4', 'portrait');

    return $pdf->stream('vida-ministerio-' . $programa->fecha->format('Y-m-d') . '.pdf');
}

    public function pdfSeleccionados(Request $request)
{
    $this->puedeVer();

    $ids = collect($request->input('programas', []))
        ->map(fn ($id) => (int) $id)
        ->filter()
        ->unique()
        ->values();

    if ($ids->isEmpty()) {
        return redirect()
            ->route('vida-ministerio.index')
            ->with('success', 'Seleccioná al menos una semana para imprimir.');
    }

    $programas = $this->programasQuery()
        ->with(['partes.asignaciones.publicador', 'congregacion'])
        ->whereIn('id', $ids)
        ->orderBy('fecha')
        ->get();

    if ($programas->isEmpty()) {
        return redirect()
            ->route('vida-ministerio.index')
            ->with('success', 'No se encontraron semanas para imprimir.');
    }

    $tituloPeriodo = $this->tituloPeriodoPdf($programas);

    $pdf = Pdf::loadView('vida_ministerio.pdf', compact('programas', 'tituloPeriodo'))
        ->setPaper('a4', 'portrait');

    return $pdf->stream('vida-ministerio-seleccionadas.pdf');
}


    private function crearPartesBase(VidaMinisterio $programa): void
    {
        $partes = [
            ['seccion' => 'encabezado', 'tipo_asignacion' => 'presidente', 'numero' => null, 'titulo' => 'Presidente', 'duracion_minutos' => null, 'orden' => 10],
            ['seccion' => 'encabezado', 'tipo_asignacion' => 'ayudante_auditorio', 'numero' => null, 'titulo' => 'Ayudante auditorio principal', 'duracion_minutos' => null, 'orden' => 20],
            ['seccion' => 'encabezado', 'tipo_asignacion' => 'consejero_auxiliar', 'numero' => null, 'titulo' => 'Consejero sala auxiliar', 'duracion_minutos' => null, 'orden' => 30],
            ['seccion' => 'encabezado', 'tipo_asignacion' => 'ayudante_auxiliar', 'numero' => null, 'titulo' => 'Ayudante sala auxiliar', 'duracion_minutos' => null, 'orden' => 40],
            ['seccion' => 'encabezado', 'tipo_asignacion' => 'oracion', 'numero' => null, 'titulo' => 'Oración inicial', 'duracion_minutos' => null, 'orden' => 50],

            ['seccion' => 'tesoros', 'tipo_asignacion' => 'tesoro', 'numero' => null, 'titulo' => null, 'duracion_minutos' => 10, 'orden' => 100],
            ['seccion' => 'tesoros', 'tipo_asignacion' => 'perlas', 'numero' => null, 'titulo' => 'Busquemos perlas escondidas', 'duracion_minutos' => 10, 'orden' => 110],
            ['seccion' => 'tesoros', 'tipo_asignacion' => 'lectura_biblia', 'numero' => null, 'titulo' => 'Lectura de la Biblia', 'duracion_minutos' => 4, 'orden' => 120],

            ['seccion' => 'maestros', 'tipo_asignacion' => 'maestro_estudiante', 'numero' => null, 'titulo' => null, 'duracion_minutos' => null, 'orden' => 200],

            ['seccion' => 'vida', 'tipo_asignacion' => 'vida_cristiana', 'numero' => null, 'titulo' => null, 'duracion_minutos' => null, 'orden' => 300],
            ['seccion' => 'vida', 'tipo_asignacion' => 'estudio_conductor', 'numero' => null, 'titulo' => 'Estudio bíblico de la congregación', 'duracion_minutos' => 30, 'orden' => 400],

            ['seccion' => 'final', 'tipo_asignacion' => 'oracion', 'numero' => null, 'titulo' => 'Oración final', 'duracion_minutos' => null, 'orden' => 500],
        ];

        foreach ($partes as $parte) {
            VidaMinisterioParte::create([
                'congregacion_id' => $programa->congregacion_id,
                'vida_ministerio_id' => $programa->id,
                'seccion' => $parte['seccion'],
                'tipo_asignacion' => $parte['tipo_asignacion'],
                'numero' => $parte['numero'],
                'titulo' => $parte['titulo'],
                'duracion_minutos' => $parte['duracion_minutos'],
                'orden' => $parte['orden'],
            ]);
        }
    }


    private function crearPartesBaseDesdeRequest(VidaMinisterio $programa, Request $request): array
{
    $input = $request->input('partes_base', []);

    if (empty($input)) {
        return [];
    }

    $base = [
        'presidente' => [
            'seccion' => 'encabezado',
            'tipo_asignacion' => 'presidente',
            'titulo' => 'Presidente',
            'duracion_minutos' => null,
            'orden' => 10,
        ],
        'ayudante_auditorio' => [
            'seccion' => 'encabezado',
            'tipo_asignacion' => 'ayudante_auditorio',
            'titulo' => 'Ayudante auditorio principal',
            'duracion_minutos' => null,
            'orden' => 20,
        ],
        'consejero_auxiliar' => [
            'seccion' => 'encabezado',
            'tipo_asignacion' => 'consejero_auxiliar',
            'titulo' => 'Consejero sala auxiliar',
            'duracion_minutos' => null,
            'orden' => 30,
        ],
        'ayudante_auxiliar' => [
            'seccion' => 'encabezado',
            'tipo_asignacion' => 'ayudante_auxiliar',
            'titulo' => 'Ayudante sala auxiliar',
            'duracion_minutos' => null,
            'orden' => 40,
        ],
        'oracion_inicio' => [
            'seccion' => 'encabezado',
            'tipo_asignacion' => 'oracion',
            'titulo' => 'Canción, oración y palabras de introducción',
            'duracion_minutos' => 6,
            'orden' => 50,
        ],

        'tesoro' => [
            'seccion' => 'tesoros',
            'tipo_asignacion' => 'tesoro',
            'titulo' => null,
            'duracion_minutos' => 10,
            'orden' => 100,
        ],
        'perlas' => [
            'seccion' => 'tesoros',
            'tipo_asignacion' => 'perlas',
            'titulo' => 'Busquemos perlas escondidas',
            'duracion_minutos' => 10,
            'orden' => 110,
        ],
        'lectura_biblia' => [
            'seccion' => 'tesoros',
            'tipo_asignacion' => 'lectura_biblia',
            'titulo' => 'Lectura de la Biblia',
            'duracion_minutos' => 4,
            'orden' => 120,
        ],

        'maestro_1' => [
            'seccion' => 'maestros',
            'tipo_asignacion' => 'maestro_estudiante',
            'titulo' => null,
            'duracion_minutos' => null,
            'orden' => 200,
        ],
       

        'vida_1' => [
            'seccion' => 'vida',
            'tipo_asignacion' => 'vida_cristiana',
            'titulo' => null,
            'duracion_minutos' => null,
            'orden' => 300,
        ],
        'estudio' => [
            'seccion' => 'vida',
            'tipo_asignacion' => 'estudio_conductor',
            'titulo' => 'Estudio bíblico de la congregación',
            'duracion_minutos' => 30,
            'orden' => 400,
        ],

        'oracion_final' => [
            'seccion' => 'final',
            'tipo_asignacion' => 'oracion',
            'titulo' => 'Palabras de conclusión, canción final y oración',
            'duracion_minutos' => 3,
            'orden' => 500,
        ],
    ];

    $creadas = [];

    foreach ($base as $key => $default) {
        if (!array_key_exists($key, $input)) {
            continue;
        }

        $parteData = $input[$key];

        $titulo = array_key_exists('titulo', $parteData)
            ? trim($parteData['titulo'] ?? '')
            : $default['titulo'];

        $duracion = array_key_exists('duracion_minutos', $parteData)
            ? $parteData['duracion_minutos']
            : $default['duracion_minutos'];

        $parte = VidaMinisterioParte::create([
            'congregacion_id' => $programa->congregacion_id,
            'vida_ministerio_id' => $programa->id,
            'seccion' => $parteData['seccion'] ?? $default['seccion'],
            'tipo_asignacion' => $parteData['tipo_asignacion'] ?? $default['tipo_asignacion'],
            'numero' => null,
            'titulo' => $titulo !== '' ? $titulo : null,
            'duracion_minutos' => $duracion !== null && $duracion !== '' ? (int) $duracion : null,
            'orden' => $default['orden'],
        ]);

        $creadas[(string) $key] = $parte->id;
    }

    return $creadas;
}

    private function guardarPartes(VidaMinisterio $programa, Request $request): array
    {
        $partesInput = $request->input('partes', []);
        $partes = $programa->partes()->get()->keyBy('id');

        foreach ($partesInput as $parteId => $parteData) {
            $parte = $partes->get((int) $parteId);

            if (!$parte) {
                continue;
            }

            if (!empty($parteData['eliminar']) && in_array($parte->seccion, ['maestros', 'vida'])) {
                $parte->delete();
                continue;
            }

            $parte->update([
                'titulo' => $parteData['titulo'] ?? null,
                'duracion_minutos' => isset($parteData['duracion_minutos']) && $parteData['duracion_minutos'] !== ''
                    ? (int) $parteData['duracion_minutos']
                    : null,
                'tipo_asignacion' => $parteData['tipo_asignacion'] ?? $parte->tipo_asignacion,
                'seccion' => $parteData['seccion'] ?? $parte->seccion,
            ]);
        }

        $nuevasPartesCreadas = [];

        foreach ($request->input('nuevas_partes', []) as $key => $nueva) {
            $titulo = trim($nueva['titulo'] ?? '');
            $duracion = $nueva['duracion_minutos'] ?? null;
            $seccion = $nueva['seccion'] ?? null;

            $tieneAsignaciones = collect($nueva['slots'] ?? [])
                ->contains(fn ($slot) => !empty($slot['publicador_id']));

            if (!$seccion || ($titulo === '' && !$duracion && !$tieneAsignaciones)) {
                continue;
            }

            $tipo = $nueva['tipo_asignacion'] ?? match ($seccion) {
                'maestros' => 'maestro_estudiante',
                'vida' => 'vida_cristiana',
                default => null,
            };

            if (!$tipo || !in_array($tipo, $this->tiposAsignacion(), true)) {
                continue;
            }

            $parte = VidaMinisterioParte::create([
                'congregacion_id' => $programa->congregacion_id,
                'vida_ministerio_id' => $programa->id,
                'seccion' => $seccion,
                'tipo_asignacion' => $tipo,
                'numero' => null,
                'titulo' => $titulo ?: null,
                'duracion_minutos' => $duracion !== null && $duracion !== '' ? (int) $duracion : null,
                'orden' => $this->siguienteOrden($programa, $seccion),
            ]);

            $nuevasPartesCreadas[(string) $key] = $parte->id;
        }

        return $nuevasPartesCreadas;
    }

    private function guardarAsignaciones(
        VidaMinisterio $programa,
        Request $request,
        array $nuevasPartesCreadas = [],
        array $partesBaseCreadas = []
    ): void {
        $publicadoresPermitidos = Publicador::where('congregacion_id', $this->congregacionId())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $calificaciones = VidaMinisterioCalificacion::where('congregacion_id', $this->congregacionId())
            ->where('activo', true)
            ->get()
            ->groupBy('publicador_id')
            ->map(fn ($items) => $items->pluck('tipo_asignacion')->toArray());

        VidaMinisterioAsignacion::where('vida_ministerio_id', $programa->id)->delete();

        $partes = $programa->partes()->get()->keyBy('id');
        $orden = 1;

        $guardarSlots = function ($parte, $slots) use (
            $programa,
            $publicadoresPermitidos,
            $calificaciones,
            &$orden
        ) {
            if (!$parte || !is_array($slots)) {
                return;
            }

            foreach ($slots as $slot) {
                $publicadorId = isset($slot['publicador_id']) ? (int) $slot['publicador_id'] : null;

                if (!$publicadorId || !in_array($publicadorId, $publicadoresPermitidos, true)) {
                    continue;
                }

                $tipoAsignacion = $slot['tipo_asignacion'] ?? $parte->tipo_asignacion;
                $tiposPermitidosPublicador = $calificaciones->get($publicadorId, []);

                if (!in_array($tipoAsignacion, $tiposPermitidosPublicador, true)) {
                    continue;
                }

                VidaMinisterioAsignacion::create([
                    'congregacion_id' => $programa->congregacion_id,
                    'vida_ministerio_id' => $programa->id,
                    'vida_ministerio_parte_id' => $parte->id,
                    'publicador_id' => $publicadorId,
                    'tipo_asignacion' => $tipoAsignacion,
                    'rol' => $slot['rol'] ?? 'principal',
                    'sala' => $slot['sala'] ?? 'general',
                    'fecha' => $programa->fecha,
                    'orden' => $orden++,
                    'notas' => null,
                ]);
            }
        };

        foreach ($request->input('asignaciones', []) as $parteId => $slots) {
            $parte = $partes->get((int) $parteId);
            $guardarSlots($parte, $slots);
        }

        foreach ($request->input('partes_base', []) as $key => $parteBase) {
            $parteId = $partesBaseCreadas[(string) $key] ?? null;

            if (!$parteId) {
                continue;
            }

            $parte = $partes->get((int) $parteId) ?: VidaMinisterioParte::find($parteId);

            $guardarSlots($parte, $parteBase['slots'] ?? []);
        }

        foreach ($request->input('nuevas_partes', []) as $key => $nueva) {
            $parteId = $nuevasPartesCreadas[(string) $key] ?? null;

            if (!$parteId) {
                continue;
            }

            $parte = $partes->get((int) $parteId) ?: VidaMinisterioParte::find($parteId);

            $guardarSlots($parte, $nueva['slots'] ?? []);
        }
    }

    private function recalcularNumeros(VidaMinisterio $programa): void
{
    $numero = 1;

    $partes = $programa->partes()
        ->withCount('asignaciones')
        ->whereNotIn('seccion', ['encabezado', 'final'])
        ->orderBy('orden')
        ->get();

    foreach ($partes as $parte) {
        $esParteFija = in_array($parte->tipo_asignacion, [
            'tesoro',
            'perlas',
            'lectura_biblia',
            'estudio_conductor',
        ], true);

        $tieneContenido = trim((string) $parte->titulo) !== ''
            || ((int) $parte->duracion_minutos > 0)
            || ((int) $parte->asignaciones_count > 0);

        if (!$esParteFija && !$tieneContenido) {
            $parte->update(['numero' => null]);
            continue;
        }

        $parte->update(['numero' => $numero++]);
    }

    $programa->partes()
        ->whereIn('seccion', ['encabezado', 'final'])
        ->update(['numero' => null]);
}

    private function calcularHorarios(VidaMinisterio $programa): void
    {
        if (!$programa->hora_inicio) {
            $programa->partes()->update([
                'hora_inicio' => null,
                'hora_fin' => null,
            ]);

            return;
        }

        $hora = strlen($programa->hora_inicio) === 5
            ? $programa->hora_inicio . ':00'
            : $programa->hora_inicio;

        $cursor = Carbon::createFromFormat('H:i:s', $hora);

        $partes = $programa->partes()
            ->whereNotNull('duracion_minutos')
            ->where('duracion_minutos', '>', 0)
            ->orderBy('orden')
            ->get();

        $programa->partes()->update([
            'hora_inicio' => null,
            'hora_fin' => null,
        ]);

        foreach ($partes as $parte) {
            $inicio = $cursor->format('H:i:s');
            $cursor->addMinutes((int) $parte->duracion_minutos);
            $fin = $cursor->format('H:i:s');

            $parte->update([
                'hora_inicio' => $inicio,
                'hora_fin' => $fin,
            ]);
        }
    }

 private function siguienteOrden(VidaMinisterio $programa, string $seccion): int
{
    if ($seccion === 'maestros') {
        $max = VidaMinisterioParte::where('vida_ministerio_id', $programa->id)
            ->where('seccion', 'maestros')
            ->where('tipo_asignacion', 'maestro_estudiante')
            ->max('orden');

        return $max ? ((int) $max + 10) : 200;
    }

    if ($seccion === 'vida') {
        $max = VidaMinisterioParte::where('vida_ministerio_id', $programa->id)
            ->where('seccion', 'vida')
            ->where('tipo_asignacion', 'vida_cristiana')
            ->max('orden');

        return $max ? ((int) $max + 10) : 300;
    }

    return 800;
}

    private function publicadoresPorTipo()
    {
        return VidaMinisterioCalificacion::with('publicador')
            ->where('congregacion_id', $this->congregacionId())
            ->where('activo', true)
            ->whereHas('publicador', function ($q) {
                $q->where('congregacion_id', $this->congregacionId())
                    ->where(function ($qq) {
                        $qq->whereNull('estado')
                            ->orWhere('estado', '')
                            ->orWhere('estado', 'activo');
                    });
            })
            ->get()
            ->groupBy('tipo_asignacion')
            ->map(function ($items) {
                return $items
                    ->pluck('publicador')
                    ->filter()
                    ->sortBy('nombre')
                    ->values();
            });
    }

private function historialAnteriorHasta(string $fecha)
{
    return VidaMinisterioAsignacion::with('publicador')
        ->where('congregacion_id', $this->congregacionId())
        ->whereDate('fecha', '<', $fecha)
        ->orderByDesc('fecha')
        ->get()
        ->groupBy(fn ($asignacion) => $asignacion->publicador_id . '|' . $asignacion->tipo_asignacion)
        ->map(fn ($items) => $items->first());
}

    private function tituloPeriodoPdf($programas): string
{
    if ($programas->isEmpty()) {
        return '';
    }

    $primera = \Carbon\Carbon::parse($programas->first()->fecha);
    $ultima = \Carbon\Carbon::parse($programas->last()->fecha);

    $mesInicio = mb_strtoupper($primera->locale('es')->translatedFormat('F'));
    $mesFin = mb_strtoupper($ultima->locale('es')->translatedFormat('F'));

    if ($mesInicio === $mesFin) {
        return $mesInicio . ' ' . $primera->format('Y');
    }

    return $mesInicio . ' - ' . $mesFin . ' ' . $ultima->format('Y');
}
}