@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 1180px;">

    {{-- ENCABEZADO --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-1 fw-bold text-dark">
                Editar programa y columnas
            </h3>
            <div class="text-muted small">
                Cambia el nombre de la tarjeta y define que columnas tendran sus planillas.
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('tablero.index') }}" class="btn btn-light border btn-sm">
                <i class="fa-solid fa-table-columns"></i> Ver tablero
            </a>

            <a href="{{ route('programas.bloques.index', $programa) }}" class="btn btn-success btn-sm">
                <i class="fa-solid fa-folder-open"></i> Ver planillas
            </a>

            <a href="{{ route('programas.index') }}" class="btn btn-light border btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Volver a programas
            </a>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success py-2 small">
            <i class="fa-solid fa-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2 small">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>
            Revisá los campos marcados.
        </div>
    @endif

    @php
        $camposExtra = $programa->campos
            ->where('visible_en_listado', true)
            ->where('tipo', '!=', 'fecha');

        $camposConfigurados = $programa->campos
            ->where('tipo', '!=', 'fecha');

        $siguienteOrden = ($programa->campos->max('orden') ?? 1) + 1;
    @endphp

    {{-- AJUSTES RÁPIDOS --}}
    <div class="card ajuste-card mb-3">
        <div class="card-body py-3">
            <form action="{{ route('programas.update', $programa) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-2 align-items-center">
                    <div class="col-lg-7">
                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                            <label class="form-label fw-semibold small mb-0 ajuste-label">
                                Nombre del programa
                            </label>

                            <input type="text"
                                   name="nombre"
                                   class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $programa->nombre) }}"
                                   placeholder="Ej: Limpieza mensual"
                                   required>
                        </div>

                        @error('nombre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-3">
                        <div class="visible-inline">
                            <div>
                                <span class="fw-semibold small">Mostrar</span>
                                <span class="text-muted mini-text ms-1">en tablero</span>
                            </div>

                            <div class="form-check form-switch m-0">
                                <input type="checkbox"
                                       name="activo"
                                       value="1"
                                       class="form-check-input"
                                       id="activo"
                                       {{ old('activo', $programa->activo) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 d-grid">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-save"></i> Guardar programa
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- BLOQUE PRINCIPAL --}}
    <div class="card sol-card">
        <div class="card-header sol-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <div class="fw-bold text-dark">
                    Columnas de las planillas
                </div>
                <small class="text-muted">
                    Dia y fecha ya vienen por defecto. Agrega solo las columnas que vas a completar en cada fila.
                </small>
            </div>

            <button type="button"
                    class="btn btn-success btn-sm px-3"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAgregarColumna">
                <i class="fa-solid fa-plus"></i> Agregar columna
            </button>
        </div>

        <div class="card-body">
            <div class="row g-4">

                {{-- COLUMNAS --}}
                <div class="col-lg-5">
                    <div class="section-title mb-2">
                        <div class="fw-bold text-dark">
                            Columnas de la tabla
                        </div>
                        <small class="text-muted">
                            Estas columnas aparecen al cargar filas, en la vista de la planilla y en el PDF.
                        </small>
                    </div>

                    <div class="table-responsive border rounded-4 overflow-hidden columnas-box">
                        <table class="table table-hover align-middle mb-0 columnas-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">Orden</th>
                                    <th>Columna</th>
                                    <th style="width: 105px;">Tipo</th>
                                    <th class="text-end" style="width: 60px;"></th>
                                </tr>
                            </thead>

                            <tbody>
                                {{-- DÍA AUTOMÁTICO --}}
                                <tr class="fila-sistema">
                                    <td>
                                        <span class="orden-pill sistema">Auto</span>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">Día</div>
                                        <div class="small text-muted">Se calcula solo según la fecha.</div>
                                    </td>

                                    <td>
                                        <span class="badge bg-primary">Sistema</span>
                                    </td>

                                    <td class="text-end">
                                        <span class="text-muted small">Fijo</span>
                                    </td>
                                </tr>

                                {{-- FECHA FIJA --}}
                                <tr class="fila-sistema">
                                    <td>
                                        <span class="orden-pill sistema">1</span>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">Fecha</div>
                                        <div class="small text-muted">Ordena filas y arma el calendario.</div>
                                    </td>

                                    <td>
                                        <span class="badge bg-primary">Fecha</span>
                                    </td>

                                    <td class="text-end">
                                        <span class="text-muted small">Fijo</span>
                                    </td>
                                </tr>

                                {{-- COLUMNAS CREADAS --}}
                                @foreach($camposConfigurados as $campo)
                                    @php
                                        $tipoNombre = [
                                            'texto' => 'Texto',
                                            'textarea' => 'Texto largo',
                                            'numero' => 'Número',
                                            'hora' => 'Hora',
                                            'select' => 'Lista',
                                            'checkbox' => 'Sí / No',
                                        ][$campo->tipo] ?? $campo->tipo;
                                    @endphp

                                    <tr>
                                        <td>
                                            <span class="orden-pill">
                                                {{ $campo->orden }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="fw-semibold">
                                                {{ $campo->nombre }}
                                            </div>

                                            @if($campo->tipo === 'select' && !empty($campo->opciones))
                                                <div class="small text-muted text-truncate opciones-text">
                                                    {{ implode(', ', $campo->opciones) }}
                                                </div>
                                            @else
                                                <div class="small text-muted">
                                                    Se completa al cargar cada fila.
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge bg-dark">
                                                {{ $tipoNombre }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <form action="{{ route('programas.campos.destroy', [$programa, $campo]) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Eliminar esta columna? También se eliminarán sus datos cargados.')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($camposConfigurados->isEmpty())
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-row">
                                                <div class="fw-semibold text-dark mb-1">
                                                    Todavía no agregaste columnas propias.
                                                </div>

                                                <div class="text-muted small mb-3">
                                                    Ej: Grupo, Encargado, Horario u Observación.
                                                </div>

                                                <button type="button"
                                                        class="btn btn-success btn-sm px-4"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalAgregarColumna">
                                                    <i class="fa-solid fa-plus"></i> Agregar primera columna
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- VISTA PREVIA --}}
                <div class="col-lg-7">
                    <div class="section-title mb-2">
                        <div class="fw-bold text-dark">
                            Vista previa
                        </div>
                        <small class="text-muted">
                            Asi se vera una planilla cuando cargues filas.
                        </small>
                    </div>

                    <div class="preview-box">
                        <div class="preview-title text-center">
                            <h5>{{ strtoupper($programa->nombre) }}</h5>
                            <small>Planilla de ejemplo</small>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-bordered text-center align-middle mb-0 preview-table">
                                <thead>
                                    <tr>
                                        <th>DÍA</th>
                                        <th>FECHA</th>

                                        @foreach($camposExtra as $campo)
                                            <th>{{ strtoupper($campo->nombre) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">MIÉRCOLES</td>
                                        <td><strong>10/06/2026</strong></td>

                                        @foreach($camposExtra as $campo)
                                            <td class="text-muted">
                                                @if($campo->tipo === 'hora')
                                                    19:30
                                                @elseif($campo->tipo === 'numero')
                                                    1
                                                @elseif($campo->tipo === 'checkbox')
                                                    Sí
                                                @elseif($campo->tipo === 'select' && !empty($campo->opciones))
                                                    {{ $campo->opciones[0] ?? 'Ejemplo' }}
                                                @else
                                                    Ejemplo
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        <td class="fw-semibold">JUEVES</td>
                                        <td><strong>11/06/2026</strong></td>

                                        @foreach($camposExtra as $campo)
                                            <td class="text-muted">
                                                @if($campo->tipo === 'hora')
                                                    20:00
                                                @elseif($campo->tipo === 'numero')
                                                    2
                                                @elseif($campo->tipo === 'checkbox')
                                                    No
                                                @elseif($campo->tipo === 'select' && !empty($campo->opciones))
                                                    {{ $campo->opciones[1] ?? ($campo->opciones[0] ?? 'Ejemplo') }}
                                                @else
                                                    Ejemplo
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if($camposExtra->isEmpty())
                            <div class="preview-empty">
                                Agregá columnas para completar la vista previa.
                            </div>
                        @endif
                    </div>

                    <div class="info-line mt-3">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Dia y fecha son columnas fijas del sistema. Las demas las definis vos.</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- MODAL AGREGAR COLUMNA --}}
<div class="modal fade" id="modalAgregarColumna" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('programas.campos.store', $programa) }}" method="POST">
                @csrf

                <div class="modal-header">
                    <div>
                        <h6 class="modal-title fw-bold mb-0">
                            Agregar columna a la tabla
                        </h6>
                        <small class="text-muted">
                            Se agregara despues de Dia y Fecha.
                        </small>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nombre de la columna</label>
                        <input type="text"
                               name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               placeholder="Ej: Grupo, Encargado, Horario, Observacion"
                               value="{{ old('nombre') }}"
                               required>

                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold small">Tipo de dato</label>
                            <select name="tipo"
                                    id="tipoCampo"
                                    class="form-select @error('tipo') is-invalid @enderror"
                                    required>
                                <option value="texto" {{ old('tipo') === 'texto' ? 'selected' : '' }}>Texto simple</option>
                                <option value="textarea" {{ old('tipo') === 'textarea' ? 'selected' : '' }}>Texto largo</option>
                                <option value="numero" {{ old('tipo') === 'numero' ? 'selected' : '' }}>Número</option>
                                <option value="hora" {{ old('tipo') === 'hora' ? 'selected' : '' }}>Hora</option>
                                <option value="select" {{ old('tipo') === 'select' ? 'selected' : '' }}>Lista de opciones</option>
                                <option value="checkbox" {{ old('tipo') === 'checkbox' ? 'selected' : '' }}>Sí / No</option>
                            </select>

                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold small">Orden</label>
                            <input type="number"
                                   name="orden"
                                   class="form-control"
                                   value="{{ old('orden', $siguienteOrden) }}">
                        </div>
                    </div>

                    <div class="alert alert-light border small mb-3" id="ayudaTipo">
                        Texto simple: para nombres, lugares o asignaciones.
                    </div>

                    <div class="mb-3" id="opcionesSelectBox" style="display:none;">
                        <label class="form-label fw-semibold small">Opciones de la lista</label>
                        <textarea name="opciones"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Una opción por línea. Ej:
