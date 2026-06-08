@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 900px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Editar fila</h3>
            <p class="text-muted mb-0">
                {{ $programa->nombre }} · {{ $bloque->nombre }}
            </p>
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

            <form action="{{ route('programas.bloques.registros.update', [$programa, $bloque, $registro]) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="orden" value="{{ $registro->orden }}">

                <div class="mb-3">
                    <label class="form-label">Tipo de fila</label>
                    <select name="tipo_fila" id="tipo_fila" class="form-select">
                        <option value="normal" {{ old('tipo_fila', $registro->tipo_fila) === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="evento" {{ old('tipo_fila', $registro->tipo_fila) === 'evento' ? 'selected' : '' }}>Evento</option>
                        <option value="nota" {{ old('tipo_fila', $registro->tipo_fila) === 'nota' ? 'selected' : '' }}>Nota</option>
                        <option value="separador" {{ old('tipo_fila', $registro->tipo_fila) === 'separador' ? 'selected' : '' }}>Separador</option>
                    </select>
                </div>

                <div id="bloque_especial" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date"
                               name="fecha_especial"
                               class="form-control"
                               value="{{ old('fecha_especial', optional($registro->fecha)->format('Y-m-d')) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Texto especial</label>
                        <textarea name="texto_especial"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Ej: No hay salida por asamblea / Semana especial / Nota importante">{{ old('texto_especial', $registro->texto_especial) }}</textarea>
                    </div>
                </div>

                <div id="bloque_campos">
                    @foreach($programa->campos as $campo)
                        @php
                            $valor = $valores->get($campo->id);
                            $valorActual = old('campos.' . $campo->id);

                            if ($valorActual === null && $valor) {
                                if ($campo->tipo === 'numero') {
                                    $valorActual = $valor->valor_numero;
                                } elseif ($campo->tipo === 'fecha') {
                                    $valorActual = $valor->valor_fecha ? \Carbon\Carbon::parse($valor->valor_fecha)->format('Y-m-d') : null;
                                } elseif ($campo->tipo === 'hora') {
                                    $valorActual = $valor->valor_hora ? \Carbon\Carbon::parse($valor->valor_hora)->format('H:i') : null;
                                } elseif ($campo->tipo === 'checkbox') {
                                    $valorActual = data_get($valor->valor_json, 'checked') ? 1 : null;
                                } else {
                                    $valorActual = $valor->valor_texto;
                                }
                            }
                        @endphp

                        <div class="mb-3">
                            <label class="form-label">
                                {{ $campo->nombre }}

                                @if($campo->obligatorio)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @include('programas.registros.partials.input', [
                                'campo' => $campo,
                                'valorActual' => $valorActual
                            ])

                            @error('campos.' . $campo->id)
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
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