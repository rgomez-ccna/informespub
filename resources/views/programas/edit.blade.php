@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 1150px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Configurar programa</h3>
            <p class="text-muted mb-0">
                {{ $programa->nombre }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('programas.bloques.index', $programa) }}" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-layer-group"></i> Ver bloques
            </a>

            <a href="{{ route('programas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
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

        {{-- DATOS DEL PROGRAMA --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-semibold">
                    Datos del programa
                </div>

                <div class="card-body">
                    <form action="{{ route('programas.update', $programa) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text"
                                   name="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $programa->nombre) }}"
                                   required>

                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion"
                                      class="form-control @error('descripcion') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Descripción breve del programa">{{ old('descripcion', $programa->descripcion) }}</textarea>

                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orden en tablero</label>
                                <input type="number"
                                       name="orden"
                                       class="form-control @error('orden') is-invalid @enderror"
                                       value="{{ old('orden', $programa->orden) }}">

                                @error('orden')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="activo"
                                           value="1"
                                           class="form-check-input"
                                           id="activo"
                                           {{ old('activo', $programa->activo) ? 'checked' : '' }}>

                                    <label for="activo" class="form-check-label">
                                        Activo en tablero
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save"></i> Guardar programa
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-light border small">
                <strong>Idea:</strong><br>
                Primero configurá las columnas del programa. Después entrá a “Ver bloques” para crear una edición imprimible, por ejemplo Junio 2026, Semana especial o Campaña.
            </div>
        </div>

        {{-- CAMPOS DEL PROGRAMA --}}
        <div class="col-lg-7">

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-semibold">
                    Agregar campo / columna
                </div>

                <div class="card-body">
                    <form action="{{ route('programas.campos.store', $programa) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Nombre del campo</label>
                                <input type="text"
                                       name="nombre"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       placeholder="Ej: Fecha, Acceso 1, Auditorio"
                                       required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="texto">Texto</option>
                                    <option value="textarea">Texto largo</option>
                                    <option value="numero">Número</option>
                                    <option value="fecha">Fecha</option>
                                    <option value="hora">Hora</option>
                                    <option value="select">Select</option>
                                    <option value="checkbox">Checkbox</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Orden</label>
                                <input type="number"
                                       name="orden"
                                       class="form-control"
                                       value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Opciones para select</label>
                            <textarea name="opciones"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Una opción por línea. Solo se usa si el tipo es select."></textarea>
                        </div>

                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                       name="obligatorio"
                                       value="1"
                                       class="form-check-input"
                                       id="obligatorio">

                                <label for="obligatorio" class="form-check-label">
                                    Obligatorio
                                </label>
                            </div>

                            <div class="form-check">
                                <input type="checkbox"
                                       name="visible_en_listado"
                                       value="1"
                                       class="form-check-input"
                                       id="visible_en_listado"
                                       checked>

                                <label for="visible_en_listado" class="form-check-label">
                                    Visible en tabla/PDF
                                </label>
                            </div>

                            <div class="form-check">
                                <input type="checkbox"
                                       name="buscable"
                                       value="1"
                                       class="form-check-input"
                                       id="buscable">

                                <label for="buscable" class="form-check-label">
                                    Buscable
                                </label>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-plus"></i> Agregar campo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
                    <span>Campos configurados</span>
                    <span class="badge bg-secondary">{{ $programa->campos->count() }}</span>
                </div>

                <div class="card-body p-0">
                    @if($programa->campos->isEmpty())
                        <div class="p-4 text-muted text-center">
                            Todavía no hay campos cargados.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">Orden</th>
                                        <th>Campo</th>
                                        <th style="width: 110px;">Tipo</th>
                                        <th class="text-center" style="width: 90px;">Oblig.</th>
                                        <th class="text-center" style="width: 90px;">Visible</th>
                                        <th class="text-end" style="width: 80px;"></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($programa->campos as $campo)
                                        <tr>
                                            <td>{{ $campo->orden }}</td>

                                            <td>
                                                <div class="fw-semibold">{{ $campo->nombre }}</div>

                                                @if($campo->tipo === 'select' && !empty($campo->opciones))
                                                    <div class="small text-muted">
                                                        Opciones: {{ implode(', ', $campo->opciones) }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $campo->tipo }}
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
                                                      onsubmit="return confirm('¿Eliminar este campo? También se eliminarán sus valores cargados.')">
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
@endsection