Grupo 1
Grupo 2
Grupo 3">{{ old('opciones') }}</textarea>
                    </div>

                    <label class="opcion-box">
                        <input type="checkbox"
                               name="visible_en_listado"
                               value="1"
                               {{ old('visible_en_listado', true) ? 'checked' : '' }}>

                        <span>
                            <strong>Mostrar en la planilla y en el PDF</strong>
                            <small>Si esta activo, esta columna se vera al cargar datos e imprimir.</small>
                        </span>
                    </label>

                    <input type="hidden" name="buscable" value="0">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-plus"></i> Agregar columna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
body {
    background: #f4f6fb;
}

.ajuste-card {
    border: 0;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
    background: #ffffff;
}

.sol-card {
    border: 0;
    border-radius: 18px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
    overflow: hidden;
}

.sol-card-header {
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fc 100%) !important;
    border-bottom: 1px solid #edf0f5;
    padding: 16px 18px;
}

.section-title {
    padding-left: 2px;
}

.ajuste-label {
    width: 74px;
    min-width: 74px;
}

.mini-text {
    font-size: 11px;
}

.visible-inline {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #e1e6ef;
    border-radius: 10px;
    padding: 6px 10px;
    background: #ffffff;
    min-height: 32px;
}

.columnas-box {
    background: #fff;
}

