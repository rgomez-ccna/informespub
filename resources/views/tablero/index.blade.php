@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center fw-bold text-secondary">Tablero de Anuncios</h2>

    <div class="row g-3">

        @php
            $departamentos = [
    ['nombre' => 'Limpieza', 'ruta' => 'limpieza.index', 'color' => '#9fc131'],
    ['nombre' => 'Reunión Vida y Ministerio', 'ruta' => 'vidaministerio.index', 'color' => '#00a8b5'],
    ['nombre' => 'Reunión Pública: Presidente y Lector', 'ruta' => 'publica.index', 'color' => '#254463'],
    ['nombre' => 'Discursos Públicos (Salidas y Visitas)', 'ruta' => 'discursos.index', 'color' => '#5c5d8c'],
    ['nombre' => 'Anuncios', 'ruta' => 'tablero.anuncios', 'color' => '#007a33'],
    ['nombre' => 'Acomodadores', 'ruta' => 'acomodadores.index', 'color' => '#9055a2'],
    ['nombre' => 'Programa de Salidas al Ministerio', 'ruta' => 'ministerio.index', 'color' => '#1d6176'],
    ['nombre' => 'Informe Mensual de Cuentas', 'ruta' => 'tablero.cuentas', 'color' => '#5e412f'],
    ['nombre' => 'Territorio', 'ruta' => 'tablero.territorio', 'color' => '#008891'],
];

        @endphp

        @foreach($departamentos as $d)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route($d['ruta']) }}" class="text-decoration-none">
                <div class="card border rounded-3 shadow tablero-card bg-white position-relative" style="--color: {{ $d['color'] }};">
                    <div class="barra-color"></div>
                    <div class="card-body d-flex justify-content-center align-items-center text-center" style="height: 100px;">
                        <h5 class="mb-0 fw-semibold text-dark">{{ $d['nombre'] }}</h5>
                    </div>
                </div>
            </a>
        </div>
        @endforeach

    </div>
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
    transform: translateY(-2px) scale(1.05);
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
    font-size: 1.2rem;
}
</style>
@endsection
