@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('programas.registros.index', $programa) }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>

        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>

    <div class="banner-programa text-center mb-3">
        <h1 class="titulo">{{ strtoupper($programa->nombre) }}</h1>

        @if($programa->descripcion)
            <h5 class="subtitulo">{{ strtoupper($programa->descripcion) }}</h5>
        @endif
    </div>

    <div class="table-responsive">
        <table class="tabla-programa text-center align-middle">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>TÍTULO</th>

                    @foreach($programa->campos as $campo)
                        <th>{{ strtoupper($campo->nombre) }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($registros as $registro)
                    @php
                        $valores = $registro->valores->keyBy('programa_campo_id');
                    @endphp

                    <tr>
                        <td>
                            <strong>
                                {{ $registro->fecha ? $registro->fecha->format('d/m/Y') : '-' }}
                            </strong>
                        </td>

                        <td>{{ $registro->titulo ?: '-' }}</td>

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
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }

    body {
        margin: 0 !important;
        background: white !important;
    }

    .container {
        width: 100% !important;
        max-width: none !important;
        padding: 0 20px !important;
    }

    table {
        width: 100% !important;
    }
}
</style>
@endsection