@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 760px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Agregar fila</h4>
            <p class="text-muted mb-0 small">
                {{ $programa->nombre }} · {{ $bloque->nombre }}
            </p>
        </div>

        <a href="{{ route('programas.bloques.index', $programa) }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 small">
            Revisá los campos marcados.
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-3 p-md-4">

            <form action="{{ route('programas.bloques.registros.store', [$programa, $bloque]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold mb-2">Tipo de fila</label>

                    <div class="tipo-opciones">
                        <label class="tipo-card" for="tipo_normal">
                            <input type="radio"
                                   name="tipo_fila"
                                   id="tipo_normal"
                                   value="normal"
                                   {{ old('tipo_fila', 'normal') === 'normal' ? 'checked' : '' }}>

                            <span>
                                <strong>Fila normal</strong>
                                <em>Para cargar los datos comunes de la tabla.</em>
                                <small>Ej: fecha, horario, encargado, lugar, grupo u observación.</small>
                            </span>
                        </label>

                        <label class="tipo-card" for="tipo_especial">
                            <input type="radio"
                                   name="tipo_fila"
                                   id="tipo_especial"
                                   value="especial"
                                   {{ old('tipo_fila') === 'especial' ? 'checked' : '' }}>

                            <span>
                                <strong>Aviso / fila especial</strong>
                                <em>Para mostrar un texto ancho dentro de la tabla.</em>
                                <small>Ej: asamblea, no hay salida, feriado o semana especial.</small>
                            </span>
                        </label>
                    </div>
                </div>

                <div id="bloque_especial" style="display:none;">
                    <div class="alert alert-warning py-2 small mb-3">
                        Esta fila no usa las columnas normales. Solo muestra un aviso en la fecha indicada.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha del aviso</label>
                        <input type="date"
                               name="fecha_especial"
                               class="form-control form-control-sm @error('fecha_especial') is-invalid @enderror"
                               value="{{ old('fecha_especial') }}">

                        @error('fecha_especial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Sirve para ubicar el aviso dentro del orden de la tabla.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Texto del aviso</label>
                        <textarea name="texto_especial"
                                  class="form-control @error('texto_especial') is-invalid @enderror"
                                  rows="3"
                                  placeholder="Ej: Asamblea de Circuito: BUSQUEN LA PAZ">{{ old('texto_especial') }}</textarea>

                        @error('texto_especial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="bloque_campos">
                    @foreach($programa->campos as $campo)
                        <div class="campo-linea mb-3">
                            <label class="form-label fw-semibold mb-1">
                                {{ $campo->nombre }}

                                @if($campo->obligatorio)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @include('programas.registros.partials.input', [
                                'campo' => $campo,
                                'valorActual' => old('campos.' . $campo->id)
                            ])

                            @error('campos.' . $campo->id)
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('programas.bloques.index', $programa) }}" class="btn btn-light border btn-sm">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-save"></i> Guardar fila
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<style>
.card {
    border-radius: 12px;
}

.tipo-opciones {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.tipo-card {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    border: 1px solid #d8d8d8;
    border-radius: 10px;
    padding: 10px 12px;
    cursor: pointer;
    background: #fff;
    transition: .15s ease-in-out;
}

.tipo-card:hover {
    border-color: #6b5b95;
    background: #faf9ff;
}

.tipo-card input {
    margin-top: 4px;
}

.tipo-card strong {
    display: block;
    font-size: 14px;
    color: #222;
    line-height: 1.2;
    margin-bottom: 2px;
}

.tipo-card em {
    display: block;
    font-style: normal;
    font-size: 12.5px;
    color: #444;
    line-height: 1.25;
    margin-bottom: 2px;
}

.tipo-card small {
    display: block;
    font-size: 11.5px;
    color: #777;
    line-height: 1.25;
}

.tipo-card:has(input:checked) {
    border-color: #6b5b95;
    background: #f4f0ff;
    box-shadow: 0 0 0 2px rgba(107, 91, 149, .08);
}

.campo-linea {
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 10px;
}

.campo-linea:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

#bloque_campos .form-control,
#bloque_campos .form-select {
    font-size: 14px;
}
</style>

<script>
function toggleTipoFila() {
    const tipoSeleccionado = document.querySelector('input[name="tipo_fila"]:checked')?.value;
    const esNormal = tipoSeleccionado === 'normal';

    document.getElementById('bloque_campos').style.display = esNormal ? 'block' : 'none';
    document.getElementById('bloque_especial').style.display = esNormal ? 'none' : 'block';
}

document.querySelectorAll('input[name="tipo_fila"]').forEach(input => {
    input.addEventListener('change', toggleTipoFila);
});

toggleTipoFila();
</script>
@endsection