@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 1250px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Calificaciones</h3>
            <p class="text-muted mb-0">
                Definí qué publicadores pueden recibir cada asignación de Vida y Ministerio.
            </p>
        </div>

        <a href="{{ route('vida-ministerio.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2">
            Revisá los datos enviados.
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">

            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Buscar publicador</label>
                    <input type="text"
                           id="buscarPublicador"
                           class="form-control form-control-sm"
                           placeholder="Buscar por nombre, grupo o privilegio...">
                </div>

                <div class="col-md-6 text-md-end">
                    <div class="small text-muted">
                        Marcá solo las asignaciones que realmente puede recibir cada publicador.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <form method="POST" action="{{ route('vida-ministerio.calificaciones.store') }}">
        @csrf

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">

                <div class="table-responsive tabla-calificaciones">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="min-width: 240px;">Publicador</th>
                                <th style="min-width: 110px;">Grupo</th>
                                <th class="text-center" style="min-width: 80px;">Fila</th>

                                @foreach($tipos as $tipo => $label)
                                    <th class="text-center" style="min-width: 105px;">
                                        <div class="d-flex flex-column align-items-center gap-1">
                                            <small class="fw-semibold">{{ $label }}</small>

                                            <input type="checkbox"
                                                   class="form-check-input check-columna"
                                                   data-tipo="{{ $tipo }}"
                                                   title="Marcar/desmarcar columna">
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($publicadores as $publicador)
                                @php
                                    $marcadas = $calificaciones->get($publicador->id, []);

                                    $textoFiltro = strtolower(trim(
                                        ($publicador->nombre ?? '') . ' ' .
                                        ($publicador->grupo ?? '') . ' ' .
                                        ($publicador->rol ?? '') . ' ' .
                                        ($publicador->estado ?? '') . ' ' .
                                        ($publicador->anciano ? 'anciano ' : '') .
                                        ($publicador->sv ? 'siervo ministerial ' : '') .
                                        ($publicador->precursor ? 'precursor ' : '')
                                    ));
                                @endphp

                                <tr class="fila-publicador" data-filtro="{{ $textoFiltro }}">
                                    <td>
                                        <div class="fw-semibold">{{ $publicador->nombre }}</div>

                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @if($publicador->anciano)
                                                <span class="badge bg-primary">Anciano</span>
                                            @endif

                                            @if($publicador->sv)
                                                <span class="badge bg-info text-dark">SM</span>
                                            @endif

                                            @if($publicador->precursor)
                                                <span class="badge bg-success">Precursor</span>
                                            @endif

                                            @if($publicador->rol)
                                                <span class="badge bg-light text-dark border">{{ $publicador->rol }}</span>
                                            @endif

                                            @if($publicador->estado && $publicador->estado !== 'activo')
                                                <span class="badge bg-warning text-dark">{{ $publicador->estado }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <span class="small">{{ $publicador->grupo ?: '-' }}</span>
                                    </td>

                                    <td class="text-center">
                                        <input type="checkbox"
                                               class="form-check-input check-fila"
                                               title="Marcar/desmarcar fila">
                                    </td>

                                    @foreach($tipos as $tipo => $label)
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   class="form-check-input check-calificacion check-tipo-{{ $tipo }}"
                                                   name="calificaciones[{{ $publicador->id }}][]"
                                                   value="{{ $tipo }}"
                                                   {{ in_array($tipo, $marcadas, true) ? 'checked' : '' }}>
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($tipos) + 3 }}" class="text-center text-muted py-4">
                                        No hay publicadores cargados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('vida-ministerio.index') }}" class="btn btn-light border btn-sm">
                Cancelar
            </a>

            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-save"></i> Guardar calificaciones
            </button>
        </div>
    </form>

</div>

<style>
.tabla-calificaciones {
    max-height: 72vh;
    overflow: auto;
}

.tabla-calificaciones table {
    font-size: 13px;
}

.tabla-calificaciones thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #f8f9fa;
    vertical-align: middle;
}

.tabla-calificaciones tbody tr:hover {
    background: #fafafa;
}

.form-check-input {
    cursor: pointer;
}

.badge {
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buscador = document.getElementById('buscarPublicador');

    buscador?.addEventListener('input', function () {
        const valor = this.value.trim().toLowerCase();

        document.querySelectorAll('.fila-publicador').forEach(fila => {
            const texto = fila.dataset.filtro || '';
            fila.style.display = texto.includes(valor) ? '' : 'none';
        });
    });

    document.querySelectorAll('.check-fila').forEach(checkFila => {
        checkFila.addEventListener('change', function () {
            const fila = this.closest('tr');

            fila.querySelectorAll('.check-calificacion').forEach(check => {
                check.checked = this.checked;
            });
        });
    });

    document.querySelectorAll('.check-columna').forEach(checkColumna => {
        checkColumna.addEventListener('change', function () {
            const tipo = this.dataset.tipo;

            document.querySelectorAll('.check-tipo-' + tipo).forEach(check => {
                const fila = check.closest('tr');

                if (fila.style.display !== 'none') {
                    check.checked = this.checked;
                }
            });
        });
    });
});
</script>
@endsection