@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 600px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold text-secondary">
            {{ isset($registro) ? 'Editar registro' : 'Nuevo registro de limpieza' }}
        </h4>
        <a href="{{ route('limpieza.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al programa
        </a>
    </div>

    <form action="{{ isset($registro) ? route('limpieza.update', $registro) : route('limpieza.store') }}" method="POST">
        @csrf
        @if(isset($registro)) @method('PUT') @endif

        <div class="mb-2">
            <label class="form-label">Mes</label>
            <input type="text" name="mes" class="form-control form-control-sm" value="{{ old('mes', $registro->mes ?? '') }}" required>
        </div>

        <div class="mb-2">
            <label class="form-label">Grupo Asignado</label>
            <input type="text" name="grupo_asignado" class="form-control form-control-sm" value="{{ old('grupo_asignado', $registro->grupo_asignado ?? '') }}" required>
        </div>

        <div class="mb-2">
            <label class="form-label">Superintendente</label>
            <input type="text" name="superintendente" class="form-control form-control-sm" value="{{ old('superintendente', $registro->superintendente ?? '') }}" required>
        </div>

        <div class="mb-2">
            <label class="form-label">Auxiliar</label>
            <input type="text" name="auxiliar" class="form-control form-control-sm" value="{{ old('auxiliar', $registro->auxiliar ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <input type="text" name="observaciones" class="form-control form-control-sm" value="{{ old('observaciones', $registro->observaciones ?? '') }}">
        </div>

        <div class="text-end">
            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-check"></i> Guardar
            </button>
        </div>
    </form>
</div>
@endsection
