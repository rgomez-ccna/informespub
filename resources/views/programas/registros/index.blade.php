@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">{{ $programa->nombre }}</h3>
            <p class="text-muted mb-0">
                {{ $bloque->nombre }}
                @if($bloque->fecha_inicio || $bloque->fecha_fin)
                    ·
                    @if($bloque->fecha_inicio)
                        {{ $bloque->fecha_inicio->format('d/m/Y') }}
                    @endif
                    @if($bloque->fecha_fin)
                        al {{ $bloque->fecha_fin->format('d/m/Y') }}
                    @endif
                @endif
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('programas.bloques.index', $programa) }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Bloques
            </a>

            <a href="{{ route('programas.bloques.edit', [$programa, $bloque]) }}" class="btn btn-outline-warning btn-sm">
                <i class="fa-solid fa-gear"></i> Editar bloque
            </a>

            <a href="{{ route('programas.bloques.registros.create', [$programa, $bloque]) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Agregar fila
            </a>

            <a href="{{ route('programas.bloques.registros.pdf', [$programa, $bloque]) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($programa->campos->isEmpty())
        <div class="alert alert-warning">
            Este programa todavía no tiene campos configurados.
            <a href="{{ route('programas.edit', $programa) }}">Configurar campos</a>
        </div>
    @else

        <div class="banner-programa text-center mb-3">
            <h1 class="titulo">{{ strtoupper($programa->nombre) }}</h1>

            <h5 class="subtitulo">
                {{ strtoupper($bloque->nombre) }}
                @if($bloque->descripcion)
                    · {{ strtoupper($bloque->descripcion) }}
                @endif
            </h5>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="tabla-programa text-center align-middle mb-0">
                        <thead>
                            <tr>
                                <th>DÍA</th>
                                @foreach($programa->campos->where('visible_en_listado', true) as $campo)
                                    <th>{{ strtoupper($campo->nombre) }}</th>
                                @endforeach
                                <th style="width: 110px;">ACCIONES</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($registros as $registro)
                                @php
                                    $valores = $registro->valores->keyBy('programa_campo_id');
                                    $esEspecial = $registro->tipo_fila !== 'normal';
                                    $cantidadColumnas = $programa->campos->where('visible_en_listado', true)->count() + 2;
                                @endphp

                                @if($esEspecial)
                                    <tr class="fila-especial">
                                        <td colspan="{{ $cantidadColumnas }}">
                                            {{ $registro->texto_especial ?: strtoupper($registro->tipo_fila) }}

                                            <span class="float-end">
                                                <a href="{{ route('programas.bloques.registros.edit', [$programa, $bloque, $registro]) }}"
                                                   class="btn btn-sm btn-outline-warning">
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
                                            </span>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ $registro->fecha ? strtoupper($registro->fecha->translatedFormat('l')) : '-' }}
                                        </td>

                                        @foreach($programa->campos->where('visible_en_listado', true) as $campo)
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

                                        <td class="text-nowrap">
                                            <a href="{{ route('programas.bloques.registros.edit', [$programa, $bloque, $registro]) }}"
                                               class="btn btn-sm btn-outline-warning">
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
                                @endif
                            @empty
                                <tr>
                                    <td colspan="{{ $programa->campos->where('visible_en_listado', true)->count() + 2 }}"
                                        class="text-center text-muted py-4">
                                        Todavía no hay filas cargadas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        @if($bloque->observaciones)
            <div class="mt-4 px-3 py-2 texto-final">
                {!! nl2br(e($bloque->observaciones)) !!}
            </div>
        @endif

        <div class="mt-3">
            {{ $registros->links('pagination::bootstrap-5') }}
        </div>

    @endif

</div>

<style>
.banner-programa {
    border: 3px solid #6b5b95;
    border-radius: 8px;
    padding: 14px;
    background: #f8f6ff;
}

.banner-programa .titulo {
    font-size: 28px;
    font-weight: 800;
    margin: 0;
    color: #3d315b;
}

.banner-programa .subtitulo {
    font-size: 15px;
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
    font-size: 13px;
    padding: 10px;
    border: 1px solid #59497d;
}

.tabla-programa td {
    font-size: 13px;
    padding: 9px;
    border: 1px solid #ddd;
}

.tabla-programa tbody tr:nth-child(even) {
    background: #f8f6ff;
}

.fila-especial td {
    background: #fff7d6 !important;
    color: #5c4700;
    font-weight: 700;
    text-align: center;
}

.texto-final {
    font-size: 13px;
    border: 2px solid #6b5b95;
    border-radius: 6px;
    background: #fff;
}
</style>
@endsection