.columnas-table th,
.columnas-table td {
    font-size: 12.5px;
}

.columnas-table td {
    padding-top: 12px;
    padding-bottom: 12px;
}

.fila-sistema {
    background: #f8fbff;
}

.fila-sistema td {
    border-bottom-color: #e4ecf7;
}

.orden-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 26px;
    border-radius: 999px;
    background: #f1f3f5;
    color: #495057;
    font-size: 12px;
    font-weight: 700;
}

.orden-pill.sistema {
    background: #eef3ff;
    color: #2454d6;
}

.opciones-text {
    max-width: 190px;
}

.preview-box {
    border: 1px solid #dcd6ef;
    border-radius: 16px;
    padding: 14px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfaff 100%);
    min-height: 100%;
}

.preview-title {
    border: 2px solid #6b5b95;
    border-radius: 12px;
    padding: 10px;
    background: #ffffff;
}

.preview-title h5 {
    margin: 0;
    color: #3d315b;
    font-size: 15px;
    font-weight: 800;
    letter-spacing: .4px;
}

.preview-title small {
    color: #5f527f;
    font-weight: 600;
}

.preview-table {
    min-width: 640px;
}

.preview-table thead th {
    background: #6b5b95;
    color: #fff;
    font-size: 11px;
    vertical-align: middle;
}

