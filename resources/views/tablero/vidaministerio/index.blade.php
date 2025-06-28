@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 850px;">

    {{-- BOTONES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>
        <a href="{{ route('vidaministerio.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Agregar programa
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print"></i> Imprimir todo
        </button>
    </div>

    {{-- FILTRO --}}
    <form method="GET" class="row g-2 align-items-end mb-3 no-print">
        <div class="col-auto">
            <label class="form-label mb-0 small">Desde</label>
            <input type="date" name="desde" class="form-control form-control-sm" value="{{ $desde }}">
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small">Hasta</label>
            <input type="date" name="hasta" class="form-control form-control-sm" value="{{ $hasta }}">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-filter"></i> Filtrar</button>
        </div>
        <div class="col-auto">
            <a href="{{ route('vidaministerio.index') }}" class="btn btn-outline-secondary btn-sm">Mes en curso</a>
        </div>
    </form>

   {{-- CHECKBOX SELECCIÓN --}}
<div class="no-print mb-3">
    <div class="d-flex align-items-center gap-3">
        <div class="form-check mb-0">
            <input type="checkbox" id="check-todos" class="form-check-input">
            <label class="form-check-label" for="check-todos">Seleccionar todos</label>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-imprimir-seleccionados">
            <i class="fa-solid fa-print"></i> Imprimir seleccionados
        </button>
    </div>
</div>


{{-- CABECERA GENERAL SOLO PARA IMPRESIÓN --}}
   <div class="banner-programa d-none d-print-block">
    <h1 class="titulo fw-bold">VIDA Y MINISTERIO CRISTIANO</h1>
    <h5 class="subtitulo">
        PROGRAMA DE ASIGNACIONES
       
    </h5>
  </div>

 @php $nro = 1; @endphp
    {{-- PROGRAMAS --}}
    @foreach ($registros as $r)
     

        <div class="pagina-programa ">

           {{-- TÍTULO SEMANA --}}
            <div class="d-flex justify-content-between align-items-center border rounded-3 p-2 mb-2 bg-light no-print">
                <div class="d-flex align-items-center gap-2">
                    {{-- CHECKBOX POR PROGRAMA --}}
                    <input type="checkbox" class="form-check-input check-imprimir" value="{{ $loop->index }}" id="programa_{{ $loop->index }}">
                    <label class="form-check-label mb-0" for="programa_{{ $loop->index }}">
                        <strong>{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</strong> —
                        {{ mb_strtoupper(\Carbon\Carbon::parse($r->fecha)->locale('es')->translatedFormat('F Y')) }} —
                        Semana {{ \Carbon\Carbon::parse($r->fecha)->weekOfMonth }}
                    </label>
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-semana">
                    <i class="fa-solid fa-chevron-down"></i> Ver detalles
                </button>
            </div>

            {{-- CONTENIDO --}}
            <div class="contenido-semana d-none ">
                <div class="programa-box border p-3 rounded-3 mb-4 bg-white">
                    {{-- ENCABEZADO --}}
                    <div class="d-flex justify-content-between flex-wrap small ">
                        <div>
                        <span style="background-color: #fcff9f; border-radius: 0.25rem;" class="seccion-title fw-bold">PROGRAMA <span class="fw-bold">{{ mb_strtoupper(\Carbon\Carbon::parse($r->fecha)->locale('es')->translatedFormat('F Y')) }} (Semana {{ \Carbon\Carbon::parse($r->fecha)->weekOfMonth }}) - </span> 
                        <strong style="font-size: 0.8rem;">{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</strong></span><br>
                         Lectura semanal: <strong>{{ $r->lectura_semanal }}</strong>
                        </div>
                        <div class="text-end">
                            <div><strong>Presidente:</strong> {{ $r->presidente }}</div>
                            @if ($r->presidente_ayudante)
                                <div>Ayudante <small>(Auditorio Principal)</small>: {{ $r->presidente_ayudante }}</div>
                            @endif
                            <div><strong>Consejero Auxiliar:</strong> {{ $r->consejero_auxiliar }}</div>
                            @if ($r->consejero_ayudante)
                                <div>Ayudante <small>(Sala Auxiliar)</small>: {{ $r->consejero_ayudante }}</div>
                            @endif
                        </div>
                    </div>

                    <ul class="small ps-3 list-unstyled mb-0">
                        <li>• Canción <strong>{{ $r->cancion_inicio }}</strong> <span class="float-end"><b>Oración:</b> {{ $r->oracion_inicio }}</span></li>
                        <li>• Palabras de introducción <span class="text-muted">(1 min.)</span></li>
                    </ul>

                    {{-- TESOROS --}}
                    @if ($r->tesoro_titulo || $r->perlas_disertante || $r->lectura_lector_auxiliar || $r->lectura_lector_principal)
                        <div class="seccion-title fw-bold" style="color: #2a6f74;">
                            <i class="fa-solid fa-gem me-2"></i> TESOROS DE LA BIBLIA
                        </div>
                        <hr class="mt-0 mb-0">

                       

                        <ul class="mb-2 small ps-3 list-unstyled mb-0">
                            <li><strong>{{ $nro++ }}.</strong> {{ $r->tesoro_titulo }} <span class="text-muted">(10 mins.)</span> <span class="float-end">{{ $r->tesoro_disertante }}</span></li>
                            <li><strong>{{ $nro++ }}.</strong> Busquemos perlas escondidas <span class="text-muted">(10 mins.)</span> <span class="float-end">{{ $r->perlas_disertante }}</span></li>
                            <li>
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><strong>{{ $nro++ }}.</strong> Lectura de la Biblia <span class="text-muted">(4 mins.)</span></div>
                                    <div class="d-flex gap-4 text-end small">
                                        <div class="d-flex flex-column text-center"><span class="fw-bold">Sala Auxiliar</span><span>{{ $r->lectura_lector_auxiliar }}</span></div>
                                        <div class="d-flex flex-column text-center"><span class="fw-bold">Auditorio Principal</span><span>{{ $r->lectura_lector_principal }}</span></div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endif

                    {{-- MAESTROS --}}
                    @if ($r->asignaciones_maestros)
                        
                        <hr class="mt-0 mb-0">

                        <div class="ps-3">
                            <table class="table table-borderless table-sm ">
                                <thead class="text-muted border-bottom">
                                    <tr>
                                    <th class="th-reset">
                                        <div class="seccion-title fw-bold" style="color: #c69214;">
                                            <i class="fa-solid fa-wheat-awn me-2"></i> SEAMOS MEJORES MAESTROS
                                        </div>
                                    </th>
                                    <th>Sala Auxiliar</th>
                                    <th>Auditorio Principal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($r->asignaciones_maestros as $i => $a)
                                        @php
                                            $tieneDatos = ($a['titulo'] ?? '') || ($a['auxiliar']['estudiante'] ?? '') || ($a['auxiliar']['ayudante'] ?? '') || ($a['principal']['estudiante'] ?? '') || ($a['principal']['ayudante'] ?? '');
                                        @endphp
                                        @if ($tieneDatos)
                                            <tr style="border-bottom: 1px solid #dee2e6;">
                                                <td><strong>{{ $nro++ }}.</strong> {{ $a['titulo'] ?? '-' }}</td>
                                                <td>{{ trim(($a['auxiliar']['estudiante'] ?? '') . (($a['auxiliar']['estudiante'] ?? '') && ($a['auxiliar']['ayudante'] ?? '') ? ' • ' : '') . ($a['auxiliar']['ayudante'] ?? '')) ?: '-' }}</td>
                                                <td>{{ trim(($a['principal']['estudiante'] ?? '') . (($a['principal']['estudiante'] ?? '') && ($a['principal']['ayudante'] ?? '') ? ' • ' : '') . ($a['principal']['ayudante'] ?? '')) ?: '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- VIDA CRISTIANA --}}
                    @if ($r->vida_cristiana || $r->estudio_conductor || $r->estudio_lector)
                        <div class="seccion-title fw-bold" style="color: #a73229;">
                            <i class="fa-solid fa-book me-2"></i> NUESTRA VIDA CRISTIANA
                        </div>
                        <hr class="mt-0 mb-0">
                        <ul class="small ps-3 list-unstyled mb-0">
                            <li>• Canción <strong>{{ $r->cancion_medio }}</strong></li>
                            @foreach ($r->vida_cristiana ?? [] as $v)
                                <li><strong>{{ $nro++ }}.</strong> {{ $v['titulo'] ?? '-' }} <span class="float-end">{{ $v['disertante'] ?? '-' }}</span></li>
                            @endforeach
                            <li>
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><strong>{{ $nro++ }}.</strong> Estudio bíblico de la congregación <span class="text-muted">(30 mins.)</span></div>
                                    <div class="d-flex gap-4 text-end small">
                                        <div class="d-flex flex-column text-center"><span class="fw-bold">Conductor</span><span>{{ $r->estudio_conductor }}</span></div>
                                        <div class="d-flex flex-column text-center"><span class="fw-bold">Lector</span><span>{{ $r->estudio_lector }}</span></div>
                                    </div>
                                </div>
                            </li>
                            <li>• Palabras de conclusión <span class="text-muted">(3 mins.)</span></li>
                            <li>• Canción final: <strong>{{ $r->cancion_final }}</strong> <span class="float-end"><b>Oración:</b> {{ $r->oracion_final }}</span></li>
                        </ul>
                        <hr class="mt-0 mb-0">
                    @endif

                    {{-- BOTONES --}}
                    <form action="{{ route('vidaministerio.destroy', $r->id) }}" method="POST" class="no-print" onsubmit="return confirm('¿Eliminar este programa?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash"></i> Eliminar Semana</button>
                        
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>

{{-- ESTILOS GENERALES --}}
<style>
/* Estilo general de la tabla (modo vista) */
.table td, .table th {
    padding: 0.1rem 0.2rem;
    font-size: 0.75rem;
}
/* Reseteo de estilos del th especial */
.th-reset {
    all: unset;
    display: table-cell; /* necesario para que no rompa la tabla */
    vertical-align: top;
}


/* Espaciado entre ítems de listas */
ul li {
    margin-bottom: 0.15rem;
    padding-bottom: 0.15rem;
    border-bottom: 1px solid #eee;
}

/* Eliminar borde inferior del último ítem */
ul li:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>



{{-- ESTILOS SOLO PARA IMPRESIÓN --}}
<style>
@media print {

    /* Ajuste de fuente y márgenes globales */
    body {
        font-size: 11px;
        margin: 0;
        padding: 0;
    }

    /* Ocultar todo lo marcado como "no-print" */
    .no-print {
        display: none !important;
    }

    /* Forzar a que el contenido esté visible al imprimir */
    .contenido-semana {
        display: block !important;
    }

   /* Reseteo de estilos del th especial */
    .th-reset {
        all: unset;
        display: table-cell;
        vertical-align: top;
    }

    /* Cada bloque de programa (debe ocupar media hoja) */
    .pagina-programa {
        height: 48vh !important; /* Justo media hoja */
        overflow: hidden;
        padding: 5px !important;
        margin: 0 !important;
        box-sizing: border-box;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    /* Contenedor interno del programa */
    .programa-box {
        padding: 5px !important;
        margin: 0 !important;
        border: none !important;
        background-color: transparent !important;
    }

    /* Reducción general de paddings de contenedores internos */
    .pagina-programa .border {
        padding: 5px !important;
        margin-bottom: 6px !important;
    }

    /* Títulos de sección (Tesoros, Maestros, etc.) */
    .seccion-title {
        font-size: 12px !important;
        margin-top: 2px !important;
        margin-bottom: 2px !important;
    }

    /* Tabla de asignaciones más compacta */
    .table-sm td, .table-sm th {
        font-size: 10px !important;
        padding: 2px !important;
    }

    /* Listas más chicas y juntas */
    ul li {
        font-size: 11px !important;
        margin-bottom: 1px !important;
        padding-bottom: 1px !important;
    }

    /* Para que los elementos se ubiquen bien en el margen derecho */
    .float-end {
        float: right;
    }
}
</style>



{{-- SCRIPTS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-imprimir-uno').forEach(function (btn, index) {
        btn.addEventListener('click', function () {
            const bloques = document.querySelectorAll('.pagina-programa');
            bloques.forEach((bloque, i) => bloque.style.display = (i === index) ? 'block' : 'none');
            window.print();
            setTimeout(() => bloques.forEach(b => b.style.display = 'block'), 1000);
        });
    });

    document.getElementById('check-todos')?.addEventListener('change', function () {
        document.querySelectorAll('.check-imprimir').forEach(chk => chk.checked = this.checked);
    });

    document.getElementById('btn-imprimir-seleccionados')?.addEventListener('click', function () {
        const seleccionados = Array.from(document.querySelectorAll('.check-imprimir'))
            .map((chk, i) => chk.checked ? i : null)
            .filter(i => i !== null);

        const bloques = document.querySelectorAll('.pagina-programa');
        bloques.forEach((bloque, i) => bloque.style.display = seleccionados.includes(i) ? 'block' : 'none');
        window.print();
        setTimeout(() => bloques.forEach(b => b.style.display = 'block'), 1000);
    });

    document.querySelectorAll('.btn-toggle-semana').forEach(btn => {
        btn.addEventListener('click', function () {
            const content = this.closest('.pagina-programa').querySelector('.contenido-semana');
            content.classList.toggle('d-none');
            this.innerHTML = content.classList.contains('d-none')
                ? '<i class="fa-solid fa-chevron-down"></i> Ver detalles'
                : '<i class="fa-solid fa-chevron-up"></i> Ocultar detalles';
        });
    });
});
</script>
@endsection
