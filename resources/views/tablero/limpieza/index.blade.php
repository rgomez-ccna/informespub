@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">
        {{-- LIMPIEZA POR GRUPO --}}
        <div id="bloque-grupo" class="col-md-6">
            {{-- BOTONES (ocultos al imprimir) --}}
            <div class="d-flex justify-content-end gap-2 mb-3 no-print">
                <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
                </a>

                <a href="{{ route('limpieza.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Actualizar programa (agregar fila)
                </a>
                <button onclick="imprimirSeleccionadosGrupo()" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-print"></i> Imprimir seleccionados
                </button>
            </div>

            {{-- ENCABEZADO TIPO CARTEL --}}
            <div class="banner-programa">
                <h1 class="titulo">LIMPIEZA DEL SALÓN</h1>
                <h5 class="subtitulo">POR GRUPO DE PREDICACIÓN</h5>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive">
                <form method="GET" class="mb-2 d-flex align-items-center gap-2 no-print">
                    <input type="hidden" name="per_page_mensual" value="{{ $porPaginaMensual }}">
                    <input type="hidden" name="page_mensual" value="{{ request('page_mensual') }}">

                    <label for="per_page" class="form-label mb-0">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm w-auto"
                            onchange="this.form.submit()">
                        @foreach([5, 10, 20, 50, 100] as $cantidad)
                            <option value="{{ $cantidad }}" {{ $porPagina == $cantidad ? 'selected' : '' }}>
                                {{ $cantidad }}
                            </option>
                        @endforeach
                    </select>
                    <span>registros</span>
                </form>

                <table class="tabla-programa text-center align-middle">
                    <thead>
                        <tr>
                            <th class="no-print"><input type="checkbox" id="checkAllGrupo" class="form-check-input"></th>
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
                        <tr class="fila-imprimible-grupo">
                            <td class="no-print"><input type="checkbox" class="form-check-input check-fila-grupo"></td>
                            <td class="fw-bold">{{ strtoupper($r->mes) }}</td>
                            <td>{{ $r->grupo_asignado }}</td>
                            <td>{{ $r->superintendente }}</td>
                            <td>{{ $r->auxiliar }}</td>
                            <td>{{ $r->observaciones }}</td>
                            <td class="no-print">
                                <form action="{{ route('limpieza.destroy',$r) }}" method="POST" onsubmit="return confirm('¿Eliminar esta fila?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="no-print mt-3">
               {{ $registros->appends([
                    'per_page' => $porPagina,
                    'per_page_mensual' => $porPaginaMensual,
                    'page_mensual' => request('page_mensual') // para que no se pierda la otra página
                ])->links('pagination::bootstrap-5') }}

            </div>
        </div>

        {{-- LIMPIEZA MENSUAL POR CONGREGACIÓN --}}
        <div  id="bloque-mensual" class="col-md-6">
            {{-- BOTONES (ocultos al imprimir) --}}
            <div class="d-flex justify-content-end gap-2 mb-3 no-print">
                <a href="{{ route('limpieza-mensual.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Actualizar Limpieza mensual (Agregar fila)
                 </a>
                <button onclick="imprimirSeleccionadosMensual()" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-print"></i> Imprimir seleccionados
                </button>
            </div>

            {{-- ENCABEZADO TIPO CARTEL --}}
            <div class="banner-programa">
                <h1 class="titulo">LIMPIEZA MENSUAL</h1>
                <h5 class="subtitulo">DEL SALÓN POR CONGREGACIÓN</h5>
            </div>
                <form method="GET" class="mb-2 d-flex align-items-center gap-2 no-print">
                    <input type="hidden" name="per_page" value="{{ $porPagina }}">
                    <input type="hidden" name="page" value="{{ request('page') }}">

                    <label for="per_page_mensual" class="form-label mb-0">Mostrar</label>
                    <select name="per_page_mensual" id="per_page_mensual" class="form-select form-select-sm w-auto"
                            onchange="this.form.submit()">
                        @foreach([5, 10, 20, 50, 100] as $cantidad)
                            <option value="{{ $cantidad }}" {{ $porPaginaMensual == $cantidad ? 'selected' : '' }}>
                                {{ $cantidad }}
                            </option>
                        @endforeach
                    </select>
                    <span>registros</span>
                </form>

            <div class="table-responsive">
                <table class="tabla-programa text-center align-middle">
                    <thead>
                        <tr>
                            <th class="no-print"><input type="checkbox" id="checkAllMensual" class="form-check-input"></th>
                            <th>FECHA PROPUESTA</th>
                            <th>CONGREGACIÓN ASIGNADA</th>
                            <th>OBSERVACIONES</th>
                            <th class="no-print"> </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mensual as $m)
                        <tr class="fila-imprimible-mensual">
                            <td class="no-print"><input type="checkbox" class="form-check-input check-fila-mensual"></td>
                            <td>{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $m->congregacion }}</td>
                            <td>{{ $m->observaciones }}</td>
                            <td class="no-print">
                                <form action="{{ route('limpieza-mensual.destroy',$m) }}" method="POST" onsubmit="return confirm('¿Eliminar esta fila?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
               <div class="no-print mt-3">
                   {{ $mensual->appends([
                    'per_page_mensual' => $porPaginaMensual,
                    'per_page' => $porPagina,
                    'page' => request('page') // para que no se pierda la otra página
                ])->links('pagination::bootstrap-5') }}

                </div>

            </div>

            {{-- OBSERVACIÓN GENERAL DE LIMPIEZA --}}
          @if($observacionGeneral)
                <div class="mt-2">
                    <div class="alert alert-light">
                        <strong>Nota:</strong><br>
                        {!! nl2br(e($observacionGeneral)) !!}
                    </div>
                </div>
            @endif



        </div>
    </div>

    

