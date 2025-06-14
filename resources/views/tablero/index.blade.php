@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center fw-bold text-secondary">Tablero de Anuncios</h2>

    <div class="row g-3">

        @php
           $departamentos = [
                ['nombre' => 'Reunión Vida y Ministerio', 'ruta' => 'vidaministerio.index', 'color' => '#f34155'],
                ['nombre' => 'Reunión Pública: Presidente y Lector', 'ruta' => 'publica.index', 'color' => '#4361ee'],
                ['nombre' => 'Programa de Salidas al Ministerio', 'ruta' => 'ministerio.index', 'color' => '#48cae4'],
                ['nombre' => 'Limpieza', 'ruta' => 'limpieza.index', 'color' => '#80ed99'],
                ['nombre' => 'Discursos Públicos (Salidas y Visitas)', 'ruta' => 'discursos.index', 'color' => '#f9c74f'],
                ['nombre' => 'Acomodadores', 'ruta' => 'acomodadores.index', 'color' => '#b388eb'],

                // Desactivados (gris y sin link)
                ['nombre' => 'Anuncios', 'ruta' => null, 'color' => '#d3d3d3', 'disabled' => true],
                ['nombre' => 'Informe Mensual de Cuentas', 'ruta' => null, 'color' => '#d3d3d3', 'disabled' => true],
                ['nombre' => 'Territorio', 'ruta' => null, 'color' => '#d3d3d3', 'disabled' => true],
            
            ];

        @endphp

       @foreach($departamentos as $d)
            <div class="col-md-6 col-lg-4">
                @if(!empty($d['disabled']))
                    <div class="card border rounded-3 shadow tablero-card bg-white position-relative opacity-50" style="--color: {{ $d['color'] }};">
                        <div class="barra-color"></div>
                        <div class="card-body d-flex justify-content-center align-items-center text-center" style="height: 100px; cursor: not-allowed;">
                            <h5 class="mb-0 fw-semibold text-muted">{{ $d['nombre'] }}</h5>
                        </div>
                    </div>
                @else
                    <a href="{{ route($d['ruta']) }}" class="text-decoration-none">
                        <div class="card border rounded-3 shadow tablero-card bg-white position-relative" style="--color: {{ $d['color'] }};">
                            <div class="barra-color"></div>
                            <div class="card-body d-flex justify-content-center align-items-center text-center" style="height: 100px;">
                                <h5 class="mb-0 fw-semibold text-dark">{{ $d['nombre'] }}</h5>
                            </div>
                        </div>
                    </a>
                @endif
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