.preview-table td {
    font-size: 11px;
}

.preview-table tbody tr:nth-child(even) {
    background: #f8f6ff;
}

.preview-empty {
    text-align: center;
    color: #6c757d;
    font-size: 12px;
    padding: 16px 8px 4px;
}

.info-line {
    display: flex;
    align-items: flex-start;
    gap: 7px;
    color: #5f6368;
    font-size: 12px;
}

.info-line i {
    color: #4361ee;
    margin-top: 2px;
}

.empty-row {
    text-align: center;
    padding: 34px 20px;
    background: #ffffff;
}

.opcion-box {
    display: flex;
    gap: 9px;
    align-items: flex-start;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 11px;
    cursor: pointer;
    height: 100%;
    background: #fff;
    transition: all .2s ease;
}

.opcion-box:hover {
    border-color: #6b5b95;
    background: #faf9ff;
}

.opcion-box input {
    margin-top: 4px;
}

.opcion-box strong {
    display: block;
    font-size: 13px;
}

.opcion-box small {
    display: block;
    color: #6c757d;
    font-size: 11px;
    line-height: 1.25;
}

.modal-content {
    border-radius: 18px;
}

@media (max-width: 991px) {
    .preview-table {
        min-width: 700px;
    }
}

@media (max-width: 768px) {
    .ajuste-label {
        width: auto;
        min-width: auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoCampo = document.getElementById('tipoCampo');
    const ayudaTipo = document.getElementById('ayudaTipo');
    const opcionesSelectBox = document.getElementById('opcionesSelectBox');

    if (!tipoCampo || !ayudaTipo || !opcionesSelectBox) return;

    const ayudas = {
        texto: '<strong>Texto simple:</strong> para nombres, lugares, grupos o asignaciones.',
        textarea: '<strong>Texto largo:</strong> para observaciones o comentarios.',
        numero: '<strong>Número:</strong> para cantidades o valores numéricos.',
        hora: '<strong>Hora:</strong> para horarios, por ejemplo 09:00 o 19:30.',
        select: '<strong>Lista de opciones:</strong> para elegir entre valores fijos.',
        checkbox: '<strong>Sí / No:</strong> para marcar una opción simple.'
    };

    function actualizarAyudaTipo() {
        const tipo = tipoCampo.value;

        ayudaTipo.innerHTML = ayudas[tipo] || '';
        opcionesSelectBox.style.display = tipo === 'select' ? 'block' : 'none';
    }

    tipoCampo.addEventListener('change', actualizarAyudaTipo);
    actualizarAyudaTipo();

    @if($errors->has('nombre') || $errors->has('tipo'))
        const modal = new bootstrap.Modal(document.getElementById('modalAgregarColumna'));
        modal.show();
    @endif
});
</script>
@endsection
