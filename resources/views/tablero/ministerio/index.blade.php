@extends('layouts.app')

@section('content')
<div class="container">

    {{-- BOTONES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>
        <a href="{{ route('ministerio.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Agregar salida
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>

    {{-- ENCABEZADO --}}
    @php
        $inicio = $sinAgrupar->first();
        $fin    = $sinAgrupar->last();
    @endphp

    <div class="banner-programa">
        <h1 class="titulo">SALIDAS AL MINISTERIO</h1>
        @if($inicio && $fin)
            <h5 class="subtitulo">
                SEMANA DEL {{ \Carbon\Carbon::parse($inicio->fecha)->format('d') }}
                AL {{ \Carbon\Carbon::parse($fin->fecha)->format('d') }}
                DE {{ strtoupper(\Carbon\Carbon::parse($fin->fecha)->translatedFormat('F Y')) }}
            </h5>
        @endif
    </div>

    {{-- TABLA --}}
    <div class="table-responsive">
        <table class="tabla-programa text-center align-middle">
            <thead>
                <tr>
                    <th>DÍA</th>
                    <th>FECHA</th>
                    <th>HORA</th>
                    <th>CONDUCTOR</th>
                    <th>PUNTO DE ENCUENTRO</th>
                    <th>TERRITORIO</th>
                    <th class="no-print"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $fecha => $items)
                    @php
                        $dia = \Carbon\Carbon::parse($fecha)->translatedFormat('l');
                        $fechaForm = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                        $rowspan = $items->where('es_fila_info', false)->count();
                    @endphp

                    {{-- Fila informativa (si existe) --}}
                    @foreach($items as $i => $r)
                        @if($r->es_fila_info)
                            <tr>
                                <td>{{ strtoupper($dia) }}</td>
                                <td><strong>{{ $fechaForm }}</strong></td>
                                <td colspan="4" class="fw-bold text-center">
                                    {{ $r->punto_encuentro ?? $r->conductor ?? $r->territorio }}
                                </td>
                                <td class="no-print">
                                    <form action="{{ route('ministerio.destroy', $r) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Eliminar esta fila?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endif
                    @endforeach

                    {{-- Resto de salidas del día --}}
                    @foreach($items->where('es_fila_info', false)->values() as $i => $r)
                        <tr>
                            @if($i === 0)
                                <td rowspan="{{ $rowspan }}">{{ strtoupper($dia) }}</td>
                                <td rowspan="{{ $rowspan }}"><strong>{{ $fechaForm }}</strong></td>
                            @endif

                            <td>{{ $r->hora }}</td>
                            <td>{{ $r->conductor }}</td>
                            <td>{{ $r->punto_encuentro }}</td>
                            <td>{{ $r->territorio }}</td>
                            <td class="no-print">
                                <form action="{{ route('ministerio.destroy', $r) }}" method="POST">
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
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
