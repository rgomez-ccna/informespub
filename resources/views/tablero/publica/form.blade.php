@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 700px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold text-secondary">Nueva asignación</h4>
        <a href="{{ route('publica.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al programa
        </a>
    </div>

    <form action="{{ isset($registro) ? route('publica.update', $registro) : route('publica.store') }}" method="POST">
        @csrf
        @if(isset($registro)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label mb-0">Fecha</label>
            <input type="date" name="fecha" class="form-control form-control-sm" required value="{{ old('fecha', $registro->fecha ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label mb-0">Presidente</label>
            <input type="text" name="presidente" class="form-control form-control-sm" required value="{{ old('presidente', $registro->presidente ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label mb-0">Lector</label>
            <input type="text" name="lector" class="form-control form-control-sm" required value="{{ old('lector', $registro->lector ?? '') }}">
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="es_nuevo_programa" value="1"
                   {{ old('es_nuevo_programa', $registro->es_nuevo_programa ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark">
                Solo marcar si este registro arranca un nuevo programa
            </label>
            <small class="text-muted d-block">Se usará para agrupar desde esta fecha en adelante.</small>
        </div>

        <div class="text-end">
            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-check"></i> Guardar
            </button>
        </div>
    </form>
</div>
@endsection
