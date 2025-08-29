@extends('layouts.app')
@section('content')
<div class="container" style="max-width: 850px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-secondary mb-0">{{ $item ? 'Editar' : 'Nueva' }} – Capturas Semana</h4>
    <a href="{{ route('tablero.programa-capturas.index') }}" class="btn btn-secondary btn-sm">
      <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
  </div>

  <form action="{{ $item ? route('tablero.programa-capturas.update',$item->id) : route('tablero.programa-capturas.store') }}"
        method="POST" enctype="multipart/form-data">
    @csrf @if($item) @method('PUT') @endif

    <div class="row g-2 mb-3">
      <div class="col-md-4">
        <label class="form-label">Fecha (lunes de la semana)</label>
        <input type="date" name="fecha" class="form-control form-control-sm"
               value="{{ old('fecha', optional($item?->fecha)->toDateString()) }}" required>
      </div>
    </div>

    {{-- ARCHIVOS --}}
    <div class="border rounded p-2 mb-3">
      <label class="form-label">Capturas (JPG/PNG/PDF)</label>
      <input type="file" class="form-control form-control-sm" name="imagenes[]" multiple
             accept=".jpg,.jpeg,.png,.pdf">
      <small class="text-muted">Podés subir varias; se imprimirán como aparecen.</small>

      @if(!empty($item?->imagenes))
        <div class="row row-cols-2 row-cols-md-3 g-2 mt-2">
          @foreach($item->imagenes as $k => $val)
            @php
              $isText = Str::startsWith($val, '::text::');
              $isPdf  = Str::endsWith($val, ['.pdf','.PDF']);
              $url    = !$isText ? Storage::url($val) : null;
            @endphp
            <div class="col">
              <div class="border rounded p-1 h-100 d-flex flex-column">
                @if($isText)
                  <div class="fw-semibold mb-1"><i class="fa-regular fa-note-sticky"></i> Nota</div>
                  <div class="small" style="white-space:pre-line;">{{ Str::after($val,'::text::') }}</div>
                @elseif(!$isPdf)
                  <img src="{{ $url }}" class="img-fluid rounded" alt="">
                @else
                  <a href="{{ $url }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="fa-solid fa-file-pdf"></i> Ver PDF
                  </a>
                @endif

                <form action="{{ route('tablero.programa-capturas.imagen.destroy', [$item->id,$k]) }}"
                      method="POST" class="mt-2">
                  @csrf @method('DELETE')
                  <button class="btn btn-outline-danger btn-sm w-100"
                          onclick="return confirm('¿Eliminar este elemento?')">
                    <i class="fa-solid fa-trash"></i> Eliminar
                  </button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- NOTA EXTRA --}}
    <div class="mt-2 border rounded p-2">
      <label class="form-label fw-semibold mb-1">Agregar nota (opcional)</label>
      <textarea name="nota" class="form-control form-control-sm" rows="2"
                placeholder="Ej.: No hay reunión esta semana por asamblea."></textarea>
    </div>

    <button class="btn btn-primary btn-sm mt-3">
      <i class="fa-solid fa-check"></i> Guardar
    </button>
  </form>
</div>

<style>@media print {.no-print{display:none!important} img{break-inside:avoid;page-break-inside:avoid}}</style>
@endsection
