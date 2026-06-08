@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 1220px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Configurar programa</h3>
            <p class="text-muted mb-0">
                Armá las columnas que tendrá este programa en el tablero y en el PDF.
            </p>
        </div>

        <a href="{{ route('programas.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            Revisá los campos marcados.
        </div>
    @endif

    <div class="row g-4">

        {{-- PANEL IZQUIERDO --}}
        <div class="col-lg-4">

            {{-- DATOS DEL PROGRAMA --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom fw-bold text-dark">
                    1. Datos del programa
                </div>

                <div class="card-body">
                    <form action="{{ route('programas.update', $programa) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre</label>
                            <input type="text"
                                   name="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $programa->nombre) }}"
                                   placeholder="Ej: Acomodadores"
                                   required>

                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea name="descripcion"
                                      class="form-control @error('descripcion') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Opcional. Ej: Programa mensual de asignaciones">{{ old('descripcion', $programa->descripcion) }}</textarea>

                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox"
                                   name="activo"
                                   value="1"
                                   class="form-check-input"
                                   id="activo"
                                   {{ old('activo', $programa->activo) ? 'checked' : '' }}>

                            <label for="activo" class="form-check-label">
                                Mostrar en tablero
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-save"></i> Guardar programa
                        </button>
                    </form>
                </div>
            </div>

            {{-- AYUDA SIMPLE --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom fw-bold text-dark">
                    ¿Qué estoy creando?
                </div>

                <div class="card-body">
                    <div class="paso-ayuda mb-3">
                        <div class="numero-ayuda">1</div>
                        <div>
                            <strong>Programa</strong>
                            <div class="text-muted small">La tarjeta del tablero. Ej: Limpieza.</div>
                        </div>
                    </div>

                    <div class="paso-ayuda mb-3">
                        <div class="numero-ayuda">2</div>
                        <div>
                            <strong>Columnas</strong>
                            <div class="text-muted small">Los datos que tendrá la tabla. Ej: Fecha, Grupo, Observaciones.</div>
                        </div>
                    </div>

                    <div class="paso-ayuda">
                        <div class="numero-ayuda">3</div>
                        <div>
                            <strong>Bloques y filas</strong>
                            <div class="text-muted small">Se cargan después desde el tablero.</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- PANEL DERECHO --}}
        <div class="col-lg-8">

            {{-- VISTA PREVIA --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark">Vista previa</div>
                        <small class="text-muted">Así se verá la tabla cuando cargues datos.</small>
                    </div>

                    <span class="badge bg-primary">Ejemplo</span>
                </div>

                <div class="card-body">
                    <div class="preview-programa">
                        <div class="preview-banner text-center mb-3">
                            <h5>{{ strtoupper($programa->nombre) }}</h5>
                            <small>Ejemplo de bloque</small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center align-middle mb-0 preview-table">
                                <thead>
                                    <tr>
                                        <th>DÍA</th>
                                        <th>FECHA</th>

                                        @forelse($programa->campos->where('visible_en_listado', true) as $campo)
                                            @if($campo->tipo !== 'fecha')
                                                <th>{{ strtoupper($campo->nombre) }}</th>
                                            @endif
                                        @empty
                                            <th>COLUMNA 1</th>
                                            <th>COLUMNA 2</th>
                                        @endforelse
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">MIÉRCOLES</td>
                                        <td><strong>10/06/2026</strong></td>

                                        @forelse($programa->campos->where('visible_en_listado', true) as $campo)
                                            @if($campo->tipo !== 'fecha')
                                                <td class="text-muted">Ejemplo</td>
                                            @endif
                                        @empty
                                            <td class="text-muted">Dato</td>
                                            <td class="text-muted">Dato</td>
                                        @endforelse
                                    </tr>

                                    <tr>
                                        <td class="fw-semibold">JUEVES</td>
                                        <td><strong>11/06/2026</strong></td>

                                        @forelse($programa->campos->where('visible_en_listado', true) as $campo)
                                            @if($campo->tipo !== 'fecha')
                                                <td class="text-muted">Ejemplo</td>
                                            @endif
                                        @empty
                                            <td class="text-muted">Dato</td>
                                            <td class="text-muted">Dato</td>
                                        @endforelse
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(!$programa->campos->contains('tipo', 'fecha'))
                        <div class="alert alert-warning mt-3 mb-0 small">
                            <strong>Recomendado:</strong> agregá una columna tipo <strong>Fecha</strong>. Sirve para ordenar filas y mostrar el día automáticamente.
                        </div>
                    @endif
                </div>
            </div>

            {{-- AGREGAR COLUMNA --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="fw-bold text-dark">2. Agregar columna</div>
                    <small class="text-muted">Cada columna será un dato que se cargará en cada fila.</small>
                </div>

                <div class="card-body">
                    <form action="{{ route('programas.campos.store', $programa) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-semibold">Nombre de la columna</label>
                                <input type="text"
                                       name="nombre"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       placeholder="Ej: Fecha, Acceso 1, Auditorio"
                                       required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Tipo de dato</label>
                                <select name="tipo" id="tipoCampo" class="form-select" required>
                                    <option value="texto">Texto simple</option>
                                    <option value="textarea">Texto largo</option>
                                    <option value="numero">Número</option>
                                    <option value="fecha">Fecha</option>
                                    <option value="hora">Hora</option>
                                    <option value="select">Lista de opciones</option>
                                    <option value="checkbox">Sí / No</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Orden</label>
                                <input type="number"
                                       name="orden"
                                       class="form-control"
                                       value="{{ ($programa->campos->max('orden') ?? 0) + 1 }}">
                            </div>
                        </div>

                        <div class="alert alert-light border small mb-3" id="ayudaTipo">
                            Texto simple: para nombres, lugares o asignaciones.
                        </div>

                        <div class="mb-3" id="opcionesSelectBox" style="display:none;">
                            <label class="form-label fw-semibold">Opciones de la lista</label>
                            <textarea name="opciones"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Una opción por línea. Ej:
Grupo 1
Grupo 2
Grupo 3"></textarea>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="opcion-card">
                                    <input type="checkbox" name="obligatorio" value="1">
                                    <span>
                                        <strong>Obligatorio</strong>
                                        <small>No permite dejar vacío.</small>
                                    </span>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="opcion-card">
                                    <input type="checkbox" name="visible_en_listado" value="1" checked>
                                    <span>
                                        <strong>Visible</strong>
                                        <small>Sale en tabla y PDF.</small>
                                    </span>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="opcion-card">
                                    <input type="checkbox" name="buscable" value="1">
                                    <span>
                                        <strong>Buscable</strong>
                                        <small>Para filtros futuros.</small>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-plus"></i> Agregar columna
                        </button>
                    </form>
                </div>
            </div>

            {{-- COLUMNAS CONFIGURADAS --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark">3. Columnas configuradas</div>
                        <small class="text-muted">Estas columnas ya forman la tabla del programa.</small>
                    </div>

                    <span class="badge bg-secondary">{{ $programa->campos->count() }}</span>
                </div>

                <div class="card-body p-0">
                    @if($programa->campos->isEmpty())
                        <div class="p-4 text-center">
                            <div class="mb-2 text-muted">
                                Todavía no hay columnas cargadas.
                            </div>
                            <div class="small text-muted">
                                Para empezar, agregá una columna tipo <strong>Fecha</strong>.
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">Orden</th>
                                        <th>Columna</th>
                                        <th style="width: 150px;">Tipo</th>
                                        <th class="text-center" style="width: 100px;">Oblig.</th>
                                        <th class="text-center" style="width: 100px;">Visible</th>
                                        <th class="text-end" style="width: 90px;"></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($programa->campos as $campo)
                                        <tr>
                                            <td>{{ $campo->orden }}</td>

                                            <td>
                                                <div class="fw-semibold">{{ $campo->nombre }}</div>

                                                @if($campo->tipo === 'fecha')
                                                    <div class="small text-success">
                                                        Ordena filas y calcula el día.
                                                    </div>
                                                @endif

                                                @if($campo->tipo === 'select' && !empty($campo->opciones))
                                                    <div class="small text-muted">
                                                        Opciones: {{ implode(', ', $campo->opciones) }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                @php
                                                    $tipoNombre = [
                                                        'texto' => 'Texto',
                                                        'textarea' => 'Texto largo',
                                                        'numero' => 'Número',
                                                        'fecha' => 'Fecha',
                                                        'hora' => 'Hora',
                                                        'select' => 'Lista',
                                                        'checkbox' => 'Sí / No',
                                                    ][$campo->tipo] ?? $campo->tipo;
                                                @endphp

                                                <span class="badge bg-dark">
                                                    {{ $tipoNombre }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                @if($campo->obligatorio)
                                                    <span class="badge bg-danger">Sí</span>
                                                @else
                                                    <span class="text-muted">No</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if($campo->visible_en_listado)
                                                    <span class="badge bg-success">Sí</span>
                                                @else
                                                    <span class="text-muted">No</span>
                                                @endif
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
                                </tbody>

                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

<style>
.card {
    border-radius: 12px;
}

.preview-programa {
    border: 1px solid #ded8ef;
    border-radius: 12px;
    padding: 14px;
    background: #faf9ff;
}

.preview-banner {
    border: 2px solid #6b5b95;
    border-radius: 8px;
    padding: 10px;
    background: #ffffff;
}

.preview-banner h5 {
    font-weight: 800;
    color: #3d315b;
    margin: 0;
}

.preview-banner small {
    color: #5f527f;
    font-weight: 600;
}

.preview-table thead th {
    background: #6b5b95;
    color: white;
    font-size: 12px;
}

.preview-table tbody tr:nth-child(even) {
    background: #f8f6ff;
}

.preview-table td {
    font-size: 12px;
}

.paso-ayuda {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}

.numero-ayuda {
    width: 26px;
    height: 26px;
    min-width: 26px;
    border-radius: 50%;
    background: #6b5b95;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
}

.opcion-card {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 10px;
    cursor: pointer;
    height: 100%;
    background: #fff;
}

.opcion-card:hover {
    border-color: #6b5b95;
    background: #faf9ff;
}

.opcion-card input {
    margin-top: 4px;
}

.opcion-card strong {
    display: block;
    font-size: 13px;
}

.opcion-card small {
    display: block;
    color: #6c757d;
    font-size: 11px;
}
</style>

<script>
const tipoCampo = document.getElementById('tipoCampo');
const ayudaTipo = document.getElementById('ayudaTipo');
const opcionesSelectBox = document.getElementById('opcionesSelectBox');

const ayudas = {
    texto: '<strong>Texto simple:</strong> para nombres, lugares, asignaciones o textos cortos.',
    textarea: '<strong>Texto largo:</strong> para comentarios u observaciones dentro de una fila.',
    numero: '<strong>Número:</strong> para cantidades o valores numéricos.',
    fecha: '<strong>Fecha:</strong> ordena las filas y permite mostrar el día automáticamente.',
    hora: '<strong>Hora:</strong> para horarios, por ejemplo 09:00 o 19:30.',
    select: '<strong>Lista de opciones:</strong> muestra un desplegable con valores fijos.',
    checkbox: '<strong>Sí / No:</strong> para marcar una opción simple.'
};

function actualizarAyudaTipo() {
    const tipo = tipoCampo.value;

    ayudaTipo.innerHTML = ayudas[tipo] || '';
    opcionesSelectBox.style.display = tipo === 'select' ? 'block' : 'none';
}

tipoCampo.addEventListener('change', actualizarAyudaTipo);
actualizarAyudaTipo();
</script>
@endsection