@extends('layouts.app')

@section('content')
@php
    $desdeCarbon = \Carbon\Carbon::parse($desde);
    $hastaCarbon = \Carbon\Carbon::parse($hasta);
    $cantidadVisible = $programas->count();

    $anioImportacion = old('anio', 2026);
    $periodoImportacion = old('periodo', 'enero');
@endphp

<div class="container py-4" style="max-width: 1150px;">

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Vida y Ministerio</h3>
            <p class="text-muted mb-0">
                Programas semanales de la reunión. Marcá las semanas que querés imprimir.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('vida-ministerio.calificaciones.index') }}" class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-list-check"></i> Calificaciones
            </a>

            <a href="{{ route('vida-ministerio.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Nuevo programa / aviso
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('importar_wol'))
        <div class="alert alert-warning py-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $errors->first('importar_wol') }}
        </div>
    @endif

   {{-- FILTRO --}}
<form method="GET" action="{{ route('vida-ministerio.index') }}" class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">

            <div class="col-md-2">
                <label class="form-label small mb-1">Desde</label>
                <input type="date"
                       name="desde"
                       class="form-control form-control-sm"
                       value="{{ $desde }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date"
                       name="hasta"
                       class="form-control form-control-sm"
                       value="{{ $hasta }}">
            </div>

            <div class="col-md-2">
                <button class="btn btn-outline-primary btn-sm w-100">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
            </div>

            <div class="col-md-6">
                <div class="rounded border bg-primary bg-opacity-10 px-3 py-2 h-100 d-flex align-items-center">
                    <div class="">
                        <span class="text-primary fw-semibold">Estás viendo:</span>

                        <span class="fw-bold text-primary ms-1">
                            {{ $desdeCarbon->format('d/m/Y') }}
                            —
                            {{ $hastaCarbon->format('d/m/Y') }}
                        </span>

                        <span class="text-muted ms-1">
                            ({{ $cantidadVisible }}
                            {{ $cantidadVisible === 1 ? 'programa visible' : 'programas visibles' }})
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

    {{-- IMPORTAR --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-3">
            <form action="{{ route('vida-ministerio.importar-wol') }}"
                  method="POST"
                  class="row g-2 align-items-end"
                  id="formImportarWol">
                @csrf

                <div class="col-md-2">
                    <label class="form-label small mb-1">Año</label>
                    <select name="anio" class="form-select form-select-sm">
                        @for($anio = 2026; $anio <= max(2026, now()->year + 2); $anio++)
                            <option value="{{ $anio }}" {{ (int) $anioImportacion === $anio ? 'selected' : '' }}>
                                {{ $anio }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small mb-1">Período</label>
                    <select name="periodo" class="form-select form-select-sm">
                        <option value="enero" {{ $periodoImportacion === 'enero' ? 'selected' : '' }}>
                            Enero-Febrero
                        </option>
                        <option value="marzo" {{ $periodoImportacion === 'marzo' ? 'selected' : '' }}>
                            Marzo-Abril
                        </option>
                        <option value="mayo" {{ $periodoImportacion === 'mayo' ? 'selected' : '' }}>
                            Mayo-Junio
                        </option>
                        <option value="julio" {{ $periodoImportacion === 'julio' ? 'selected' : '' }}>
                            Julio-Agosto
                        </option>
                        <option value="septiembre" {{ $periodoImportacion === 'septiembre' ? 'selected' : '' }}>
                            Septiembre-Octubre
                        </option>
                        <option value="noviembre" {{ $periodoImportacion === 'noviembre' ? 'selected' : '' }}>
                            Noviembre-Diciembre
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-success btn-sm w-100" id="btnImportarWol">
                        <i class="fa-solid fa-cloud-arrow-down"></i>
                        Importar desde JW.org
                    </button>
                </div>

                <div class="col-md-3">
                    <div class="small text-muted">
                        Si ya existe, se actualiza y se abre ese período.
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="small text-muted mt-1">
                        Importa títulos, canciones, lectura semanal y duraciones. Luego solo cargás los asignados.
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- FORMULARIO REAL PARA PDF SELECCIONADOS --}}
    <form method="GET"
          action="{{ route('vida-ministerio.pdf.seleccionados') }}"
          target="_blank"
          id="formPdfSeleccionados">
    </form>

    {{-- ACCIONES PDF --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="form-check mb-0">
                        <input type="checkbox"
                               class="form-check-input"
                               id="checkTodosProgramas">

                        <label class="form-check-label small" for="checkTodosProgramas">
                            Seleccionar todos
                        </label>
                    </div>

                    <span class="small text-muted">
                        <span id="contadorSeleccionadas">0</span>
                        seleccionada(s). Se imprimirán únicamente las semanas seleccionadas.
                    </span>
                </div>

                <button type="submit"
                        form="formPdfSeleccionados"
                        class="btn btn-outline-danger btn-sm">
                    <i class="fa-solid fa-file-pdf"></i> PDF seleccionadas
                </button>
            </div>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45px;" class="text-center">PDF</th>
                            <th>Semana</th>
                            <th>Lectura / aviso</th>
                            <th>Hora</th>
                            <th>Tipo</th>
                            <th class="text-center">Partes</th>
                            <th class="text-center">Asignados</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($programas as $programa)
                            @php
                                $fecha = $programa->fecha;
                                $inicio = $fecha->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                                $fin = $fecha->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

                                $mesInicio = mb_strtoupper($inicio->locale('es')->translatedFormat('F'));
                                $mesFin = mb_strtoupper($fin->locale('es')->translatedFormat('F'));

                                if ($mesInicio !== $mesFin) {
                                    $semanaTexto = $inicio->format('d') . ' DE ' . $mesInicio . ' – ' . $fin->format('d') . ' DE ' . $mesFin;
                                } else {
                                    $semanaTexto = $inicio->format('d') . '–' . $fin->format('d') . ' DE ' . $mesFin;
                                }

                                $diaTexto = ucfirst($fecha->locale('es')->translatedFormat('l'));

                                $totalPartes = $programa->partes->count();
                                $totalAsignadas = $programa->partes->sum(fn($parte) => $parte->asignaciones->count());

                                $esAviso = $programa->estado === 'aviso';
                            @endphp

                            <tr class="{{ $esAviso ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                           name="programas[]"
                                           value="{{ $programa->id }}"
                                           class="form-check-input check-programa"
                                           form="formPdfSeleccionados">
                                </td>

                                <td style="min-width: 170px;">
                                    <div class="fw-bold text-secondary" style="font-size: 0.98rem; line-height: 1.15;">
                                        {{ $semanaTexto }}
                                    </div>

                                    <div class="small text-muted mt-1">
                                        {{ $diaTexto }} {{ $fecha->format('d/m/Y') }}
                                    </div>
                                </td>

                                <td>
                                    @if($esAviso)
                                        <div class="fw-semibold text-dark">
                                            {{ $programa->observaciones ?: 'Aviso / no hay reunión' }}
                                        </div>
                                        <div class="small text-muted">
                                            Esta semana se imprimirá como aviso.
                                        </div>
                                    @else
                                        {{ $programa->lectura_semanal ?: '-' }}
                                    @endif
                                </td>

                                <td>
                                    @if($esAviso)
                                        -
                                    @else
                                        {{ $programa->hora_inicio ? substr($programa->hora_inicio, 0, 5) : '-' }}
                                    @endif
                                </td>

                                <td>
                                    @if($esAviso)
                                        <span class="badge bg-warning text-dark">Aviso</span>
                                    @else
                                        <span class="badge bg-primary">Reunión</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{ $esAviso ? '-' : $totalPartes }}
                                </td>

                                <td class="text-center">
                                    {{ $esAviso ? '-' : $totalAsignadas }}
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('vida-ministerio.edit', $programa) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </a>

                                        <form action="{{ route('vida-ministerio.destroy', $programa) }}"
                                              method="POST"
                                              class="d-inline form-eliminar-programa">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-outline-danger btn-sm"
                                                    title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No hay programas en el rango mostrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkTodos = document.getElementById('checkTodosProgramas');
    const formPdf = document.getElementById('formPdfSeleccionados');
    const contadorSeleccionadas = document.getElementById('contadorSeleccionadas');

    const formImportarWol = document.getElementById('formImportarWol');
    const btnImportarWol = document.getElementById('btnImportarWol');

    function actualizarSeleccionados() {
        const checks = document.querySelectorAll('.check-programa');
        const checksMarcados = document.querySelectorAll('.check-programa:checked');

        if (contadorSeleccionadas) {
            contadorSeleccionadas.textContent = checksMarcados.length;
        }

        if (checkTodos) {
            checkTodos.checked = checks.length > 0 && checks.length === checksMarcados.length;
            checkTodos.indeterminate = checksMarcados.length > 0 && checksMarcados.length < checks.length;
        }
    }

    checkTodos?.addEventListener('change', function () {
        document.querySelectorAll('.check-programa').forEach(check => {
            check.checked = this.checked;
        });

        actualizarSeleccionados();
    });

    document.querySelectorAll('.check-programa').forEach(check => {
        check.addEventListener('change', actualizarSeleccionados);
    });

    actualizarSeleccionados();

    formPdf?.addEventListener('submit', function (e) {
        const seleccionados = document.querySelectorAll('.check-programa:checked');

        if (!seleccionados.length) {
            e.preventDefault();
            alert('Seleccioná al menos una semana para imprimir.');
        }
    });

    document.querySelectorAll('.form-eliminar-programa').forEach(form => {
        form.addEventListener('submit', function (e) {
            const ok = confirm('¿Eliminar este programa? Esta acción no se puede deshacer.');

            if (!ok) {
                e.preventDefault();
            }
        });
    });

    formImportarWol?.addEventListener('submit', function () {
        if (!btnImportarWol) {
            return;
        }

        btnImportarWol.disabled = true;
        btnImportarWol.innerHTML = `
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            Importando...
        `;
    });
});
</script>
@endsection