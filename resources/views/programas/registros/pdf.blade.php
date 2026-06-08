<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $programa->nombre }} - {{ $bloque->nombre }}</title>

    <style>
       @page {
    margin: 22px 24px;
}

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 10px;
    color: #222;
    margin: 0;
}

.banner-programa {
    border: 2px solid #6b5b95;
    border-radius: 6px;
    padding: 10px 14px;
    background: #ffffff;
    text-align: center;
    margin-bottom: 12px;
}

.titulo {
    font-size: 19px;
    font-weight: bold;
    margin: 0;
    color: #3d315b;
    letter-spacing: .4px;
}

.subtitulo {
    font-size: 11px;
    margin: 4px 0 0;
    color: #5f527f;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    border: 1px solid #b9b4c9;
}

thead {
    display: table-header-group;
}

th {
    background: #6b5b95;
    color: white;
    padding: 7px 5px;
    border: 1px solid #5b4b85;
    font-size: 9px;
    text-align: center;
    font-weight: bold;
    line-height: 1.25;
}

td {
    padding: 6px 5px;
    border: 1px solid #d4d0df;
    text-align: center;
    font-size: 9.5px;
    line-height: 1.35;
    vertical-align: middle;
    word-wrap: break-word;
}

.fila-blanca {
    background: #ffffff;
}

.fila-violeta {
    background: #f8f6ff;
}

.dia-fecha {
    font-weight: bold;
    white-space: nowrap;
    color: #3d315b;
}

.fila-especial-texto {
    background: #fff9d8 !important;
    color: #624800;
    font-weight: bold;
    text-align: center;
    border: 1px solid #ffe175;
}

.observaciones {
    margin-top: 12px;
    padding: 8px 10px;
    border: 2px solid #6b5b95;
    border-radius: 5px;
    font-size: 9.5px;
    line-height: 1.45;
    background: #ffffff;
}

.footer {
    margin-top: 8px;
    font-size: 7px;
    color: #777;
    text-align: right;
}
    </style>
</head>
<body>

@php
    $camposVisibles = $programa->campos->where('visible_en_listado', true);

    $registrosOrdenados = $registros->sortBy([
        ['fecha', 'asc'],
        ['orden', 'asc'],
        ['id', 'asc'],
    ]);

    $registrosPorFecha = $registrosOrdenados->groupBy(function ($registro) {
        return $registro->fecha ? $registro->fecha->format('Y-m-d') : 'sin_fecha';
    });
@endphp

<div class="banner-programa">
    <h1 class="titulo">{{ strtoupper($programa->nombre) }}</h1>

    <div class="subtitulo">
        {{ strtoupper($bloque->nombre) }}

        @if($bloque->descripcion)
            · {{ strtoupper($bloque->descripcion) }}
        @endif
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 13%;">DÍA</th>
            <th style="width: 13%;">FECHA</th>

            @foreach($camposVisibles as $campo)
                @if($campo->tipo !== 'fecha')
                    <th>{{ strtoupper($campo->nombre) }}</th>
                @endif
            @endforeach
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
                        <td rowspan="{{ $rowspan }}" class="{{ $rowClass }} dia-fecha">
                            {{ $fechaReal ? strtoupper($fechaReal->translatedFormat('l')) : '-' }}
                        </td>

                        <td rowspan="{{ $rowspan }}" class="{{ $rowClass }} dia-fecha">
                            {{ $fechaReal ? $fechaReal->format('d/m/Y') : '-' }}
                        </td>
                    @endif

                    @if($esEspecial)
                        <td colspan="{{ $camposVisibles->where('tipo', '!=', 'fecha')->count() }}"
                            class="{{ $rowClass }} fila-especial-texto">
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
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="{{ $camposVisibles->where('tipo', '!=', 'fecha')->count() + 2 }}"
                    style="text-align:center; padding: 12px;">
                    No hay filas cargadas.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($bloque->observaciones)
    <div class="observaciones">
        {!! nl2br(e($bloque->observaciones)) !!}
    </div>
@endif



</body>
</html>