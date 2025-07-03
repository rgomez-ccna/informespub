@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 850px;">

    {{-- BOTONES SUPERIORES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>
        <a href="{{ route('ministerio.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Agregar salida
        </a>
    </div>

    <div class=" text-center mb-3">
        <h4 class="titulo">PROGRAMAS - SALIDAS AL MINISTERIO</h4>
    </div>

    {{-- ACORDEÓN --}}
    <div class="accordion" id="accordionProgramas">
        @foreach($bloques as $i => $registros)
            @php
                $primeraFecha = $registros->keys()->first();
                $ultimaFecha  = $registros->keys()->last();
                $idUnico = 'bloque_' . $i;
            @endphp

            <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $i }}">
                        <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $i }}"
                            aria-expanded="false" aria-controls="collapse{{ $i }}">
                            Semana del {{ \Carbon\Carbon::parse($primeraFecha)->format('d/m/Y') }}
                            al {{ \Carbon\Carbon::parse($ultimaFecha)->format('d/m/Y') }}
                        </button>
                    </h2>
                    <div id="collapse{{ $i }}" class="accordion-collapse collapse"
                        aria-labelledby="heading{{ $i }}" data-bs-parent="#accordionProgramas">

                    <div class="accordion-body">
                        {{-- CONTENIDO DE LA SEMANA --}}
                        <div id="{{ $idUnico }}">
                            {{-- BANNER INTERNO (VISIBLE EN IMPRESIÓN) --}}
                            <div class="banner-programa text-center mb-3">
                                <h4 class="titulo">SALIDAS AL MINISTERIO</h4>
                                <h6 class="subtitulo">
                                    SEMANA DEL {{ \Carbon\Carbon::parse($primeraFecha)->format('d') }}
                                    AL {{ \Carbon\Carbon::parse($ultimaFecha)->format('d') }}
                                    DE {{ strtoupper(\Carbon\Carbon::parse($ultimaFecha)->translatedFormat('F Y')) }}
                                </h6>
                            </div>

                            {{-- TABLA DE LA SEMANA --}}
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
                                        @php $colorIndex = 0; @endphp

                                        @foreach($registros as $fecha => $items)
                                            @php
                                                $dia = \Carbon\Carbon::parse($fecha)->translatedFormat('l');
                                                $fechaForm = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                                $rowspan = $items->where('es_fila_info', false)->count();
                                                $rowClass = $colorIndex % 2 === 0 ? 'fila-blanca' : 'fila-violeta';
                                            @endphp

                                            {{-- Fila informativa --}}
                                            @foreach($items as $i => $r)
                                                @if($r->es_fila_info)
                                                    <tr class="{{ $rowClass }}">
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

                                            {{-- Resto de salidas --}}
                                            @foreach($items->where('es_fila_info', false)->values() as $i => $r)
                                                <tr>
                                                    @if($i === 0)
                                                        <td rowspan="{{ $rowspan }}" class="{{ $rowClass }}">{{ strtoupper($dia) }}</td>
                                                        <td rowspan="{{ $rowspan }}" class="{{ $rowClass }}"><strong>{{ $fechaForm }}</strong></td>
                                                    @endif

                                                    <td class="{{ $rowClass }}">{{ $r->hora }}</td>
                                                    <td class="{{ $rowClass }}">{{ $r->conductor }}</td>
                                                    <td class="{{ $rowClass }}">{{ $r->punto_encuentro }}</td>
                                                    <td class="{{ $rowClass }}">{{ $r->territorio }}</td>
                                                    <td class="no-print {{ $rowClass }}">
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

                                            @php $colorIndex++; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        

                        </div> {{-- FIN BLOQUE SEMANA --}}
                         {{-- BOTÓN DE IMPRIMIR ESTA SEMANA --}}
                        <div class="text-end no-print mb-2">
                            <button onclick="imprimirBloque('{{ $idUnico }}')" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-print"></i> Imprimir esta semana
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- JS PARA IMPRIMIR SOLO UN BLOQUE --}}
<script>
    function imprimirBloque(id) {
        let original = document.body.innerHTML;
        let contenido = document.getElementById(id).innerHTML;
        document.body.innerHTML = contenido;
        window.print();
        document.body.innerHTML = original;
        location.reload(); // para recargar después de imprimir
    }
</script>
@endsection
