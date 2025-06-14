@extends('layouts.app')

@section('content')


<div class="container" style="max-width: 600px;">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <h4 class="mb-3 fw-bold text-secondary">{{ isset($registro) ? 'Editar' : 'Agregar' }} Discurso Público</h4>

    <form action="{{ isset($registro) ? route('discursos.update', $registro) : route('discursos.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @if(isset($registro)) @method('PUT') @endif

        <div class="mb-2">
            <label class="form-label">Fecha del discurso</label>
            <input type="date" name="fecha" class="form-control form-control-sm" value="{{ old('fecha', $registro->fecha ?? '') }}" required>
        </div>

        <div class="mb-2">
            <label class="form-label">Título del discurso</label>
            <input type="text" name="conferencia" class="form-control form-control-sm" value="{{ old('conferencia', $registro->conferencia ?? '') }}" required>
            <div class="form-text">Ejemplo: ¿Qué pide Jehová de nosotros?</div>
        </div>

        <div class="mb-2">
            <label class="form-label">Nombre del disertante</label>
            <input type="text" name="disertante" class="form-control form-control-sm" value="{{ old('disertante', $registro->disertante ?? '') }}" required>
            <div class="form-text">Ejemplo: Juan Pérez</div>
        </div>

        <div class="mb-2">
            <label class="form-label">Congregación del disertante</label>
            <input type="text" name="congregacion" class="form-control form-control-sm" value="{{ old('congregacion', $registro->congregacion ?? '') }}" required>
            <div class="form-text">Ejemplo: Villa Mitre</div>
        </div>

        <div class="mb-2">
            <label class="form-label">Tipo de discurso</label>
            <select name="tipo" class="form-select form-select-sm" id="select-tipo" required>
                <option value="">Seleccione tipo...</option>
                <option value="visita" {{ old('tipo', $registro->tipo ?? '') == 'visita' ? 'selected' : '' }}>Visita (viene a nuestra congregación)</option>
                <option value="salida" {{ old('tipo', $registro->tipo ?? '') == 'salida' ? 'selected' : '' }}>Salida (va a otra congregación)</option>
            </select>
        </div>

        <div class="mb-2" id="campo-horario" style="display: none;">
            <label class="form-label">Horario del discurso (solo para salidas)</label>
            <input type="text" name="horario" class="form-control form-control-sm" value="{{ old('horario', $registro->horario ?? '') }}">
            <div class="form-text">Ejemplo: 10:00 hs</div>
        </div>

        <div class="mb-3" id="campo-check-programa" style="display: none;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check-programa" name="" value="1">
                <label class="form-check-label" id="label-programa"></label>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('discursos.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Cancelar
            </a>
            <button class="btn btn-primary btn-sm">
                <i class="fa fa-check"></i> Guardar
            </button>
        </div>
    </form>
</div>

<script>
    const selectTipo = document.getElementById('select-tipo');
    const campoHorario = document.getElementById('campo-horario');
    const campoCheckPrograma = document.getElementById('campo-check-programa');
    const checkPrograma = document.getElementById('check-programa');
    const labelPrograma = document.getElementById('label-programa');

    const oldTipo = '{{ old('tipo', $registro->tipo ?? '') }}';
    const oldCheckVisita = '{{ old('es_nuevo_programa_visita', $registro->es_nuevo_programa_visita ?? false) }}';
    const oldCheckSalida = '{{ old('es_nuevo_programa_salida', $registro->es_nuevo_programa_salida ?? false) }}';

    function mostrarCampos() {
        const tipo = selectTipo.value;

        // horario solo si es salida
        campoHorario.style.display = tipo === 'salida' ? 'block' : 'none';

        // mostrar el checkbox correspondiente
        if (tipo === 'visita') {
            campoCheckPrograma.style.display = 'block';
            checkPrograma.name = 'es_nuevo_programa_visita';
            checkPrograma.checked = oldCheckVisita == '1';
            labelPrograma.innerText = 'Nuevo programa de visitas';
        } else if (tipo === 'salida') {
            campoCheckPrograma.style.display = 'block';
            checkPrograma.name = 'es_nuevo_programa_salida';
            checkPrograma.checked = oldCheckSalida == '1';
            labelPrograma.innerText = 'Nuevo programa de salidas';
        } else {
            campoCheckPrograma.style.display = 'none';
            checkPrograma.name = '';
        }
    }

    selectTipo.addEventListener('change', mostrarCampos);
    window.addEventListener('DOMContentLoaded', mostrarCampos);
</script>
@endsection
