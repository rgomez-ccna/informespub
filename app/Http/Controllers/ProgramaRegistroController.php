<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\ProgramaBloque;
use App\Models\ProgramaRegistro;
use App\Models\ProgramaValor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class ProgramaRegistroController extends Controller
{
    public function index(Programa $programa, ProgramaBloque $bloque)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);

        $programa->load(['campos' => function ($q) {
            $q->where('activo', true)->orderBy('orden');
        }]);

        $registros = ProgramaRegistro::with(['valores.campo'])
            ->where('programa_id', $programa->id)
            ->where('programa_bloque_id', $bloque->id)
            ->where('congregacion_id', Auth::user()->congregacion_id)
            ->orderBy('fecha')
            ->orderBy('orden')
            ->orderBy('id')
            ->paginate(50);

        return view('programas.registros.index', compact('programa', 'bloque', 'registros'));
    }

    public function create(Programa $programa, ProgramaBloque $bloque)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);

        $programa->load(['campos' => function ($q) {
            $q->where('activo', true)->orderBy('orden');
        }]);

        return view('programas.registros.create', compact('programa', 'bloque'));
    }

    public function store(Request $request, Programa $programa, ProgramaBloque $bloque)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);

        $programa->load(['campos' => function ($q) {
            $q->where('activo', true)->orderBy('orden');
        }]);

        $tipoFila = $request->input('tipo_fila');
        $campoFecha = $programa->campos->firstWhere('tipo', 'fecha');

        $rules = [
            'tipo_fila' => ['required', 'in:normal,especial'],

            'fecha_especial' => [
                Rule::requiredIf($tipoFila === 'especial'),
                'nullable',
                'date',
            ],

            'texto_especial' => [
                Rule::requiredIf($tipoFila === 'especial'),
                'nullable',
                'string',
                'max:1000',
            ],

            'orden' => ['nullable', 'integer'],
        ];

        $attributes = [
            'tipo_fila' => 'tipo de fila',
            'fecha_especial' => 'fecha del aviso',
            'texto_especial' => 'texto del aviso',
            'orden' => 'orden',
        ];

        foreach ($programa->campos as $campo) {
            $esCampoFecha = $campoFecha && (int) $campo->id === (int) $campoFecha->id;

            $campoRule = ['nullable'];

            if ($tipoFila === 'normal' && ($campo->obligatorio || $esCampoFecha)) {
                array_unshift($campoRule, 'required');
            }

            if ($campo->tipo === 'numero') {
                $campoRule[] = 'numeric';
            }

            if ($campo->tipo === 'fecha') {
                $campoRule[] = 'date';
            }

            $rules['campos.' . $campo->id] = $campoRule;
            $attributes['campos.' . $campo->id] = $campo->nombre;
        }

        $request->validate($rules, [], $attributes);

        $fechaRegistro = $tipoFila === 'normal'
            ? ($campoFecha ? $request->input('campos.' . $campoFecha->id) : null)
            : $request->fecha_especial;

        $ordenSiguiente = ProgramaRegistro::where('programa_bloque_id', $bloque->id)->max('orden') + 1;

        $registro = ProgramaRegistro::create([
            'congregacion_id' => Auth::user()->congregacion_id,
            'programa_id' => $programa->id,
            'programa_bloque_id' => $bloque->id,
            'user_id' => Auth::id(),
            'fecha' => $fechaRegistro,
            'titulo' => null,
            'estado' => 'activo',
            'tipo_fila' => $tipoFila,
            'texto_especial' => $tipoFila === 'especial' ? $request->texto_especial : null,
            'orden' => $ordenSiguiente,
        ]);

        if ($tipoFila === 'normal') {
            foreach ($programa->campos as $campo) {
                $this->guardarValor($registro, $campo, $request->input('campos.' . $campo->id));
            }
        }

        return redirect()
            ->route('programas.bloques.index', $programa)
            ->with('success', 'Fila agregada correctamente.');
    }

    public function edit(Programa $programa, ProgramaBloque $bloque, ProgramaRegistro $registro)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);
        $this->autorizarRegistro($programa, $bloque, $registro);

        $programa->load(['campos' => function ($q) {
            $q->where('activo', true)->orderBy('orden');
        }]);

        $registro->load('valores');

        $valores = $registro->valores->keyBy('programa_campo_id');

        return view('programas.registros.edit', compact('programa', 'bloque', 'registro', 'valores'));
    }

