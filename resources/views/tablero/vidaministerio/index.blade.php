@extends('layouts.app')

@section('content')
<div class="container">

    {{-- BOTONES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>
        <a href="{{ route('vidaministerio.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Agregar programa
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>


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
        <a href="{{ route('vidaministerio.index') }}" class="btn btn-outline-secondary btn-sm">
            Mes en curso
        </a>
    </div>
</form>


    @foreach ($registros as $r)
<div class="pagina-programa">

     {{-- TÍTULO SEMANA --}} 
    <div class="d-flex justify-content-between align-items-center border rounded-3 p-2 mb-2 bg-light no-print">
        <div>
            <strong>{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</strong> — 
            {{ mb_strtoupper(\Carbon\Carbon::parse($r->fecha)->locale('es')->translatedFormat('F Y')) }} 
            — Semana {{ \Carbon\Carbon::parse($r->fecha)->weekOfMonth }}
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-semana">
            <i class="fa-solid fa-chevron-down"></i> Ver detalles
        </button>
    </div>

    {{-- CONTENIDO PLEGABLE --}}
    <div class="contenido-semana d-none">
    {{-- ENCABEZADO TIPO CARTEL --}}
    <div class="banner-programa">
        <h1 class="titulo">VIDA Y MINISTERIO CRISTIANO</h1>
        <h5 class="subtitulo">
            PROGRAMA DE ASIGNACIONES <span class="fw-bold"> {{ mb_strtoupper(\Carbon\Carbon::parse($r->fecha)->locale('es')->translatedFormat('F Y')) }} - Semana {{ \Carbon\Carbon::parse($r->fecha)->weekOfMonth }}</span>
        </h5>
    </div>

    <div class="border rounded-3 shadow-sm p-3 mb-4 bg-white">

        {{-- ENCABEZADO --}}
        <div class="d-flex justify-content-between flex-wrap small mb-2">
            <div><strong>{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</strong> | Lectura semanal: <strong>{{ $r->lectura_semanal }}</strong></div>
            <div class="text-end">
                <div><strong>Presidente:</strong> {{ $r->presidente }}</div>
                <div><strong>Consejero Auxiliar:</strong> {{ $r->consejero_auxiliar }}</div>
            </div>
        </div>

        <ul class="mb-2 small ps-3 list-unstyled">
             <li>• Canción <strong>{{ $r->cancion_inicio }}</strong> <span class="float-end"><b>Oración:</b> {{ $r->oracion_final }}</span></li>
             <li>• Palabras de introducción <span class="text-muted">(1 min.)</span></li>
        </ul>

        {{-- TESOROS --}}
        @if ($r->tesoro_titulo || $r->perlas_disertante || $r->lectura_lector_auxiliar || $r->lectura_lector_principal)
        <div class="seccion-title mt-4" style="color: #2a6f74;">
            <i class="fa-solid fa-gem me-2"></i> TESOROS DE LA BIBLIA
        </div>
        <hr class="mt-1 mb-2">

        @php $nro = 1; @endphp

        <ul class="mb-2 small ps-3 list-unstyled">
            <li><strong>{{ $nro++ }}.</strong> {{ $r->tesoro_titulo }} <span class="text-muted">(10 mins.)</span> <span class="float-end">{{ $r->tesoro_disertante }}</span></li>
            <li><strong>{{ $nro++ }}.</strong> Busquemos perlas escondidas <span class="text-muted">(10 mins.)</span> <span class="float-end">{{ $r->perlas_disertante }}</span></li>
           <li>
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong>{{ $nro++ }}.</strong> Lectura de la Biblia
                    <span class="text-muted">(4 mins.)</span>
                </div>

                <div class="d-flex gap-4 text-end small">
                    <div class="d-flex flex-column text-center">
                        <span class="fw-bold">Sala Auxiliar</span>
                        <span>{{ $r->lectura_lector_auxiliar }}</span>
                    </div>
                    <div class="d-flex flex-column text-center">
                        <span class="fw-bold">Auditorio Principal</span>
                        <span>{{ $r->lectura_lector_principal }}</span>
                    </div>
                </div>
            </div>
        </li>

        </ul>
        @endif

        {{-- MAESTROS --}}
        @if ($r->asignaciones_maestros)
        <div class="seccion-title mt-4" style="color: #c69214;">
            <i class="fa-solid fa-wheat-awn me-2"></i> SEAMOS MEJORES MAESTROS
        </div>
        <hr class="mt-1 mb-2">

       <div class="ps-3">
    <table class="table table-borderless table-sm mb-2">
        <thead class="text-muted border-bottom">
            <tr>
                <th class="text-start">Asignación</th>
                <th class="text-start">Sala Auxiliar</th>
                <th class="text-start">Auditorio Principal</th>
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
                    <td>
                        @php
                            $aux1 = $a['auxiliar']['estudiante'] ?? '';
                            $aux2 = $a['auxiliar']['ayudante'] ?? '';
                            echo trim($aux1 . ($aux1 && $aux2 ? ' • ' : '') . $aux2) ?: '-';
                        @endphp
                    </td>
                    <td>
                        @php
                            $pri1 = $a['principal']['estudiante'] ?? '';
                            $pri2 = $a['principal']['ayudante'] ?? '';
                            echo trim($pri1 . ($pri1 && $pri2 ? ' • ' : '') . $pri2) ?: '-';
                        @endphp
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

        @endif

        {{-- VIDA CRISTIANA --}}
        @if ($r->vida_cristiana || $r->estudio_conductor || $r->estudio_lector)
        <div class="seccion-title mt-4" style="color: #a73229;">
            <i class="fa-solid fa-book me-2"></i> NUESTRA VIDA CRISTIANA
        </div>
        <hr class="mt-1 mb-2">

        <ul class="mb-2 small ps-3 list-unstyled">
            <li>• Canción <strong>{{ $r->cancion_medio }}</strong></li>
            @foreach ($r->vida_cristiana ?? [] as $i => $v)
            <li><strong>{{ $nro++ }}.</strong> {{ $v['titulo'] ?? '-' }} <span class="float-end">{{ $v['disertante'] ?? '-' }}</span></li>
            @endforeach

            <li>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $nro++ }}.</strong> Estudio bíblico de la congregación
                        <span class="text-muted">(30 mins.)</span>
                    </div>

                    <!-- títulos arriba, nombres abajo -->
                    <div class="d-flex gap-4 text-end small">
                        <div class="d-flex flex-column text-center">
                            <span class="fw-bold">Conductor</span>
                            <span>{{ $r->estudio_conductor }}</span>
                        </div>
                        <div class="d-flex flex-column text-center">
                            <span class="fw-bold">Lector</span>
                            <span>{{ $r->estudio_lector }}</span>
                        </div>
                    </div>
                </div>
            </li>



            <li>• Palabras de conclusión <span class="text-muted">(3 mins.)</span></li>
            <li>• Canción final: <strong>{{ $r->cancion_final }}</strong> <span class="float-end"><b>Oración:</b> {{ $r->oracion_final }}</span></li>
        </ul>
        @endif

        {{-- ELIMINAR --}}
        <form action="{{ route('vidaministerio.destroy', $r->id) }}" method="POST" class="no-print mt-3" onsubmit="return confirm('¿Eliminar este programa?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm">
                <i class="fa-solid fa-trash"></i> Eliminar Semana
            </button>
           <button type="button" class="btn btn-outline-secondary btn-sm btn-imprimir-uno">
    <i class="fa-solid fa-print"></i> Imprimir Semana
</button>


        </form>

    </div>
</div> 
 </div>   
    @endforeach

</div>

{{-- ESTILOS --}}
<style>
  
    .table td, .table th {
        padding: 0.1rem 0.2rem;
        font-size: 0.75rem;
    }

    ul li {
        margin-bottom: 0.15rem;
        padding-bottom: 0.15rem;
        border-bottom: 1px solid #eee;
    }

    ul li:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }


    .border.rounded-3.shadow-sm.p-3.mb-4 {
        padding: 0.5rem !important;
        margin-bottom: 0.9rem !important;
    }

    .d-flex.gap-4 {
        gap: 0.6rem !important;
    }

    .d-flex.justify-content-between.flex-wrap.small.mb-2 {
        margin-bottom: 0.3rem !important;
    }

    .mb-2 {
        margin-bottom: 0.2rem !important;
    }

    .ps-3 {
        padding-left: 0.75rem !important;
    }

    .float-end {
        float: right;
    }


    @media print {
    .pagina-programa {
        page-break-after: always;
    }

    .no-print {
        display: none !important;
    }
}
@media print {
    .contenido-semana {
        display: block !important;
    }
}

</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-imprimir-uno').forEach(function (btn, index) {
            btn.addEventListener('click', function () {
                const bloques = document.querySelectorAll('.pagina-programa');
                bloques.forEach((bloque, i) => {
                    bloque.style.display = (i === index) ? 'block' : 'none';
                });

                window.print();

                // Restaurar después de imprimir
                setTimeout(() => {
                    bloques.forEach(b => b.style.display = 'block');
                }, 1000);
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
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