</div>
<style>
    /* no se corte en dos líneas al imprimir, y siempre se mantenga en una sola línea */
@media print {
    td.fw-bold {
        white-space: nowrap !important;
    }
}
</style>
<style>
@media print {
    body {
        margin: 0 !important;
    }

    .container {
        width: 100% !important;
        max-width: none !important;
        padding: 0 20px !important;
    }

    table {
        width: 100% !important;
    }

    #bloque-grupo,
    #bloque-mensual {
        break-inside: avoid-page;
    }
}
</style>



<script>
function imprimirSeleccionadosGrupo() {
    const bloqueMensual = document.getElementById('bloque-mensual');
    const filas = document.querySelectorAll('.fila-imprimible-grupo');

    // oculto el otro bloque
    bloqueMensual.classList.add('d-none');

    // oculto todas y muestro solo seleccionadas
    filas.forEach(f => f.classList.add('d-none'));
    document.querySelectorAll('.check-fila-grupo:checked').forEach(c => {
        c.closest('.fila-imprimible-grupo').classList.remove('d-none');
    });

    window.print();

    // restauro
    bloqueMensual.classList.remove('d-none');
    filas.forEach(f => f.classList.remove('d-none'));
}

function imprimirSeleccionadosMensual() {
    const bloqueGrupo = document.getElementById('bloque-grupo');
    const filas = document.querySelectorAll('.fila-imprimible-mensual');

    // oculto el otro bloque
    bloqueGrupo.classList.add('d-none');

    // oculto todas y muestro solo seleccionadas
    filas.forEach(f => f.classList.add('d-none'));
    document.querySelectorAll('.check-fila-mensual:checked').forEach(c => {
        c.closest('.fila-imprimible-mensual').classList.remove('d-none');
    });

    window.print();

    // restauro
    bloqueGrupo.classList.remove('d-none');
    filas.forEach(f => f.classList.remove('d-none'));
}

document.getElementById('checkAllGrupo').addEventListener('change', function () {
    document.querySelectorAll('.check-fila-grupo').forEach(c => c.checked = this.checked);
});

document.getElementById('checkAllMensual').addEventListener('change', function () {
    document.querySelectorAll('.check-fila-mensual').forEach(c => c.checked = this.checked);
});
</script>

@endsection
