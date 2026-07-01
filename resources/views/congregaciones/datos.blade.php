@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 1120px;">

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Datos de la congregacion</h3>
            <p class="text-muted mb-0">Resumen general y baja de datos.</p>
        </div>

        <a href="{{ route('usuarios.index') }}" class="btn btn-light border btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver a usuarios
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row g-3 align-items-start">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body py-3">
                    <h5 class="fw-bold mb-1">{{ $congregacion->nombre }}</h5>
                    <div class="text-muted small">Congregacion asociada a tu usuario.</div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-2">
                    <div class="fw-bold">Resumen de datos</div>
                    <small class="text-muted">Datos principales vinculados a esta congregacion.</small>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 datos-table">
                            <tbody>
                                @foreach($resumen as $titulo => $cantidad)
                                    <tr>
                                        <td>{{ $titulo }}</td>
                                        <td class="text-end fw-semibold" style="width: 90px;">{{ $cantidad }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-danger shadow-sm sticky-lg-top" style="top: 82px;">
                <div class="card-header bg-danger text-white fw-bold">
                    Eliminar datos
                </div>

                <div class="card-body">
                    <p class="small text-danger fw-semibold mb-2">
                        Baja definitiva de la congregacion.
                    </p>

                    <p class="small text-muted mb-3">
                        Se eliminan usuarios, publicadores, informes, asistencia, tablero,
                        Vida y Ministerio y registros asociados. Esta accion es irreversible.
                    </p>

                    <form method="POST"
                          action="{{ route('congregacion.destruir-propia') }}"
                          id="formEliminarCongregacion">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                Para confirmar, escribí <span class="text-danger">eliminar</span>.
                            </label>
                            <input type="text"
                                   name="confirmacion"
                                   class="form-control form-control-sm @error('confirmacion') is-invalid @enderror"
                                   placeholder="eliminar"
                                   autocomplete="off"
                                   required>

                            @error('confirmacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Eliminar definitivamente todos los datos
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.datos-table td {
    padding-top: 7px;
    padding-bottom: 7px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formEliminarCongregacion');

    if (!form) {
        return;
    }

    form.addEventListener('submit', function (event) {
        const ok = confirm('Esta accion eliminara definitivamente toda la congregacion y sus datos. ¿Continuar?');

        if (!ok) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);
});
</script>
@endsection
