@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 900px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Editar bloque</h3>
            <p class="text-muted mb-0">{{ $programa->nombre }}</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('programas.bloques.registros.index', [$programa, $bloque]) }}" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-table"></i> Ver filas
            </a>

            <a href="{{ route('programas.bloques.index', $programa) }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            Revisá los campos marcados.
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('programas.bloques.update', [$programa, $bloque]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nombre del bloque</label>
                    <input type="text"
                           name="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $bloque->nombre) }}"
                           required>

                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción / subtítulo</label>
                    <input type="text"
                           name="descripcion"
                           class="form-control @error('descripcion') is-invalid @enderror"
                           value="{{ old('descripcion', $bloque->descripcion) }}">

                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha inicio</label>
                        <input type="date"
                               name="fecha_inicio"
                               class="form-control @error('fecha_inicio') is-invalid @enderror"
                               value="{{ old('fecha_inicio', optional($bloque->fecha_inicio)->format('Y-m-d')) }}">

                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha fin</label>
                        <input type="date"
                               name="fecha_fin"
                               class="form-control @error('fecha_fin') is-invalid @enderror"
                               value="{{ old('fecha_fin', optional($bloque->fecha_fin)->format('Y-m-d')) }}">

                        @error('fecha_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Orden</label>
                        <input type="number"
                               name="orden"
                               class="form-control @error('orden') is-invalid @enderror"
                               value="{{ old('orden', $bloque->orden) }}">

                        @error('orden')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox"
                                   name="activo"
                                   value="1"
                                   class="form-check-input"
                                   id="activo"
                                   {{ old('activo', $bloque->activo) ? 'checked' : '' }}>

                            <label for="activo" class="form-check-label">
                                Activo
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones finales</label>
                    <textarea name="observaciones"
                              class="form-control @error('observaciones') is-invalid @enderror"
                              rows="4">{{ old('observaciones', $bloque->observaciones) }}</textarea>

                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('programas.bloques.index', $programa) }}" class="btn btn-light border">
                        Cancelar
                    </a>

                    <button class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Guardar cambios
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection