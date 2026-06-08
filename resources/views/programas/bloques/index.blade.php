@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">{{ $programa->nombre }}</h3>
            <p class="text-muted mb-0">
                Bloques imprimibles del programa
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Tablero
            </a>

            <a href="{{ route('programas.edit', $programa) }}" class="btn btn-outline-warning btn-sm">
                <i class="fa-solid fa-gear"></i> Configurar
            </a>

            <a href="{{ route('programas.bloques.create', $programa) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Nuevo bloque
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($bloques->isEmpty())
        <div class="alert alert-light border text-center">
            Todavía no hay bloques creados para este programa.
            <br>
            <a href="{{ route('programas.bloques.create', $programa) }}" class="btn btn-primary btn-sm mt-3">
                <i class="fa-solid fa-plus"></i> Crear primer bloque
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($bloques as $bloque)
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 bloque-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h5 class="fw-bold mb-0 text-dark">
                                    {{ $bloque->nombre }}
                                </h5>

                                @if($bloque->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </div>

                            @if($bloque->descripcion)
                                <p class="text-muted small mb-2">
                                    {{ $bloque->descripcion }}
                                </p>
                            @endif

                            <div class="small text-muted mb-3">
                                @if($bloque->fecha_inicio || $bloque->fecha_fin)
                                    <i class="fa-solid fa-calendar-days"></i>

                                    @if($bloque->fecha_inicio)
                                        {{ $bloque->fecha_inicio->format('d/m/Y') }}
                                    @endif

                                    @if($bloque->fecha_fin)
                                        al {{ $bloque->fecha_fin->format('d/m/Y') }}
                                    @endif
                                @else
                                    Sin rango de fechas
                                @endif
                            </div>

                            @if($bloque->observaciones)
                                <div class="small text-muted border rounded p-2 bg-light mb-3">
                                    <strong>Observación:</strong><br>
                                    {{ \Illuminate\Support\Str::limit($bloque->observaciones, 90) }}
                                </div>
                            @endif

                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('programas.bloques.registros.index', [$programa, $bloque]) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-table"></i> Entrar
                                </a>

                                <a href="{{ route('programas.bloques.edit', [$programa, $bloque]) }}"
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="fa-solid fa-edit"></i>
                                </a>

                                <form action="{{ route('programas.bloques.destroy', [$programa, $bloque]) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este bloque y todas sus filas?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $bloques->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

<style>
.bloque-card {
    border-left: 6px solid #6b5b95 !important;
    transition: all .2s ease-in-out;
}

.bloque-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(0,0,0,.12) !important;
}
</style>
@endsection