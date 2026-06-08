@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">
                Programas del tablero
            </h3>
            <p class="text-muted mb-0">
                Desde acá se crean y configuran los programas dinámicos.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Volver al tablero
            </a>

            <a href="{{ route('programas.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Crear programa
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($programas->isEmpty())
        <div class="alert alert-light border text-center">
            No hay programas creados todavía.
            <br>
            <a href="{{ route('programas.create') }}" class="btn btn-primary btn-sm mt-3">
                <i class="fa-solid fa-plus"></i> Crear primer programa
            </a>
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Programa</th>
                                <th>Descripción</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Orden</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($programas as $programa)
                                <tr>
                                    <td class="fw-semibold">
                                        {{ $programa->nombre }}
                                    </td>

                                    <td class="text-muted">
                                        {{ $programa->descripcion ?: 'Sin descripción' }}
                                    </td>

                                    <td class="text-center">
                                        @if($programa->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{ $programa->orden }}
                                    </td>

                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('programas.bloques.index', $programa) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </a>

                                        <a href="{{ route('programas.edit', $programa) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fa-solid fa-gear"></i>
                                        </a>

                                        <form action="{{ route('programas.destroy', $programa) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Eliminar este programa y todos sus datos cargados?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
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
        </div>

        <div class="mt-3">
            {{ $programas->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection