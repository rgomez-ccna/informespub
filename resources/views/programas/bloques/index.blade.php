@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1000px;">

    {{-- BOTONES SUPERIORES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print sticky-top bg-white pt-2 pb-2" style="z-index:20;">
        <a href="{{ route('tablero.index') }}" class="btn btn-light border btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al tablero
        </a>

        <a href="{{ route('programas.edit', $programa) }}" class="btn btn-warning btn-sm">
            <i class="fa-solid fa-table-columns"></i> Editar programa y columnas
        </a>

        <a href="{{ route('programas.bloques.create', $programa) }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Nueva planilla
        </a>
    </div>

    <div class="text-center mb-3">
        <h4 class="titulo">PLANILLAS - {{ strtoupper($programa->nombre) }}</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success no-print">{{ session('success') }}</div>
    @endif

    @if($programa->campos->where('tipo', '!=', 'fecha')->isEmpty())
        <div class="alert alert-info border">
            Este programa todavía no tiene columnas para cargar datos.
            <a href="{{ route('programas.edit', $programa) }}">Agregar columnas</a>
        </div>
    @elseif($bloques->isEmpty())
        <div class="alert alert-light border text-center">
            Todavía no hay planillas creadas.
            <br>
            <a href="{{ route('programas.bloques.create', $programa) }}" class="btn btn-primary btn-sm mt-3">
                <i class="fa-solid fa-plus"></i> Crear primera planilla
            </a>
        </div>
    @else

        <div class="accordion" id="accordionProgramas">
            @foreach($bloques as $i => $bloque)
                @php
                    $idUnico = 'bloque_' . $bloque->id;
                    $collapseId = 'collapse_' . $bloque->id;
                    $abierto = $loop->first;

                    $camposVisibles = $programa->campos->where('visible_en_listado', true);

                    $registrosOrdenados = $bloque->registros
                        ->sortBy([
                            ['fecha', 'asc'],
                            ['orden', 'asc'],
                            ['id', 'asc'],
                        ]);

                    $registrosPorFecha = $registrosOrdenados->groupBy(function ($registro) {
                        return $registro->fecha ? $registro->fecha->format('Y-m-d') : 'sin_fecha';
                    });
                @endphp

                <div class="accordion-item mb-3">
                    <h2 class="accordion-header" id="heading{{ $bloque->id }}">
                        <button class="accordion-button {{ $abierto ? '' : 'collapsed' }}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#{{ $collapseId }}"
                                aria-expanded="{{ $abierto ? 'true' : 'false' }}"
                                aria-controls="{{ $collapseId }}">
                            <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                                <span>
                                    {{ $bloque->nombre }}
                                </span>

                                <small class="text-muted">
                                    {{ $registrosOrdenados->count() }} filas
                                </small>
                            </div>
                        </button>
                    </h2>

                    <div id="{{ $collapseId }}"
                         class="accordion-collapse collapse {{ $abierto ? 'show' : '' }}"
                         aria-labelledby="heading{{ $bloque->id }}"
                         data-bs-parent="#accordionProgramas">

                        <div class="accordion-body">

                            <div class="d-flex justify-content-end gap-2 mb-3 no-print">
                                <a href="{{ route('programas.bloques.registros.create', [$programa, $bloque]) }}"
                                class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus"></i> Agregar fila
                                </a>

                                <a href="{{ route('programas.bloques.edit', [$programa, $bloque]) }}"
                                class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-edit"></i> Editar planilla
                                </a>

                                <a href="{{ route('programas.bloques.registros.pdf', [$programa, $bloque]) }}"
                                class="btn btn-outline-danger btn-sm"
                                target="_blank">
                                    <i class="fa-solid fa-file-pdf"></i> Imprimir PDF
                                </a>

                                <form action="{{ route('programas.bloques.destroy', [$programa, $bloque]) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('¿Eliminar esta planilla y todas sus filas cargadas?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i> Eliminar planilla
                                    </button>
                                </form>
                            </div>

                            <div id="{{ $idUnico }}">

                                {{-- ENCABEZADO --}}
                                <div class="banner-programa text-center mb-3">
                                    <h4 class="titulo">{{ strtoupper($programa->nombre) }}</h4>

                                    <h6 class="subtitulo">
                                        {{ strtoupper($bloque->nombre) }}

                                        @if($bloque->descripcion)
                                            · {{ strtoupper($bloque->descripcion) }}
                                        @endif
                                    </h6>
                                </div>

                                {{-- TABLA --}}
                                <div class="table-responsive">
                                    <table class="tabla-programa text-center align-middle">
                                        <thead>
                                            <tr>
                                                <th>DÍA</th>
                                                <th>FECHA</th>

                                                @foreach($camposVisibles as $campo)
                                                    @if($campo->tipo !== 'fecha')
                                                        <th>{{ strtoupper($campo->nombre) }}</th>
                                                    @endif
                                                @endforeach

                                                <th class="no-print text-nowrap" style="width:110px;"></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse($registrosPorFecha as $fechaGrupo => $items)
                                                @php
                                                    $fechaReal = $fechaGrupo !== 'sin_fecha'
                                                        ? \Carbon\Carbon::parse($fechaGrupo)
                                                        : null;

                                                    $rowClass = $loop->index % 2 === 0 ? 'fila-blanca' : 'fila-violeta';
                                                    $rowspan = $items->count();
                                                @endphp

                                                @foreach($items->values() as $posicion => $registro)
                                                    @php
                                                        $valores = $registro->valores->keyBy('programa_campo_id');
                                                        $esEspecial = $registro->tipo_fila !== 'normal';
                                                    @endphp

                                                    <tr>
                                                        @if($posicion === 0)
                                                            <td rowspan="{{ $rowspan }}" class="{{ $rowClass }} fw-semibold">
                                                                {{ $fechaReal ? strtoupper($fechaReal->translatedFormat('l')) : '-' }}
                                                            </td>

                                                            <td rowspan="{{ $rowspan }}" class="{{ $rowClass }}">
                                                                <strong>{{ $fechaReal ? $fechaReal->format('d/m/Y') : '-' }}</strong>
                                                            </td>
                                                        @endif

                                                        @if($esEspecial)
                                                            <td colspan="{{ $camposVisibles->where('tipo', '!=', 'fecha')->count() }}"
                                                                class="{{ $rowClass }} fw-bold text-center fila-especial-texto">
                                                                {{ $registro->texto_especial ?: strtoupper($registro->tipo_fila) }}
                                                            </td>
                                                        @else
                                                            @foreach($camposVisibles as $campo)
                                                                @if($campo->tipo !== 'fecha')
                                                                    @php
                                                                        $valor = $valores->get($campo->id);
                                                                    @endphp

                                                                    <td class="{{ $rowClass }}">
                                                                        @include('programas.registros.partials.valor', [
                                                                            'campo' => $campo,
                                                                            'valor' => $valor
                                                                        ])
                                                                    </td>
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                        <td class="no-print {{ $rowClass }} text-nowrap">
                                                            <a href="{{ route('programas.bloques.registros.edit', [$programa, $bloque, $registro]) }}"
                                                               class="btn btn-sm btn-warning">
                                                                <i class="fa-solid fa-edit"></i>
                                                            </a>

                                                            <form action="{{ route('programas.bloques.registros.destroy', [$programa, $bloque, $registro]) }}"
                                                                  method="POST"
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('¿Eliminar esta fila?')">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button class="btn btn-sm btn-outline-danger">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @empty
                                                <tr>
                                                    <td colspan="{{ $camposVisibles->where('tipo', '!=', 'fecha')->count() + 3 }}"
                                                        class="text-center text-muted py-4">
                                                        Todavía no hay filas cargadas en esta planilla.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($bloque->observaciones)
                                    <div class="mt-4 px-3 py-2 texto-final">
                                        {!! nl2br(e($bloque->observaciones)) !!}
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<style>
.banner-programa {
    border: 3px solid #6b5b95;
    border-radius: 8px;
    padding: 12px;
    background: #ffffff;
}

.banner-programa .titulo {
    font-size: 22px;
    font-weight: 800;
    margin: 0;
    color: #3d315b;
}

.banner-programa .subtitulo {
    font-size: 13px;
    font-weight: 600;
    margin: 4px 0 0;
    color: #5f527f;
}

.tabla-programa {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.tabla-programa th {
    background: #6b5b95;
    color: white;
    font-size: 12px;
    padding: 9px;
    border: 1px solid #59497d;
}

.tabla-programa td {
    font-size: 12px;
    padding: 8px;
    border: 1px solid #ddd;
    vertical-align: middle;
}

.fila-blanca {
    background: #ffffff;
}

.fila-violeta {
    background: #f8f6ff;
}

.fila-especial-texto {
    color: #624800;
    background: #fff9d8 !important;
    border: 1px solid #ffe175;
}



.texto-final {
    font-size: 13px;
    border: 2px solid #6b5b95;
    border-radius: 6px;
    background: #fff;
}

.accordion-button {
    font-weight: 600;
}

@media print {
    .no-print {
        display: none !important;
    }

    .container {
        max-width: none !important;
        width: 100% !important;
    }
}
</style>
@endsection
