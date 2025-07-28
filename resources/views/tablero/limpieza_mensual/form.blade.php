@extends('layouts.app')
@section('content')
<div class="container" style="max-width: 600px;">
    <h4 class="mb-3">Nueva Limpieza Mensual</h4>

    <form action="{{ route('limpieza-mensual.store') }}" method="POST">
        @csrf

        <div class="mb-2">
            <label class="form-label">Fecha Propuesta</label>
            <input type="date" name="fecha" class="form-control form-control-sm" required>
        </div>

        <div class="mb-2">
            <label class="form-label">Congregación asignada</label>
            <input type="text" name="congregacion" class="form-control form-control-sm" required>
        </div>

        <div class="mb-2">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" rows="2" class="form-control form-control-sm"></textarea>
        </div>

            <div class="mb-2 mt-5">
            <label class="form-label">
               Nota general de limpieza (opcional) <br>
                <small class="text-muted">* Se mostrará solo la última registrada</small>
            </label>
            <textarea name="observacion_general" rows="4" class="form-control form-control-sm"
                    placeholder="Ejemplo: 1) Las fechas son de carácter tentativo.&#10;2) La limpieza incluye el interior y exterior del Salón del Reino, y se realizará conforme a la modalidad indicada por el Comité de Mantenimiento."></textarea>
        </div>


        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('limpieza.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-check"></i> Guardar
            </button>
        </div>
    </form>
</div>
@endsection