public function update(Request $request, Programa $programa, ProgramaBloque $bloque, ProgramaRegistro $registro)
{
    $this->autorizarPrograma($programa);
    $this->autorizarBloque($programa, $bloque);
    $this->autorizarRegistro($programa, $bloque, $registro);

    $programa->load(['campos' => function ($q) {
        $q->where('activo', true)->orderBy('orden');
    }]);

    $tipoFila = $request->input('tipo_fila');
    $campoFecha = $programa->campos->firstWhere('tipo', 'fecha');

    $rules = [
        'tipo_fila' => ['required', 'in:normal,especial'],

        'fecha_especial' => [
            Rule::requiredIf($tipoFila === 'especial'),
            'nullable',
            'date',
        ],

        'texto_especial' => [
            Rule::requiredIf($tipoFila === 'especial'),
            'nullable',
            'string',
            'max:1000',
        ],

        'orden' => ['nullable', 'integer'],
    ];

    $attributes = [
        'tipo_fila' => 'tipo de fila',
        'fecha_especial' => 'fecha del aviso',
        'texto_especial' => 'texto del aviso',
        'orden' => 'orden',
    ];

    foreach ($programa->campos as $campo) {
        $esCampoFecha = $campoFecha && (int) $campo->id === (int) $campoFecha->id;

        $campoRule = ['nullable'];

        if ($tipoFila === 'normal' && ($campo->obligatorio || $esCampoFecha)) {
            array_unshift($campoRule, 'required');
        }

        if ($campo->tipo === 'numero') {
            $campoRule[] = 'numeric';
        }

        if ($campo->tipo === 'fecha') {
            $campoRule[] = 'date';
        }

        $rules['campos.' . $campo->id] = $campoRule;
        $attributes['campos.' . $campo->id] = $campo->nombre;
    }

    $request->validate($rules, [], $attributes);

    $fechaRegistro = $tipoFila === 'normal'
        ? ($campoFecha ? $request->input('campos.' . $campoFecha->id) : null)
        : $request->fecha_especial;

    $registro->update([
        'fecha' => $fechaRegistro,
        'titulo' => null,
        'tipo_fila' => $tipoFila,
        'texto_especial' => $tipoFila === 'especial' ? $request->texto_especial : null,
        'orden' => $request->orden ?? $registro->orden,
    ]);

    if ($tipoFila === 'normal') {
        foreach ($programa->campos as $campo) {
            $this->guardarValor($registro, $campo, $request->input('campos.' . $campo->id));
        }
    } else {
        $registro->valores()->delete();
    }

    return redirect()
        ->route('programas.bloques.index', $programa)
        ->with('success', 'Fila actualizada correctamente.');
}

    public function destroy(Programa $programa, ProgramaBloque $bloque, ProgramaRegistro $registro)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);
        $this->autorizarRegistro($programa, $bloque, $registro);

        $registro->delete();

       return redirect()
    ->route('programas.bloques.index', $programa)
    ->with('success', 'Fila eliminada correctamente.');
    }

    public function pdf(Programa $programa, ProgramaBloque $bloque)
    {
        $this->autorizarPrograma($programa);
        $this->autorizarBloque($programa, $bloque);

        $programa->load(['campos' => function ($q) {
            $q->where('activo', true)
                ->where('visible_en_listado', true)
                ->orderBy('orden');
        }]);

        $registros = ProgramaRegistro::with(['valores.campo'])
            ->where('programa_id', $programa->id)
            ->where('programa_bloque_id', $bloque->id)
            ->where('congregacion_id', Auth::user()->congregacion_id)
            ->orderBy('fecha')
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView('programas.registros.pdf', compact('programa', 'bloque', 'registros'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($programa->slug . '-' . $bloque->id . '.pdf');
    }

    private function guardarValor(ProgramaRegistro $registro, $campo, $valor): void
    {
        $data = [
            'valor_texto' => null,
            'valor_numero' => null,
            'valor_fecha' => null,
            'valor_hora' => null,
            'valor_json' => null,
        ];

        if ($campo->tipo === 'numero') {
            $data['valor_numero'] = $valor;
        } elseif ($campo->tipo === 'fecha') {
            $data['valor_fecha'] = $valor;
        } elseif ($campo->tipo === 'hora') {
            $data['valor_hora'] = $valor;
        } elseif ($campo->tipo === 'checkbox') {
            $data['valor_json'] = [
                'checked' => (bool) $valor,
            ];
        } else {
            $data['valor_texto'] = $valor;
        }

        ProgramaValor::updateOrCreate(
            [
                'programa_registro_id' => $registro->id,
                'programa_campo_id' => $campo->id,
            ],
            $data
        );
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

    private function autorizarRegistro(Programa $programa, ProgramaBloque $bloque, ProgramaRegistro $registro): void
    {
        if (
            $registro->programa_id !== $programa->id ||
            $registro->programa_bloque_id !== $bloque->id ||
            $registro->congregacion_id !== Auth::user()->congregacion_id
        ) {
            abort(404);
        }
    }
}