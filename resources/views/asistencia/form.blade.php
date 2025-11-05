@extends('layouts.app')

@section('content')
<div class="container col-6">

<h5 class="mb-3">{{ isset($asistencia) ? 'Editar Asistencia (mes)' : 'Asistencia (Registro del mes)' }}</h5>

{{-- Año y Mes igual al sistema normal (septiembre arranca año servicio) --}}
@php
    $mesActual = now()->month;
    $añoServicio = ($mesActual >= 9) ? now()->year + 1 : now()->year;
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $mesAnterior = $meses[now()->subMonth()->month - 1];
@endphp


<form action="{{ isset($asistencia) ? route('asist.update',$asistencia->id) : route('asist.store') }}" method="POST">
    @csrf
    @if(isset($asistencia)) @method('PUT') @endif

    {{-- Año --}}
    <div class="mb-2">
        <label class="form-label">* AÑO DE SERVICIO</label>
        <select name="a_servicio" class="form-select form-select-sm" required>
            @for($year = $añoServicio; $year >= 2024; $year--)
                <option value="{{ $year }}" 
                    {{ old('a_servicio', $asistencia->a_servicio ?? $añoServicio) == $year ? 'selected':'' }}>
                    {{ $year }}
                </option>
            @endfor
        </select>
    </div>

    {{-- Mes --}}
    <div class="mb-2">
        <label class="form-label">* Mes</label>
        <select name="mes" class="form-select form-select-sm" required>
            @foreach($meses as $m)
            <option value="{{ $m }}" 
                {{ old('mes', $asistencia->mes ?? $mesAnterior) == $m ? 'selected':'' }}>
                {{ $m }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Tipo --}}
    <div class="mb-2">
        <label class="form-label">* Tipo Reunión</label>
        <select name="tipo" class="form-select form-select-sm" required>
            <option value="">Seleccione la Reunión</option>
            <option value="FS" {{ old('tipo',$asistencia->tipo ?? '')=='FS'?'selected':'' }}>🟦 FINAL DE SEMANA</option>
            <option value="ES" {{ old('tipo',$asistencia->tipo ?? '')=='ES'?'selected':'' }}>🟩 ENTRE SEMANA</option>
        </select>
    </div>


    <div class="row mb-2">
        <div class="col">
            <label class="form-label"># Reuniones</label>
            <input name="reuniones" type="number" class="form-control form-control-sm"
                value="{{ old('reuniones',$asistencia->reuniones ?? '') }}" required>
        </div>
        <div class="col">
            <label class="form-label">Asistencia Total</label>
            <input name="total" type="number" class="form-control form-control-sm"
                value="{{ old('total',$asistencia->total ?? '') }}" required>
        </div>
    </div>


    <div class="mt-3">
        <a href="{{ route('asist.index') }}" class="btn btn-secondary btn-sm">Volver</a>
        <button type="submit" class="btn btn-primary btn-sm">
            {{ isset($asistencia) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>

</form>

</div>
@endsection
