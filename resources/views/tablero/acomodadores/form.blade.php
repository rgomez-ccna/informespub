@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 700px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold text-secondary">Nueva asignación</h4>
        <a href="{{ route('acomodadores.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al programa
        </a>
    </div>

    <form action="{{ isset($registro) ? route('acomodadores.update', $registro) : route('acomodadores.store') }}" method="POST" class="needs-validation">
        @csrf
        @if(isset($registro))
            @method('PUT')
        @endif

        <div class="mb-2">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control form-control-sm" required value="{{ old('fecha', $registro->fecha ?? '') }}">
        </div>

        <div class="mb-2">
            <label class="form-label">Acceso / Estacionamiento 1</label>
            <input type="text" name="acceso_1" class="form-control form-control-sm" required value="{{ old('acceso_1', $registro->acceso_1 ?? '') }}">
        </div>

        <div class="mb-2">
            <label class="form-label">Acceso / Estacionamiento 2</label>
            <input type="text" name="acceso_2" class="form-control form-control-sm" required value="{{ old('acceso_2', $registro->acceso_2 ?? '') }}">
        </div>

        <div class="mb-2">
            <label class="form-label">Auditorio</label>
            <input type="text" name="auditorio" class="form-control form-control-sm" required value="{{ old('auditorio', $registro->auditorio ?? '') }}">
        </div>

        <div class="mb-2">
            <label class="form-label">Texto informativo (solo si querés actualizarlo)</label>
            <textarea name="nota_final" rows="3" class="form-control form-control-sm">{{ old('nota_final', $registro->nota_final ?? '') }}</textarea>
            <small class="text-muted">Este texto solo se muestra si lo completás. Se puede dejar vacío.</small>
        </div>

        <div class="d-flex justify-content-between align-items-start mt-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="es_nuevo_programa" value="1"
                    {{ old('es_nuevo_programa', $registro->es_nuevo_programa ?? false) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold text-dark">
                    Solo marcar si este registro arranca un nuevo programa
                </label>
                <small class="text-muted d-block">Se usará para agrupar desde esta fecha en adelante.</small>
            </div>

            <button class="btn btn-primary btn-sm mt-1">
                <i class="fa-solid fa-check"></i> Guardar
            </button>
        </div>
    </form>
</div>
@endsection
