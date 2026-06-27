@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 1150px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Vida y Ministerio</h3>
            <p class="text-muted mb-0">
                Programas semanales de la reunión. Marcá las semanas que querés imprimir.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('vida-ministerio.calificaciones.index') }}" class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-list-check"></i> Calificaciones
            </a>

            <a href="{{ route('vida-ministerio.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Nuevo programa / aviso
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    {{-- FILTRO --}}
    <form method="GET" action="{{ route('vida-ministerio.index') }}" class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Desde</label>
                    <input type="date" name="desde" class="form-control form-control-sm" value="{{ $desde }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">Hasta</label>
                    <input type="date" name="hasta" class="form-control form-control-sm" value="{{ $hasta }}">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- PDF SELECCIONADOS --}}
    <form method="GET"
          action="{{ route('vida-ministerio.pdf.seleccionados') }}"
          target="_blank"
          id="formPdfSeleccionados">

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check mb-0">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="checkTodosProgramas">

                            <label class="form-check-label small" for="checkTodosProgramas">
                                Seleccionar todos
                            </label>
                        </div>

                        <span class="small text-muted">
                            Se imprimirán 2 semanas por hoja. Los avisos ocupan el mismo espacio que una reunión normal.
                        </span>
                    </div>

                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fa-solid fa-file-pdf"></i> PDF seleccionadas
                    </button>

                </div>
            </div>
        </div>

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
                                <th class="text-center">Asignadas</th>
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

                                    $totalPartes = $programa->partes->count();
                                    $totalAsignadas = $programa->partes->sum(fn($parte) => $parte->asignaciones->count());

                                    $esAviso = $programa->estado === 'aviso';
                                @endphp

                                <tr class="{{ $esAviso ? 'table-warning' : '' }}">
                                    <td class="text-center">
                                        <input type="checkbox"
                                               name="programas[]"
                                               value="{{ $programa->id }}"
                                               class="form-check-input check-programa">
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $fecha->format('d/m/Y') }}
                                        </div>

                                        <div class="small text-muted">
                                            @if($mesInicio !== $mesFin)
                                                {{ $inicio->format('d') }} DE {{ $mesInicio }} - {{ $fin->format('d') }} DE {{ $mesFin }}
                                            @else
                                                {{ $inicio->format('d') }}–{{ $fin->format('d') }} DE {{ $mesFin }}
                                            @endif
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
                                    @if($programa->estado === 'aviso')
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
                                        <a href="{{ route('vida-ministerio.edit', $programa) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </a>

                                        <a href="{{ route('vida-ministerio.pdf', $programa) }}"
                                           target="_blank"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-file-pdf"></i> PDF
                                        </a>

                                        <form action="{{ route('vida-ministerio.destroy', $programa) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar este programa?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No hay programas creados en este rango.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkTodos = document.getElementById('checkTodosProgramas');
    const form = document.getElementById('formPdfSeleccionados');

    checkTodos?.addEventListener('change', function () {
        document.querySelectorAll('.check-programa').forEach(check => {
            check.checked = this.checked;
        });
    });

    document.querySelectorAll('.check-programa').forEach(check => {
        check.addEventListener('change', function () {
            const checks = document.querySelectorAll('.check-programa');
            const checksMarcados = document.querySelectorAll('.check-programa:checked');

            if (checkTodos) {
                checkTodos.checked = checks.length > 0 && checks.length === checksMarcados.length;
            }
        });
    });

    form?.addEventListener('submit', function (e) {
        const seleccionados = document.querySelectorAll('.check-programa:checked');

        if (!seleccionados.length) {
            e.preventDefault();
            alert('Seleccioná al menos una semana para imprimir.');
        }
    });
});
</script>
@endsection