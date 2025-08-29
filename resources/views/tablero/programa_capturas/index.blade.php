@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Carbon\Carbon;
@endphp

<div class="container" style="max-width: 900px;">

    {{-- BOTONES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>
        <a href="{{ route('tablero.programa-capturas.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Agregar
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
    </form>

    {{-- SELECCIÓN --}}
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

    {{-- ENCABEZADO SOLO 1ra PÁGINA (lo dejo igual) --}}
    <div class="banner-programa d-none d-print-block">
        <h1 class="titulo fw-bold">VIDA Y MINISTERIO CRISTIANO</h1>
        <h5 class="subtitulo">PROGRAMA DE ASIGNACIONES</h5>
    </div>

    @php
        // Elementos globales para impresión
        $ALL = [];
    @endphp

    {{-- SEMANAS (vista normal) --}}
    @foreach ($items as $idxSemana => $r)
        @php
            $fecha = Carbon::parse($r->fecha);
            $inicio = $fecha->copy()->startOfWeek(Carbon::MONDAY);
            $fin = $inicio->copy()->endOfWeek(Carbon::SUNDAY);
            $mesInicio = mb_strtoupper($inicio->locale('es')->translatedFormat('F'));
            $mesFin = mb_strtoupper($fin->locale('es')->translatedFormat('F'));
            $labelSemana = $mesInicio !== $mesFin
                ? ($inicio->format('d') . ' DE ' . $mesInicio . ' A ' . $fin->format('d') . ' DE ' . $mesFin)
                : ($inicio->format('d') . '–' . $fin->format('d') . ' DE ' . $mesFin);

            $notaVal   = collect($r->imagenes ?? [])->first(fn($v) => Str::startsWith($v,'::text::'));
            $notaTexto = $notaVal ? Str::after($notaVal,'::text::') : null;

            $imgPaths  = array_values(array_filter($r->imagenes ?? [], fn($v)=>!Str::startsWith($v,'::text::')));

            // Cargar elementos con URL completa (evita “cuadrados”)
            foreach ($imgPaths as $p) {
                $ALL[] = [
                    'week' => $idxSemana,
                    't'    => 'img',
                    'v'    => $p,
                    'url'  => asset($p),   // <-- clave
                ];
            }
            if ($notaTexto) {
                $ALL[] = [
                    'week'  => $idxSemana,
                    't'     => 'nota',
                    'v'     => $notaTexto,
                    'label' => $labelSemana,
                    'fecha' => $fecha->format('d/m/Y'),
                ];
            }
        @endphp

        <div class="semana-wrapper">
            {{-- Encabezado semana (vista) --}}
            <div class="d-flex justify-content-between align-items-center border rounded-3 p-2 mb-2 bg-light no-print">
                <div class="d-flex align-items-center gap-2">
                    <input type="checkbox" class="form-check-input check-imprimir" value="{{ $loop->index }}" id="semana_{{ $loop->index }}">
                    <label class="form-check-label mb-0" for="semana_{{ $loop->index }}">
                        <strong>{{ $fecha->format('d/m/Y') }}</strong> —
                        @if ($mesInicio !== $mesFin)
                            {{ $inicio->format('d') }} DE {{ $mesInicio }} - {{ $fin->format('d') }} DE {{ $mesFin }}
                        @else
                            {{ $inicio->format('d') }}–{{ $fin->format('d') }} DE {{ $mesFin }}
                        @endif
                    </label>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('tablero.programa-capturas.edit',$r->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                    <form action="{{ route('tablero.programa-capturas.destroy',$r->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta semana (y sus archivos)?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="fa-solid fa-trash"></i> Eliminar
                        </button>
                    </form>

                    <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-semana">
                        <i class="fa-solid fa-chevron-down"></i> Ver detalles
                    </button>
                </div>
            </div>

            {{-- Vista previa (no imprime) --}}
            <div class="contenido-semana d-none">
                <div class="border rounded-3 p-3 bg-white">
                    @if($notaTexto)
                        <div class="small mb-2">
                            <span class="fw-bold px-1" style="background:#fcff9f;border-radius:.25rem;">{{ $labelSemana }}</span>
                            <span class="ms-2" style="font-size:.8rem;">{{ $fecha->format('d/m/Y') }}</span>
                        </div>
                        <div class="border rounded p-2 bg-light mb-2">
                            <div class="fw-semibold mb-1"><i class="fa-regular fa-note-sticky"></i> Nota</div>
                            <div class="small" style="white-space:pre-line;">{{ $notaTexto }}</div>
                        </div>
                    @endif

                    <div class="row g-2">
                        @foreach($imgPaths as $p)
                            @php 
                          //  $url = Storage::url($p); 
                            $url = asset($p); // sin stotrage link

                            $isPdf = Str::endsWith($p,['.pdf','.PDF']); 
                            @endphp
                            <div class="col-12 col-md-6">
                                @if(!$isPdf)
                                    <img src="{{ $url }}" class="img-fluid w-100 rounded border" alt="">
                                @else
                                    <a href="{{ $url }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fa-solid fa-file-pdf"></i> Abrir PDF
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @php
        // Paginado global: 2 por página
        $PAGINAS_ALL = [];
        if (!empty($ALL)) $PAGINAS_ALL = array_chunk($ALL, 2);
    @endphp

    {{-- CONTENEDOR IMPRESIÓN "TODO" --}}
    <div id="print-global" class="d-none d-print-block">
        @foreach($PAGINAS_ALL as $pares)
            <div class="pagina-programa">
                <div class="slots-vertical">
                    @foreach($pares as $el)
                        <div class="slot-50">
  <div class="frame">
    @if(($el['t'] ?? null) === 'img')
      @php $isPdf = isset($el['v']) ? Str::endsWith($el['v'],['.pdf','.PDF']) : false; @endphp
      @if(!$isPdf)
        <img src="{{ $el['url'] ?? '' }}" class="print-img" alt="">
      @else
        <a href="{{ $el['url'] ?? '#' }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
          <i class="fa-solid fa-file-pdf"></i> Abrir PDF
        </a>
      @endif
    @else
      <div class="nota-box">
        <div class="fw-semibold mb-1"><i class="fa-regular fa-note-sticky"></i> {{ $el['label'] ?? 'Nota' }}</div>
        @if(!empty($el['fecha']))<div class="small text-muted mb-1">{{ $el['fecha'] }}</div>@endif
        <div class="small" style="white-space:pre-line;">{{ $el['v'] ?? '' }}</div>
      </div>
    @endif
  </div>
</div>

                    @endforeach
                    @if(count($pares)===1)
                        <div class="slot-50"></div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</div>

{{-- DATA para impresión seleccionada: con URL lista --}}
<script class="no-print">
    window.PRINT_DATA = @json($ALL, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
</script>

{{-- ESTILOS --}}
<style>
.table td, .table th { padding: .1rem .2rem !important; font-size: .75rem !important; }

@media print {
  /* @page { size: A4 portrait; margin: 10mm; } */

  .no-print{ display: none !important; }
  body { margin:0; padding:0; }

  /* oculto vistas sueltas en impresión; uso los contenedores armados */
  .semana-wrapper { display: none !important; }

  #print-global { display:block !important; }
  body.printing-selection #print-global { display: none !important; }
  body.printing-selection #print-selected { display: block !important; }

  /* Por página */
  .pagina-programa{
    break-after: page;
    -webkit-break-after: page;
    box-sizing: border-box;
    padding: 0;
    margin: 0;
  }
  .pagina-programa:last-child{
    break-after: auto;
    -webkit-break-after: auto;
  }

  /* Alturas cerradas para 2 elementos exactos:
     útil ≈ 297 - 20 = 277mm; gap 6mm → 271/2 ≈ 135.5 → usamos 133mm + 6mm gap = margen más seguro
  */

/* Colores del encabezado (ajustá a tu tono) */
:root{
  --accent: #49355c;      /* mismo color que el encabezado */
  --accent-bg: #ffffff;   /* fondo suave del contenedor */
}

/* Marco uniforme para imagen y nota (vista e impresión) */
.slot-50 { padding: 0; }
.slot-50 .frame{
  height: 100%;
  border: 2px solid var(--accent);
  background: var(--accent-bg);
  border-radius: .4rem;
  box-sizing: border-box;
  padding: 8mm;                 /* en impresión luce parejo; si queda justo, bajá a 6mm */
  display: flex;
  align-items: center;
  justify-content: center;
}

/* La imagen adentro del marco, sin deformar ni recortar */
.slot-50 .frame img.print-img{
  max-width: 100%;
  max-height: 100%;
  width: auto;
  height: auto;
  object-fit: contain;          /* no recorta ni deforma */
  object-position: center;
  display: block;
}

/* Nota sin su borde propio (usa el marco) */
.slot-50 .frame .nota-box{
  width: 100%;
  height: 100%;
  background: transparent;
  border: 0;
  padding: 0;
  overflow: hidden;
}


  .slots-vertical{ display: block; }
  .slot-50{
    height: 133mm;                 /* más seguro, evita empujes */
    margin: 0 0 6mm 0;
    overflow: hidden;               /* evita que empuje al siguiente */
    box-sizing: border-box;
    page-break-inside: avoid;
    break-inside: avoid;
  }
  .slot-50:last-child{ margin-bottom: 0; }

  .print-img{
    display: block;
    width: 100%;
    height: 100%;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;            /* no recorta, no deforma */
    object-position: center;
    page-break-inside: avoid;
    break-inside: avoid;
  }

  .nota-box{
    text-transform: uppercase;   /* convierte todo a mayúsculas */
    font-size: 1.1rem;             /* más grande, probá con 1.1rem si querés más */
    text-align: center;          /* centrado */
    line-height: 1.4;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    border: 1px solid #ddd;
    background: #f8f9fa;
    padding: 8mm;
    overflow: hidden;
    page-break-inside: avoid;
    break-inside: avoid;
  }
}


</style>

{{-- SCRIPTS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Seleccionar todos
    document.getElementById('check-todos')?.addEventListener('change', function () {
        document.querySelectorAll('.check-imprimir').forEach(chk => chk.checked = this.checked);
    });

    // Toggle detalle
    document.querySelectorAll('.btn-toggle-semana').forEach(btn => {
        btn.addEventListener('click', function () {
            const content = this.closest('.semana-wrapper').querySelector('.contenido-semana');
            const oculto = content.classList.toggle('d-none');
            this.innerHTML = oculto
                ? '<i class="fa-solid fa-chevron-down"></i> Ver detalles'
                : '<i class="fa-solid fa-chevron-up"></i> Ocultar detalles';
        });
    });

    // Imprimir seleccionados
    document.getElementById('btn-imprimir-seleccionados')?.addEventListener('click', function () {
        const seleccionadosIdx = Array.from(document.querySelectorAll('.check-imprimir'))
            .map((chk, i) => chk.checked ? i : null)
            .filter(i => i !== null);

        if (!seleccionadosIdx.length) { alert('Seleccioná al menos una semana.'); return; }

        // Filtrar elementos por semanas elegidas
        const data = (window.PRINT_DATA || []).filter(e => seleccionadosIdx.includes(e.week));
        if (!data.length) { alert('No hay elementos para imprimir.'); return; }

        // Armar páginas de a 2
        const pages = [];
        for (let i=0; i<data.length; i+=2) pages.push(data.slice(i, i+2));

        // Contenedor temporal
        const cont = document.createElement('div');
        cont.id = 'print-selected';
        cont.className = 'd-none d-print-block';

        let html = '';
        pages.forEach(par => {
            html += `<div class="pagina-programa"><div class="slots-vertical">`;
            par.forEach(el => {
                html += `<div class="slot-50">` + `<div class="frame">`;
                if (el.t === 'img') {
                    const isPdf = /\.pdf$/i.test(el.v || '');
                    if (!isPdf) {
                        html += `<img src="${(el.url || '')}" class="print-img" alt="">`;
                    } else {
                        html += `<a href="${(el.url || '#')}" target="_blank" class="btn btn-outline-secondary btn-sm w-100"><i class="fa-solid fa-file-pdf"></i> Abrir PDF</a>`;
                    }
                } else {
                    const label = (el.label || 'Nota');
                    const fecha = (el.fecha || '');
                    html += `<div class="nota-box">
                        <div class="fw-semibold mb-1">
                            <i class="fa-regular fa-note-sticky"></i> ${label}
                        </div>
                        <div class="small" style="white-space:pre-line;">
                            ${(el.v || '').replace(/</g,'&lt;')}
                        </div>
                        </div>`;

                }
                html += `</div>` + `</div>`;
            });
            if (par.length === 1) html += `<div class="slot-50"></div>`;
            html += `</div></div>`;
        });

        cont.innerHTML = html;
        document.body.appendChild(cont);
        document.body.classList.add('printing-selection');
        window.print();
        setTimeout(() => {
            document.body.classList.remove('printing-selection');
            cont.remove();
        }, 800);
    });
});
</script>
@endsection
