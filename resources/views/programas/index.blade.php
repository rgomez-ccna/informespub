@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">
                Programas del tablero
            </h3>
            <p class="text-muted mb-0">
                Creá programas, configurá sus columnas y ordená cómo aparecen en el tablero.
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

    <div id="mensajeOrden" class="alert alert-success d-none">
        Orden actualizado correctamente.
    </div>

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
                                <th style="width: 50px;"></th>
                                <th>Programa</th>
                                {{-- <th>Descripción</th> --}}
                                <th class="text-center">Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="programasOrdenables">
                            @foreach($programas as $programa)
                                <tr data-id="{{ $programa->id }}" draggable="true" class="fila-programa">
                                    <td class="text-center text-muted mover-handle">
                                        <i class="fa-solid fa-grip-vertical"></i>
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $programa->nombre }}
                                    </td>
{{-- 
                                    <td class="text-muted">
                                        {{ $programa->descripcion ?: 'Sin descripción' }}
                                    </td> --}}

                                    <td class="text-center">
                                        @if($programa->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>

                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('programas.edit', $programa) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fa-solid fa-gear"></i> Configurar columnas
                                        </a>

                                        <form action="{{ route('programas.destroy', $programa) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Eliminar este programa y todos sus bloques, filas y datos cargados?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa-solid fa-trash"></i> Eliminar
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

<style>
.fila-programa {
    cursor: grab;
}

.fila-programa:active {
    cursor: grabbing;
}

.fila-arrastrando {
    opacity: .45;
}

.mover-handle {
    cursor: grab;
    font-size: 16px;
}

.fila-programa td {
    vertical-align: middle;
}
</style>

<script>
const tbody = document.getElementById('programasOrdenables');
const mensajeOrden = document.getElementById('mensajeOrden');

let filaArrastrando = null;

if (tbody) {
    tbody.querySelectorAll('tr').forEach(fila => {
        fila.addEventListener('dragstart', () => {
            filaArrastrando = fila;
            fila.classList.add('fila-arrastrando');
        });

        fila.addEventListener('dragend', () => {
            fila.classList.remove('fila-arrastrando');
            filaArrastrando = null;
            guardarOrden();
        });

        fila.addEventListener('dragover', e => {
            e.preventDefault();

            const filaActual = e.currentTarget;

            if (!filaArrastrando || filaActual === filaArrastrando) {
                return;
            }

            const rect = filaActual.getBoundingClientRect();
            const mitad = rect.top + rect.height / 2;

            if (e.clientY > mitad) {
                filaActual.after(filaArrastrando);
            } else {
                filaActual.before(filaArrastrando);
            }
        });
    });
}

function guardarOrden() {
    const programas = [...tbody.querySelectorAll('tr')].map(fila => fila.dataset.id);

    fetch("{{ route('programas.ordenar') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
        },
        body: JSON.stringify({ programas })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            mensajeOrden.classList.remove('d-none');

            setTimeout(() => {
                mensajeOrden.classList.add('d-none');
            }, 1600);
        }
    });
}
</script>
@endsection