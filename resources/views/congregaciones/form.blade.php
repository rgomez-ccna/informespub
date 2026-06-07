@extends('layouts.app')

@section('content')
<div class="container">

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Ups!</strong> Problemas con tu carga:<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>{{ isset($congregacion) ? 'Editar Congregación' : 'Nueva Congregación' }}</h5>

    <a href="{{ route('congregaciones.index') }}" class="text-white btn bg-secondary bg-gradient">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="col-6">
    <form action="{{ isset($congregacion) ? route('congregaciones.update', $congregacion->id) : route('congregaciones.store') }}" method="POST">
        @csrf

        @if(isset($congregacion))
            @method('PUT')
        @endif

        <div class="mb-2">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control form-control-sm"
                   value="{{ old('nombre', $congregacion->nombre ?? '') }}" required>
        </div>

        <div class="mb-2">
            <label>Ciudad</label>
            <input type="text" name="ciudad" class="form-control form-control-sm"
                   value="{{ old('ciudad', $congregacion->ciudad ?? '') }}">
        </div>

        <div class="mb-2">
            <label>Provincia</label>
            <input type="text" name="provincia" class="form-control form-control-sm"
                   value="{{ old('provincia', $congregacion->provincia ?? '') }}">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="activa" value="1" class="form-check-input" id="activa"
                   {{ old('activa', $congregacion->activa ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="activa">Activa</label>
        </div>

        <div class="mt-3">
            <a href="{{ route('congregaciones.index') }}" class="btn btn-secondary me-2">
                <i class="fa fa-arrow-left"></i> Volver
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-check"></i> {{ isset($congregacion) ? 'Actualizar' : 'Guardar' }}
            </button>
        </div>
    </form>
</div>

</div>
@endsection