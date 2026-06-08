@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 850px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Editar bloque</h3>
            <p class="text-muted mb-0">{{ $programa->nombre }}</p>
        </div>

        <a href="{{ route('programas.bloques.index', $programa) }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
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

                    <div class="form-text">
                        Este nombre aparece como título del bloque y en el PDF.
                    </div>
                </div>

                {{-- <div class="mb-3">
                    <label class="form-label">Subtítulo / descripción</label>
                    <input type="text"
                           name="descripcion"
                           class="form-control @error('descripcion') is-invalid @enderror"
                           value="{{ old('descripcion', $bloque->descripcion) }}"
                           placeholder="Opcional">

                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="mb-3">
                    <label class="form-label">Observaciones finales</label>
                    <textarea name="observaciones"
                              class="form-control @error('observaciones') is-invalid @enderror"
                              rows="4"
                              placeholder="Opcional. Este texto sale debajo de la tabla en el PDF.">{{ old('observaciones', $bloque->observaciones) }}</textarea>

                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox"
                           name="activo"
                           value="1"
                           class="form-check-input"
                           id="activo"
                           {{ old('activo', $bloque->activo) ? 'checked' : '' }}>

                    <label for="activo" class="form-check-label">
                        Bloque activo
                    </label>
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