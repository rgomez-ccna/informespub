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

 
    @php
        $ALL = [];
    @endphp

    {{-- SEMANAS --}}
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

            foreach ($imgPaths as $p) {
                $ALL[] = [
                    'week' => $idxSemana,
                    't'    => 'img',
                    'v'    => $p,
                    'url'  => asset($p),
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
            {{-- Encabezado semana --}}
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

            {{-- Vista previa --}}
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
                            @php $url = asset($p); @endphp
                            <div class="col-12 col-md-6">
                                <img src="{{ $url }}" class="img-fluid w-100 rounded border" alt="">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @php
        $PAGINAS_ALL = [];
        if (!empty($ALL)) $PAGINAS_ALL = array_chunk($ALL, 2);
    @endphp

    {{-- IMPRESIÓN --}}
    <div id="print-global" class="d-none d-print-block">
        @foreach($PAGINAS_ALL as $pares)
            <div class="pagina-programa {{ $loop->first ? 'con-header' : '' }}">
                @if($loop->first)
                    <div class="banner-programa-print">
                        <h1 class="titulo fw-bold">VIDA Y MINISTERIO CRISTIANO</h1>
                        <h5 class="subtitulo">PROGRAMA DE ASIGNACIONES</h5>
                    </div>
                @endif

             <div class="slots-vertical">
                @foreach($pares as $el)
                    <div class="slot-50">
                    <div class="frame">
                        @if($el['t']==='img')
                        <img src="{{ $el['url'] }}" class="print-img" alt="">
                        @elseif($el['t']==='nota')
                        <div class="nota-box">
                            <div class="fw-semibold mb-1">{{ $el['label'] }} ({{ $el['fecha'] }})</div>
                            <div class="small" style="white-space:pre-line;">{{ $el['v'] }}</div>
                        </div>
                        @endif
                    </div>
                    </div>
                @endforeach

                @if(count($pares)===1)
                    <div class="slot-50">
                    <div class="frame"></div>
                    </div>
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
  /* ===== Vars ===== */
  :root{
    --page-h: 277mm;     /* A4 útil ≈ (márgenes ya considerados) */
    --gap: 6mm;          /* separación entre slots */
    --header-h: 22mm;    /* alto real del header en impresión */
    --accent: #41345a;
    --accent-bg: #ffffff;
  }

  /* ===== Reset ===== */
  
  /* @page { size: A4 portrait; margin: 10mm; } */
  .no-print{ display: none !important; }
  body { margin:0; padding:0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

  .semana-wrapper { display: none !important; }

  #print-global { display:block !important; }
  body.printing-selection #print-global { display: none !important; }
  body.printing-selection #print-selected { display: block !important; }

  /* ===== Página ===== */
  .pagina-programa{
    break-after: page;
    -webkit-break-after: page;
    box-sizing: border-box;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    min-height: var(--page-h);

    /* slot height por defecto: página SIN header (2 slots + 1 gap) */
    --slot-h-current: calc((var(--page-h) - var(--gap)) / 2);
  }
  .pagina-programa:last-child{
    break-after: auto;
    -webkit-break-after: auto;
  }

  /* Cuando hay header: 2 slots + 1 gap + header = page-h */
  .pagina-programa.con-header{
    --slot-h-current: calc((var(--page-h) - var(--header-h) - var(--gap)) / 2);
  }

  /* Header (sin margen adicional porque ya lo contamos en --header-h) */
.banner-programa-print {
    border: 3px solid #5c498b;
    padding: 10px 6px;
    text-align: center;
    border-radius: 6px;
    margin-bottom: 10px;
  }
  .banner-programa-print .titulo {
    margin: 0;
    font-weight: 700;
    color: #41345a;
    letter-spacing: 1px;
  }
  .banner-programa-print .subtitulo {
     margin: 0 !important;
    color: #3d3154;
    font-weight: 600;
  }

  /* Contenedor de slots */
  .slots-vertical{ display: block; }

  /* Centrado vertical solo cuando NO hay header */
  .pagina-programa:not(.con-header) .slots-vertical{ margin: auto 0; }
  .pagina-programa.con-header .slots-vertical{ margin: 0; }

  /* ===== Slots ===== */
  .slot-50{
    height: var(--slot-h-current);
    margin: 0 0 var(--gap) 0;
    overflow: hidden;
    box-sizing: border-box;
    page-break-inside: avoid;
    break-inside: avoid;
    padding: 0;
  }
  .slot-50:last-child{ margin-bottom: 0; }

  /* Marco */
  .slot-50 .frame{
    height: 100%;
    border: 2px solid var(--accent);
    background: var(--accent-bg);
    border-radius: .4rem;
    box-sizing: border-box;
    padding: 1mm;
    display: flex;
    align-items: stretch;
    justify-content: stretch;
  }

  /* Imagen */
  .print-img{
    display: block;
    width: 100%;
    height: 100%;
    object-fit: fill;      /* usar 'contain' si no querés deformar */
    object-position: center;
    max-width: none;
    max-height: none;
    page-break-inside: avoid;
    break-inside: avoid;
  }

  /* Nota */
  .nota-box{
    text-transform: uppercase;
    font-size: 1.2rem;
    text-align: center;
    line-height: 1.4;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    background: transparent;
    border: 0;
    padding: 8mm;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

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
        pages.forEach((par, idx) => {
  html += `<div class="pagina-programa ${idx===0 ? 'con-header' : ''}">`;

  // Header solo en la primera página seleccionada
  if (idx === 0) {
    html += `
      <div class="banner-programa-print">
        <h1 class="titulo fw-bold">VIDA Y MINISTERIO CRISTIANO</h1>
        <h5 class="subtitulo">PROGRAMA DE ASIGNACIONES</h5>
      </div>
    `;
  }

        html += `<div class="slots-vertical">`;
        par.forEach(el => {
            html += `<div class="slot-50"><div class="frame">`;
            if (el.t === 'img') {
            const isPdf = /\.pdf$/i.test(el.v || '');
            html += !isPdf
                ? `<img src="${(el.url || '')}" class="print-img" alt="">`
                : `<a href="${(el.url || '#')}" target="_blank" class="btn btn-outline-secondary btn-sm w-100"><i class="fa-solid fa-file-pdf"></i> Abrir PDF</a>`;
            } else {
            const label = (el.label || 'Nota');
            html += `<div class="nota-box">
                <div class="fw-semibold mb-1"><i class="fa-regular fa-note-sticky"></i> ${label}</div>
                <div class="small" style="white-space:pre-line;">${(el.v || '').replace(/</g,'&lt;')}</div>
            </div>`;
            }
            html += `</div></div>`;
        });
        if (par.length === 1) html += `<div class="slot-50"><div class="frame"></div></div>`;
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
