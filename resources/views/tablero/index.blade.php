@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-secondary">
            Tablero de Anuncios
        </h2>

        <a href="{{ route('programas.index') }}" class="btn btn-primary btn-sm no-print">
            <i class="fa-solid fa-gear"></i> Configurar programas
        </a>
    </div>

    @if($programas->isEmpty())
        <div class="alert alert-light border text-center">
            Todavía no hay programas creados.
            <br>

            <a href="{{ route('programas.create') }}" class="btn btn-primary btn-sm mt-3">
                <i class="fa-solid fa-plus"></i> Crear primer programa
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($programas as $programa)
                @php
                    $colores = [
                        '#f34155',
                        '#4361ee',
                        '#48cae4',
                        '#80ed99',
                        '#f9c74f',
                        '#b388eb',
                        '#ff9f1c',
                        '#2ec4b6',
                        '#577590',
                    ];

                    $color = $colores[$loop->index % count($colores)];
                @endphp

                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('programas.bloques.index', $programa) }}" class="text-decoration-none">
                        <div class="card border rounded-3 shadow tablero-card bg-white position-relative h-100" style="--color: {{ $color }};">
                            <div class="barra-color"></div>

                            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center" style="height: 115px;">
                                <h5 class="mb-1 fw-semibold text-dark">
                                    {{ $programa->nombre }}
                                </h5>

                                {{-- @if($programa->descripcion)
                                    <small class="text-muted px-2">
                                        {{ \Illuminate\Support\Str::limit($programa->descripcion, 75) }}
                                    </small>
                                @else
                                    <small class="text-muted">
                                        Bloques imprimibles
                                    </small>
                                @endif --}}
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</div>

<style>
.tablero-card {
    transition: all 0.25s ease-in-out;
    background-color: #f9f9f9;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    border-left: 6px solid var(--color);
    overflow: hidden;
}

.tablero-card:hover {
    transform: translateY(-2px) scale(1.03);
    background-color: #f1f4f7;
    box-shadow: 0 10px 16px rgba(0,0,0,0.12);
}

.tablero-card .barra-color {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 6px;
    background-color: var(--color);
    border-top-left-radius: .375rem;
    border-bottom-left-radius: .375rem;
}

.tablero-card h5 {
    font-size: 1.15rem;
}
</style>
@endsection