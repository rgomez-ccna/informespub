@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 1150px;">

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Calificaciones</h3>
            <p class="text-muted mb-0">
                Marcá qué asignaciones puede recibir cada publicador.
            </p>
        </div>

        <a href="{{ route('vida-ministerio.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2">
            Revisá los datos enviados.
        </div>
    @endif

    @php
        $seccionesCalificaciones = [
            'Presidir y oración' => [
                'presidente' => 'Presidente',
                'oracion' => 'Oración',
                'ayudante_auditorio' => 'Ayudante auditorio',
                'consejero_auxiliar' => 'Consejero auxiliar',
                'ayudante_auxiliar' => 'Ayudante auxiliar',
            ],

            'Tesoros de la Biblia' => [
                'tesoro' => 'Discurso inicial',
                'perlas' => 'Perlas escondidas',
                'lectura_biblia' => 'Lectura bíblica',
            ],

            'Seamos Mejores Maestros' => [
                'maestros_unificado' => 'Presentaciones',
            ],

            'Nuestra Vida Cristiana' => [
                'vida_cristiana' => 'Partes de Vida Cristiana',
                'estudio_conductor' => 'Conductor del estudio',
                'estudio_lector' => 'Lector del libro',
            ],
        ];

        $ayudas = [
            'presidente' => 'Dirige la reunión',
            'oracion' => 'Oración inicial o final',
            'ayudante_auditorio' => 'Ayudante del auditorio principal',
            'consejero_auxiliar' => 'Consejero de sala auxiliar',
            'ayudante_auxiliar' => 'Ayudante de sala auxiliar',

            'tesoro' => 'Primera parte de Tesoros',
            'perlas' => 'Busquemos perlas escondidas',
            'lectura_biblia' => 'Lectura de la Biblia',

            'maestros_unificado' => 'Demostraciones, revisitas y discursos',

            'vida_cristiana' => 'Discursos o análisis',
            'estudio_conductor' => 'Estudio bíblico de la congregación',
            'estudio_lector' => 'Lector del libro',
        ];
    @endphp

    <form method="POST"
          action="{{ route('vida-ministerio.calificaciones.store') }}"
          id="formCalificaciones">
        @csrf

        {{-- BARRA SUPERIOR --}}
        <div class="card shadow-sm border-0 mb-3 barra-calificaciones">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small mb-1">Buscar publicador</label>
                        <input type="text"
                               id="buscarPublicador"
                               class="form-control form-control-sm"
                               placeholder="Nombre, grupo, anciano, SM, precursor...">
                    </div>

                    <div class="col-md-3">
                        <div class="rounded border bg-light px-3 py-1 h-100 d-flex align-items-center">
                            <div class="small">
                                <span class="text-muted">Mostrando:</span>
                                <strong id="contadorVisibles">{{ $publicadores->count() }}</strong>
                                <span class="text-muted">de {{ $publicadores->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 text-md-end">
                        <button class="btn btn-primary btn-sm w-100">
                            <i class="fa-solid fa-save"></i> Guardar calificaciones
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">

                <div class="tabla-calificaciones">
                    <table class="table table-sm align-middle mb-0 tabla-simple-calificaciones">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 24%;">Publicador</th>
                                <th style="width: 9%;">Grupo</th>
                                <th style="width: 67%;">Asignaciones permitidas</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($publicadores as $publicador)
                                @php
                                    $marcadas = $calificaciones->get($publicador->id, []);

                                    $maestrosMarcado =
                                        in_array('maestro_estudiante', $marcadas, true) ||
                                        in_array('maestro_ayudante', $marcadas, true);

                                    $textoFiltro = strtolower(trim(
                                        ($publicador->nombre ?? '') . ' ' .
                                        ($publicador->grupo ?? '') . ' ' .
                                        ($publicador->rol ?? '') . ' ' .
                                        ($publicador->estado ?? '') . ' ' .
                                        ($publicador->anciano ? 'anciano ' : '') .
                                        ($publicador->sv ? 'siervo ministerial sm ' : '') .
                                        ($publicador->precursor ? 'precursor ' : '')
                                    ));
                                @endphp

                                <tr class="fila-publicador" data-filtro="{{ $textoFiltro }}">
                                    <td>
                                        <div class="fw-bold">
                                           <span class="h5">{{ $publicador->nombre }}</span>
                                        </div>

                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @if($publicador->anciano)
                                                <span class="badge bg-primary">Anciano</span>
                                            @endif

                                            @if($publicador->sv)
                                                <span class="badge bg-info text-dark">SM</span>
                                            @endif

                                            @if($publicador->precursor)
                                                <span class="badge bg-success">Precursor</span>
                                            @endif

                                            @if($publicador->rol)
                                                <span class="badge bg-light text-dark border">{{ $publicador->rol }}</span>
                                            @endif

                                            @if($publicador->estado && $publicador->estado !== 'activo')
                                                <span class="badge bg-warning text-dark">{{ $publicador->estado }}</span>
                                            @endif
                                        </div>

                                        <div class="mt-2 d-flex gap-2">
                                            <button type="button"
                                                    class="btn btn-link btn-sm p-0 marcar-fila">
                                                Marcar todo
                                            </button>

                                            <button type="button"
                                                    class="btn btn-link btn-sm p-0 text-danger limpiar-fila">
                                                Limpiar
                                            </button>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="small">
                                            {{ $publicador->grupo ?: '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="bloque-secciones">

                                            @foreach($seccionesCalificaciones as $nombreSeccion => $opciones)
                                                <div class="seccion-calificacion">
                                                    <div class="seccion-titulo">
                                                        {{ $nombreSeccion }}
                                                    </div>

                                                    <div class="checks-calificacion">
                                                        @foreach($opciones as $tipo => $label)
                                                            @if($tipo === 'maestros_unificado')
                                                                @php
                                                                    $inputVisibleId = 'cal_' . $publicador->id . '_maestros_unificado';
                                                                    $inputEstudianteId = 'cal_' . $publicador->id . '_maestro_estudiante';
                                                                    $inputAyudanteId = 'cal_' . $publicador->id . '_maestro_ayudante';
                                                                @endphp

                                                                <input type="checkbox"
                                                                       class="check-calificacion check-maestros-real d-none"
                                                                       id="{{ $inputEstudianteId }}"
                                                                       name="calificaciones[{{ $publicador->id }}][]"
                                                                       value="maestro_estudiante"
                                                                       data-publicador="{{ $publicador->id }}"
                                                                       {{ $maestrosMarcado ? 'checked' : '' }}>

                                                                <input type="checkbox"
                                                                       class="check-calificacion check-maestros-real d-none"
                                                                       id="{{ $inputAyudanteId }}"
                                                                       name="calificaciones[{{ $publicador->id }}][]"
                                                                       value="maestro_ayudante"
                                                                       data-publicador="{{ $publicador->id }}"
                                                                       {{ $maestrosMarcado ? 'checked' : '' }}>

                                                                <div class="opcion-calificacion">
                                                                    <input type="checkbox"
                                                                           class="form-check-input check-maestros-visible"
                                                                           id="{{ $inputVisibleId }}"
                                                                           data-publicador="{{ $publicador->id }}"
                                                                           {{ $maestrosMarcado ? 'checked' : '' }}>

                                                                    <label for="{{ $inputVisibleId }}" class="opcion-calificacion-label">
                                                                        <span class="opcion-calificacion-texto">
                                                                            {{ $label }}
                                                                        </span>
                                                                        <span class="opcion-calificacion-ayuda">
                                                                            {{ $ayudas[$tipo] }}
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            @else
                                                                @continue(!array_key_exists($tipo, $tipos))

                                                                @php
                                                                    $inputId = 'cal_' . $publicador->id . '_' . $tipo;
                                                                @endphp

                                                                <div class="opcion-calificacion">
                                                                    <input type="checkbox"
                                                                           class="form-check-input check-calificacion"
                                                                           id="{{ $inputId }}"
                                                                           name="calificaciones[{{ $publicador->id }}][]"
                                                                           value="{{ $tipo }}"
                                                                           {{ in_array($tipo, $marcadas, true) ? 'checked' : '' }}>

                                                                    <label for="{{ $inputId }}" class="opcion-calificacion-label">
                                                                        <span class="opcion-calificacion-texto">
                                                                            {{ $label }}
                                                                        </span>
                                                                        <span class="opcion-calificacion-ayuda">
                                                                            {{ $ayudas[$tipo] ?? $tipos[$tipo] }}
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No hay publicadores cargados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('vida-ministerio.index') }}" class="btn btn-light border btn-sm">
                Cancelar
            </a>

            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-save"></i> Guardar calificaciones
            </button>
        </div>
    </form>

</div>

<style>
.barra-calificaciones {
    position: sticky;
    top: 0;
    z-index: 20;
}

.tabla-calificaciones {
    max-height: 72vh;
    overflow-y: auto;
    overflow-x: hidden;
}

.tabla-simple-calificaciones {
    table-layout: fixed;
    width: 100%;
    font-size: 13px;
}

.tabla-simple-calificaciones thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8f9fa;
    vertical-align: middle;
}

/* Separación clara entre publicadores */
.tabla-simple-calificaciones tbody tr.fila-publicador > td {
    border-top: 3px solid #d7dde5;
    padding-top: 12px;
    padding-bottom: 12px;
}

.tabla-simple-calificaciones tbody tr.fila-publicador:first-child > td {
    border-top: 0;
}

/* Fondo alternado suave */
.tabla-simple-calificaciones tbody tr.fila-publicador:nth-of-type(even) > td {
    background: #fcfcfd;
}

/* Resaltado al pasar el mouse */
.tabla-simple-calificaciones tbody tr.fila-publicador:hover > td {
    background: #f3f7ff;
}

/* Marca visual al inicio de cada publicador */
.tabla-simple-calificaciones tbody tr.fila-publicador > td:first-child {
    border-left: 4px solid #d7dde5;
}

.tabla-simple-calificaciones tbody tr.fila-publicador:hover > td:first-child {
    border-left-color: #0d6efd;
}

/* Que las secciones internas no compitan con la separación principal */
.seccion-calificacion {
    border-bottom: 1px dashed #cdcdcd;
}

.seccion-calificacion:last-child {
    border-bottom: 0;
}

.bloque-secciones {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.seccion-calificacion {
    display: grid;
    grid-template-columns: 155px 1fr;
    gap: 8px;
    align-items: start;
    padding: 5px 0;
    border-bottom: 1px solid #dbdbdb;
}

.seccion-calificacion:last-child {
    border-bottom: 0;
}

.seccion-titulo {
    font-size: 12px;
    color: #495057;
    font-weight: 700;
    padding-top: 5px;
}

.checks-calificacion {
    display: grid;
    grid-template-columns: repeat(3, minmax(145px, 1fr));
    gap: 6px;
}

.opcion-calificacion {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    border: 1px solid #dee2e6;
    border-radius: 7px;
    padding: 5px 7px;
    background: #fff;
    min-height: 42px;
}

.opcion-calificacion:has(input:checked) {
    border-color: #0d6efd;
    background: rgba(13, 110, 253, .06);
}

.opcion-calificacion-label {
    cursor: pointer;
    line-height: 1.15;
    margin: 0;
}

.opcion-calificacion-texto {
    display: block;
    font-weight: 600;
    color: #212529;
}

.opcion-calificacion-ayuda {
    display: block;
    font-size: 11px;
    color: #6c757d;
}

.form-check-input,
.marcar-fila,
.limpiar-fila {
    cursor: pointer;
}

.badge {
    font-weight: 500;
}

@media (max-width: 992px) {
    .tabla-calificaciones {
        overflow-x: auto;
    }

    .tabla-simple-calificaciones {
        min-width: 900px;
    }

    .checks-calificacion {
        grid-template-columns: repeat(2, minmax(145px, 1fr));
    }
}

@media (max-width: 768px) {
    .barra-calificaciones {
        position: static;
    }

    .seccion-calificacion {
        grid-template-columns: 1fr;
    }

    .checks-calificacion {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buscador = document.getElementById('buscarPublicador');
    const contadorVisibles = document.getElementById('contadorVisibles');

    function actualizarContadorVisibles() {
        const visibles = Array.from(document.querySelectorAll('.fila-publicador'))
            .filter(fila => fila.style.display !== 'none')
            .length;

        if (contadorVisibles) {
            contadorVisibles.textContent = visibles;
        }
    }

    function sincronizarMaestros(publicadorId, checked) {
        document.querySelectorAll('.check-maestros-real[data-publicador="' + publicadorId + '"]').forEach(check => {
            check.checked = checked;
        });

        const visible = document.querySelector('.check-maestros-visible[data-publicador="' + publicadorId + '"]');

        if (visible) {
            visible.checked = checked;
        }
    }

    function actualizarMaestrosVisibles(fila) {
        fila.querySelectorAll('.check-maestros-visible').forEach(visible => {
            const publicadorId = visible.dataset.publicador;

            const reales = fila.querySelectorAll('.check-maestros-real[data-publicador="' + publicadorId + '"]');
            const algunoMarcado = Array.from(reales).some(check => check.checked);

            visible.checked = algunoMarcado;

            reales.forEach(check => {
                check.checked = algunoMarcado;
            });
        });
    }

    buscador?.addEventListener('input', function () {
        const valor = this.value.trim().toLowerCase();

        document.querySelectorAll('.fila-publicador').forEach(fila => {
            const texto = fila.dataset.filtro || '';
            fila.style.display = texto.includes(valor) ? '' : 'none';
        });

        actualizarContadorVisibles();
    });

    document.querySelectorAll('.check-maestros-visible').forEach(check => {
        check.addEventListener('change', function () {
            sincronizarMaestros(this.dataset.publicador, this.checked);
        });
    });

    document.querySelectorAll('.marcar-fila').forEach(btn => {
        btn.addEventListener('click', function () {
            const fila = this.closest('tr');

            fila.querySelectorAll('.check-calificacion').forEach(check => {
                check.checked = true;
            });

            fila.querySelectorAll('.check-maestros-visible').forEach(check => {
                check.checked = true;
                sincronizarMaestros(check.dataset.publicador, true);
            });
        });
    });

    document.querySelectorAll('.limpiar-fila').forEach(btn => {
        btn.addEventListener('click', function () {
            const fila = this.closest('tr');

            fila.querySelectorAll('.check-calificacion').forEach(check => {
                check.checked = false;
            });

            fila.querySelectorAll('.check-maestros-visible').forEach(check => {
                check.checked = false;
                sincronizarMaestros(check.dataset.publicador, false);
            });
        });
    });

    document.querySelectorAll('.fila-publicador').forEach(fila => {
        actualizarMaestrosVisibles(fila);
    });

    actualizarContadorVisibles();
});
</script>
@endsection