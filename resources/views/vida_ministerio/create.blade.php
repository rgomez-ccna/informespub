@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 800px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Nuevo programa</h3>
            <p class="text-muted mb-0">Creá la semana base de Vida y Ministerio.</p>
        </div>

        <a href="{{ route('vida-ministerio.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2">
            Revisá los campos marcados.
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('vida-ministerio.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date"
                               name="fecha"
                               class="form-control @error('fecha') is-invalid @enderror"
                               value="{{ old('fecha') }}"
                               required>

                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo de semana</label>
                        <select name="estado" id="tipoSemana" class="form-select">
                            <option value="normal" {{ old('estado', 'normal') === 'normal' ? 'selected' : '' }}>
                                Reunión normal
                            </option>
                            <option value="aviso" {{ old('estado') === 'aviso' ? 'selected' : '' }}>
                                Aviso / no hay reunión
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hora de inicio</label>
                        <input type="time"
                               name="hora_inicio"
                               class="form-control @error('hora_inicio') is-invalid @enderror"
                               value="{{ old('hora_inicio') }}">

                        @error('hora_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    
                </div>

                <div class="mb-3">
                    <label class="form-label">Lectura semanal</label>
                    <input type="text"
                           name="lectura_semanal"
                           class="form-control @error('lectura_semanal') is-invalid @enderror"
                           value="{{ old('lectura_semanal') }}"
                           placeholder="Ej: Jeremías 32, 33">

                    @error('lectura_semanal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Canción inicial</label>
                        <input type="text"
                               name="cancion_inicio"
                               class="form-control"
                               value="{{ old('cancion_inicio') }}"
                               placeholder="Ej: 1">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Canción intermedia</label>
                        <input type="text"
                               name="cancion_medio"
                               class="form-control"
                               value="{{ old('cancion_medio') }}"
                               placeholder="Ej: 128">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Canción final</label>
                        <input type="text"
                               name="cancion_final"
                               class="form-control"
                               value="{{ old('cancion_final') }}"
                               placeholder="Ej: 143">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre sala auxiliar</label>
                    <input type="text"
                           name="nombre_sala_auxiliar"
                           class="form-control"
                           value="{{ old('nombre_sala_auxiliar') }}"
                           placeholder="Opcional. Ej: Sala Auxiliar — RHOMANES">
                </div>

                <div class="mb-4">
                        <label class="form-label">Aviso u observaciones</label>
                        <textarea name="observaciones"
                                class="form-control"
                                rows="3"
                                placeholder="Ej: ASAMBLEA DE CIRCUITO 2026">{{ old('observaciones') }}</textarea>
                        <div class="form-text">
                            Si la semana es aviso, este texto aparecerá centrado en el PDF.
                        </div>
                    </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('vida-ministerio.index') }}" class="btn btn-light border">
                        Cancelar
                    </a>

                    <button class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Crear programa
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection