@extends('layouts.app')

@section('content')
<div class="container">

    {{-- BOTONES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>
        <a href="{{ route('acomodadores.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Agregar asignación
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>

    {{-- ENCABEZADO --}}
    <div class="banner-programa">
        <h1 class="titulo">ACOMODADORES</h1>
@php
    $inicio = $registros->first()?->fecha;
    $fin    = $registros->last()?->fecha;

    $mesInicio = \Carbon\Carbon::parse($inicio)->translatedFormat('F');
    $mesFin    = \Carbon\Carbon::parse($fin)->translatedFormat('F');
    $anio      = \Carbon\Carbon::parse($inicio)->year;
@endphp

<h5 class="subtitulo">
    {{ strtoupper($mesInicio . ($mesInicio !== $mesFin ? ' - ' . $mesFin : '') . ' / ' . $anio) }}
</h5>



    </div>

    {{-- TABLA --}}
    <div class="table-responsive">
        <table class="tabla-programa text-center align-middle">
            <thead>
                <tr>
                    <th>DÍA</th>
                    <th>FECHA</th>
                    <th>ACCESO 1</th>
                    <th>ACCESO 2</th>
                    <th>AUDITORIO</th>
                    <th class="no-print"> </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registros as $r)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($r->fecha)->translatedFormat('l') }}</td>
                    <td><strong>{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</strong></td>
                    <td>{{ $r->acceso_1 }}</td>
                    <td>{{ $r->acceso_2 }}</td>
                    <td>{{ $r->auditorio }}</td>
                    <td class="no-print">
                       <form action="{{ route('acomodadores.destroy', $r->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('¿Eliminar esta fila?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TEXTO FINAL (solo si existe) --}}
    @if($texto)
    <div class="mt-4 px-3 py-2 texto-final-imprimir" style="font-size: 13px; border: 2px solid #6b5b95; border-radius: 6px;">
        {!! nl2br(e($texto)) !!}
    </div>
    @endif

</div>

<style>
@media print {
    .texto-final-imprimir {
        font-size: 11px !important;
    }
}
</style>

@endsection
