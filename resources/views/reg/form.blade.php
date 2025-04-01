@extends('layouts.app')

@section('content')
<div class="container">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Nuevo Registro para: <span class="text-primary">{{ $publicador->nombre }}</span></h5>
    
</div>

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
<div class="col-6">
    <form action="{{ isset($registro) ? route('reg.update', $registro->id) : route('reg.store', $publicador->id) }}" method="POST">
        @csrf
        @if(isset($registro)) @method('PUT') @endif
    
        <input type="hidden" name="id_publicador" value="{{ $publicador->id }}">
    
        <div class="mb-2">
            <label>Año de Servicio</label>
            <select name="a_servicio" class="form-select form-select-sm" required>
                @for($year = now()->year; $year >= 2023; $year--)
                    <option value="{{ $year }}" {{ (old('a_servicio', $registro->a_servicio ?? '') == $year) ? 'selected' : '' }}> {{ $year }} </option>
                @endfor
            </select>
        </div>
    
    
    @php
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $mesAnterior = $meses[now()->subMonth()->month - 1]; // obtiene el mes anterior
    @endphp
    <div class="mb-2">
        <label>Mes</label>
        <select name="mes" class="form-select" required>
            <option value="">Seleccionar</option>
            @foreach($meses as $mes)
                <option value="{{ $mes }}" 
                    {{ old('mes', $registro->mes ?? $mesAnterior) == $mes ? 'selected' : '' }}>
                    {{ $mes }}
                </option>
            @endforeach
        </select>
    </div>
    
    
    
    
        @if($showAux)
        <div class="mb-2">
            <label>¿Hizo auxiliar este mes?</label>
            <select name="aux" class="form-select form-select-sm" id="selectAux" required>
                <option value="">Seleccionar</option>
                <option value="">No</option>
                <option value="(Auxiliar)">Si</option>
            </select>
        </div>
        @endif
    
        @if($showAux)
        <div id="checkboxContainer" class="mb-2" style="display: none;">
            <label><input type="checkbox" name="actividad" value="1"> Participó en predicación</label>
        </div>
        @endif
    
        <div id="horasContainer" class="mb-2">
            <label>Horas (sólo auxiliares o precursores)</label>
            <input type="number" name="horas" class="form-control form-control-sm" value="{{ old('horas', $registro->horas ?? '') }}">
        </div>
    
        <div class="mb-2">
            <label># Cursos Bíblicos</label>
            <input type="number" name="cursos" class="form-control form-control-sm" value="{{ old('cursos', $registro->cursos ?? '') }}">
        </div>
    
        <div class="mb-2">
            <label>Notas</label>
            <input type="text" name="notas" class="form-control form-control-sm" value="{{ old('notas', $registro->notas ?? '') }}">
        </div>
    
        <div class="text-center mt-3">
            <a href="{{ route('reg.s21', $publicador->id) }}" class="btn btn-secondary btn-sm">Volver</a>
            
            <button type="submit" class="btn btn-primary btn-sm">{{ isset($registro) ? 'Actualizar' : 'Guardar' }}</button>
        </div>
    
    </form>
   
</div>


</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectAux = document.getElementById('selectAux');
    const checkboxContainer = document.getElementById('checkboxContainer');
    const horasContainer = document.getElementById('horasContainer');

    if (selectAux) {
        selectAux.addEventListener('change', () => {
            if (selectAux.value === '(Auxiliar)') {
                horasContainer.style.display = 'block';
                checkboxContainer.style.display = 'none';
            } else {
                checkboxContainer.style.display = 'block';
                horasContainer.style.display = 'none';
            }
        });
    }
});
</script>

@endsection
