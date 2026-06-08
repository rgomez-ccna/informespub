@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 900px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Agregar fila</h3>
            <p class="text-muted mb-0">
                {{ $programa->nombre }} · {{ $bloque->nombre }}
            </p>
        </div>

        <a href="{{ route('programas.bloques.registros.index', [$programa, $bloque]) }}" class="btn btn-secondary btn-sm">
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

            <form action="{{ route('programas.bloques.registros.store', [$programa, $bloque]) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo de fila</label>
                        <select name="tipo_fila" id="tipo_fila" class="form-select">
                            <option value="normal" {{ old('tipo_fila', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="evento" {{ old('tipo_fila') === 'evento' ? 'selected' : '' }}>Evento</option>
                            <option value="nota" {{ old('tipo_fila') === 'nota' ? 'selected' : '' }}>Nota</option>
                            <option value="separador" {{ old('tipo_fila') === 'separador' ? 'selected' : '' }}>Separador</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Orden</label>
                        <input type="number"
                               name="orden"
                               class="form-control"
                               value="{{ old('orden', 0) }}">
                    </div>
                </div>

                <div id="bloque_especial" class="mb-3" style="display:none;">
                    <label class="form-label">Texto especial</label>
                    <textarea name="texto_especial"
                              class="form-control"
                              rows="3"
                              placeholder="Ej: No hay salida por asamblea / Semana especial / Nota importante">{{ old('texto_especial') }}</textarea>
                </div>

                <div id="bloque_campos">
                    @foreach($programa->campos as $campo)
                        <div class="mb-3">
                            <label class="form-label">
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

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('programas.bloques.registros.index', [$programa, $bloque]) }}" class="btn btn-light border">
                        Cancelar
                    </a>

                    <button class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Guardar fila
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
function toggleTipoFila() {
    const tipo = document.getElementById('tipo_fila').value;
    const esNormal = tipo === 'normal';

    document.getElementById('bloque_campos').style.display = esNormal ? 'block' : 'none';
    document.getElementById('bloque_especial').style.display = esNormal ? 'none' : 'block';
}

document.getElementById('tipo_fila').addEventListener('change', toggleTipoFila);
toggleTipoFila();
</script>
@endsection