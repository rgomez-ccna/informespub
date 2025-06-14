@extends('layouts.app')

@section('content')
<div class="container">

    {{-- BOTONES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>
        <a href="{{ route('publica.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Agregar asignación
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>

    {{-- ENCABEZADO --}}
    <div class="banner-programa">
        <h1 class="titulo">REUNIÓN PÚBLICA</h1>
        <h5 class="subtitulo">PRESIDENTE – LECTOR DE LA ATALAYA</h5>
    </div>

    {{-- TABLA --}}
    <div class="table-responsive">
        <table class="tabla-programa text-center align-middle">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>PRESIDENTE</th>
                    <th>LECTOR</th>
                    <th class="no-print"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $r)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($r->fecha)->format('d-m-Y') }}</td>
                    <td>{{ $r->presidente }}</td>
                    <td>{{ $r->lector }}</td>
                    <td class="no-print">
                        <form action="{{ route('publica.destroy', $r) }}" method="POST" onsubmit="return confirm('¿Eliminar esta fila?')">
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
