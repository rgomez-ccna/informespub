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

    {{-- Año --}}
    <div class="mb-2">
        <label>Año de Servicio</label>
        <select name="a_servicio" class="form-select form-select-sm" required>
            @for($year = now()->year; $year >= 2023; $year--)
                <option value="{{ $year }}" {{ (old('a_servicio', $registro->a_servicio ?? '') == $year) ? 'selected' : '' }}> {{ $year }} </option>
            @endfor
        </select>
    </div>

    {{-- Mes --}}
    @php
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $mesAnterior = $meses[now()->subMonth()->month - 1];
    @endphp
    <div class="mb-2">
        <label>Mes</label>
        <select name="mes" class="form-select" required>
            <option value="">Seleccionar</option>
            @foreach($meses as $mes)
                <option value="{{ $mes }}" {{ old('mes', $registro->mes ?? $mesAnterior) == $mes ? 'selected' : '' }}>{{ $mes }}</option>
            @endforeach
        </select>
    </div>

    {{-- Auxiliar --}}
    @if($showAux)
    <div class="mb-2">
        <label>¿Hizo auxiliar este mes?</label>
        <select name="aux" class="form-select form-select-sm" id="selectAux">
        <option value="">No</option>
        <option value="(Auxiliar)" {{ old('aux', $registro->aux ?? '') === '(Auxiliar)' ? 'selected' : '' }}>Sí</option>
        </select>


    </div>
    @endif

    {{-- Si no es auxiliar - Participación --}}
    @if($showAux)
    <div id="predicacionContainer" class="mb-2" style="display: none;">
        <label><b>Participación en predicación *</b></label><br>
        <div class="form-check form-check-inline" style="font-size: 1.2em;">
           <input class="form-check-input" type="radio" name="actividad" id="predico_si" value="1"
  {{ old('actividad', $registro->actividad ?? null) == '1' ? 'checked' : '' }}>
<label for="predico_si">Predicó</label>
        </div>
        <div class="form-check form-check-inline" style="font-size: 1.2em;">
           <input class="form-check-input" type="radio" name="actividad" id="predico_no" value="0"
  {{ old('actividad', $registro->actividad ?? null) == '0' ? 'checked' : '' }}>
<label for="predico_no">No predicó</label>
        </div>
    </div>
    @endif

    {{-- Si es auxiliar - Horas --}}
    <div id="horasContainer" class="mb-2">
        <label>Horas (sólo auxiliares o precursores)</label>
        <input type="number" name="horas" class="form-control form-control-sm" value="{{ old('horas', $registro->horas ?? '') }}">
    </div>

    {{-- Cursos --}}
    <div class="mb-2">
        <label># Cursos Bíblicos</label>
        <input type="number" name="cursos" class="form-control form-control-sm" value="{{ old('cursos', $registro->cursos ?? '') }}">
    </div>

    {{-- Notas --}}
    <div class="mb-2">
        <label>Notas</label>
        <input type="text" name="notas" class="form-control form-control-sm" value="{{ old('notas', $registro->notas ?? '') }}">
    </div>

    {{-- Botones --}}
    <div class=" mt-3">
        <a href="{{ route('reg.s21', $publicador->id) }}" class="btn btn-secondary me-2">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
        
        <button type="submit" class="btn btn-primary ">
            <i class="fa fa-check"></i> {{ isset($registro) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
    

</form>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectAux = document.getElementById('selectAux');
        const predicacionContainer = document.getElementById('predicacionContainer');
        const horasContainer = document.getElementById('horasContainer');
        const radios = document.querySelectorAll('input[name="actividad"]');
        const inputHoras = document.querySelector('input[name="horas"]');
        const esPrecursor = {{ $publicador->precursor ? 'true' : 'false' }};
    
        function toggleFields() {
                if (esPrecursor) {
                    // Precursor Regular informa horas
                    horasContainer.style.display = 'block';
                    inputHoras.required = true;
                    predicacionContainer.style.display = 'none';
                    radios.forEach(r => { r.checked = false; r.required = false; });
                    return;
                }

                if (selectAux && selectAux.value === '(Auxiliar)') {
                    // Auxiliar informa horas
                    horasContainer.style.display = 'block';
                    inputHoras.required = true;
                    predicacionContainer.style.display = 'none';
                    radios.forEach(r => { r.checked = false; r.required = false; });
                } else {
                    // Publicador común informa participación
                    predicacionContainer.style.display = 'block';
                    radios.forEach(r => { r.required = true; });
                    horasContainer.style.display = 'none';
                    inputHoras.value = '';
                    inputHoras.required = false;
                }
            }

    
        if (selectAux) {
            selectAux.addEventListener('change', toggleFields);
        }
        toggleFields();
    });
    </script>
    
@endsection
