<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $programa->nombre }} - {{ $bloque->nombre }}</title>

    <style>
        @page {
            margin: 18px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
            margin: 0;
        }

        .banner-programa {
            border: 2px solid #6b5b95;
            border-radius: 6px;
            padding: 9px 12px;
            background: #f4f0ff;
            text-align: center;
            margin-bottom: 10px;
        }

        .titulo {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            color: #3d315b;
            letter-spacing: .5px;
        }

        .subtitulo {
            font-size: 10px;
            margin: 3px 0 0;
            color: #5f527f;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        th {
            background: #6b5b95;
            color: white;
            padding: 6px 4px;
            border: 1px solid #59497d;
            font-size: 8px;
            text-align: center;
            font-weight: bold;
            line-height: 1.2;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #cfcfcf;
            text-align: center;
            font-size: 8px;
            line-height: 1.25;
            vertical-align: middle;
            word-wrap: break-word;
        }

        tbody tr:nth-child(even) {
            background: #f8f6ff;
        }

        .dia {
            font-weight: bold;
            white-space: nowrap;
        }

        .fila-especial td {
            background: #fff3c4;
            color: #4b3b00;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }

        .observaciones {
            margin-top: 12px;
            padding: 8px 10px;
            border: 2px solid #6b5b95;
            border-radius: 5px;
            font-size: 9px;
            line-height: 1.4;
        }

        .footer {
            margin-top: 8px;
            font-size: 7px;
            color: #666;
            text-align: right;
        }
    </style>
</head>
<body>

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
                <th>DÍA</th>

                @foreach($programa->campos as $campo)
                    <th>{{ strtoupper($campo->nombre) }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($registros as $registro)
                @php
                    $valores = $registro->valores->keyBy('programa_campo_id');
                    $esEspecial = $registro->tipo_fila !== 'normal';
                    $cantidadColumnas = $programa->campos->count() + 1;
                @endphp

                @if($esEspecial)
                    <tr class="fila-especial">
                        <td colspan="{{ $cantidadColumnas }}">
                            {{ $registro->texto_especial ?: strtoupper($registro->tipo_fila) }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td class="dia">
                            {{ $registro->fecha ? strtoupper($registro->fecha->translatedFormat('l')) : '-' }}
                        </td>

                        @foreach($programa->campos as $campo)
                            @php
                                $valor = $valores->get($campo->id);
                            @endphp

                            <td>
                                @include('programas.registros.partials.valor', [
                                    'campo' => $campo,
                                    'valor' => $valor
                                ])
                            </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    @if($bloque->observaciones)
        <div class="observaciones">
            {!! nl2br(e($bloque->observaciones)) !!}
        </div>
    @endif

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>