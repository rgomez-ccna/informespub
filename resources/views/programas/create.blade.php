@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 850px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Crear programa</h3>
            <p class="text-muted mb-0">
                Creá un nuevo programa para mostrarlo en el tablero.
            </p>
        </div>

        <a href="{{ route('programas.index') }}" class="btn btn-secondary btn-sm">
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

            <form action="{{ route('programas.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nombre del programa</label>
                    <input type="text"
                           name="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Acomodadores"
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
                              placeholder="Ej: Programa mensual de acomodadores">{{ old('descripcion') }}</textarea>

                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Orden</label>
                        <input type="number"
                               name="orden"
                               class="form-control @error('orden') is-invalid @enderror"
                               value="{{ old('orden', 0) }}">

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
                                   checked>

                            <label for="activo" class="form-check-label">
                                Activo en tablero
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('programas.index') }}" class="btn btn-light border">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Guardar programa
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection