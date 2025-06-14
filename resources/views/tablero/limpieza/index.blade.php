@extends('layouts.app')

@section('content')
<div class="container">

    {{-- BOTONES (ocultos al imprimir) --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>

        <a href="{{ route('limpieza.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Actualizar programa (agregar fila)
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>

    {{-- ENCABEZADO TIPO CARTEL --}}
    <div class="banner-programa">
        <h1 class="titulo">LIMPIEZA DEL SALÓN</h2>
        <h5 class="subtitulo">POR GRUPO DE PREDICACIÓN</h5>
    </div>

    {{-- TABLA --}}
    <div class="table-responsive">
        <table class="tabla-programa text-center align-middle">
            <thead>
                <tr>
                    <th>MES</th>
                    <th>GRUPO ASIGNADO</th>
                    <th>SUPERINTENDENTE</th>
                    <th>AUXILIAR</th>
                    <th>OBSERVACIONES</th>
                    <th class="no-print"> </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registros as $r)
                <tr>
                    <td class="fw-bold">{{ strtoupper($r->mes) }}</td>
                    <td>{{ $r->grupo_asignado }}</td>
                    <td>{{ $r->superintendente }}</td>
                    <td>{{ $r->auxiliar }}</td>
                    <td>{{ $r->observaciones }}</td>
                    <td class="no-print">
                        <form action="{{ route('limpieza.destroy',$r) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar esta fila?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
