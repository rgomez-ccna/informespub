@extends('layouts.app')

@section('content')
<div class="container col-10">
    <h5 class="mb-3">{{ isset($publicador) ? 'Editar' : 'Nuevo' }} Publicador</h5>

    <form action="{{ isset($publicador) ? route('pub.update', $publicador->id) : route('pub.store') }}" method="POST">
        @csrf
        @if(isset($publicador)) @method('PUT') @endif

        <div class="row g-2">
            <div class="col-sm-6">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control form-control-sm" value="{{ old('nombre', $publicador->nombre ?? '') }}" required>
            </div>

            <div class="col-sm-6">
                <label class="form-label">Grupo *</label>
                <input type="text" name="grupo" class="form-control form-control-sm" value="{{ old('grupo', $publicador->grupo ?? '') }}" required>
            </div>

            <div class="col-sm-6">
                <label class="form-label">F. Nacimiento</label>
                <input type="date" name="fnacimiento" class="form-control form-control-sm" value="{{ old('fnacimiento', $publicador->fnacimiento ?? '') }}">
            </div>

            <div class="col-sm-6">
                <label class="form-label">F. Bautismo</label>
                <input type="date" name="fbautismo" class="form-control form-control-sm" value="{{ old('fbautismo', $publicador->fbautismo ?? '') }}">
            </div>

            <div class="col-sm-6">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control form-control-sm" value="{{ old('direccion', $publicador->direccion ?? '') }}">
            </div>

            <div class="col-sm-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control form-control-sm" value="{{ old('telefono', $publicador->telefono ?? '') }}">
            </div>

            <div class="col-sm-6">
                <label class="form-label">E-mail</label>
                <input type="text" name="mail" class="form-control form-control-sm" value="{{ old('mail', $publicador->mail ?? '') }}">
            </div>

            <div class="col-sm-6">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select form-select-sm">
                    <option value="">Seleccione (opcional)</option>
                    <option value="Sup. de Grupo" {{ old('rol', $publicador->rol ?? '') == 'Sup. de Grupo' ? 'selected' : '' }}>Sup. de Grupo</option>
                    <option value="Sup. Auxiliar" {{ old('rol', $publicador->rol ?? '') == 'Sup. Auxiliar' ? 'selected' : '' }}>Sup. Auxiliar</option>
                </select>
            </div>
            
        </div>

        <hr class="my-3">

        <div class="row g-2">
            @php
                $checks = ['hombre' => 'Hombre', 'mujer' => 'Mujer', 'oo' => 'Otras Ovejas', 'ungido' => 'Ungido', 'anciano' => 'Anciano', 'sv' => 'Siervo Ministerial', 'precursor' => 'Precursor'];
            @endphp

            @foreach($checks as $campo => $label)
                <div class="col-sm-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="{{ $campo }}" value="1" {{ old($campo, $publicador->$campo ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $label }}</label>
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="my-3">

        <div class="">
            <a href="{{ route('pub.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
            <button type="submit" class="btn btn-primary">
                Guardar <i class="fa fa-check"></i>

            </button>
        </div>
        

    </form>
</div>
@endsection
