@extends('layouts.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   $(document).ready(function () {
      $('#buscador').on('input', function () {
          const buscadorValor = $(this).val().toLowerCase();
          $('.card').each(function () {
              const card = $(this);
              let tieneCoincidencia = false;
              card.find('tbody tr').each(function () {
                  const fila = $(this);
                  const nombre = fila.find('td:first').text().toLowerCase();
                  if (nombre.includes(buscadorValor)) {
                      fila.show();
                      tieneCoincidencia = true;
                  } else {
                      fila.hide();
                  }
              });
              if (tieneCoincidencia) {
                  card.show();
              } else {
                  card.hide();
              }
          });
      });
   });
</script>

<div class="container">
@if(session('free_access') === true)
<div class="alert alert-info text-center small p-2 mb-2" style="font-size:13px;">
   <i class="fa-solid fa-user-shield"></i> <i class="fa-solid fa-lock-open"></i> Acceso temporal habilitado  
    @if(isset($linkActual) && $linkActual->expires_at)
         - Expira en {{ \Carbon\Carbon::parse($linkActual->expires_at)->diffForHumans() }}
    @endif
</div>
@endif

@if(!session('free_access'))
<div class="card border-0 shadow-sm mb-3">
  <div class="card-body p-2">

       <form action="{{ route('acceso.store') }}" method="POST" class="d-flex flex-wrap align-items-center gap-2">
      @csrf

      <div class="d-flex align-items-center gap-1">
        <label for="link_dias" class="mb-0 text-muted small">Días:</label>
        <input id="link_dias" name="dias" type="number" min="1" max="60" value="7"
               class="form-control form-control-sm" style="width:70px">
      </div>

      <div class="d-flex align-items-center gap-1">
        <label for="link_pass" class="mb-0 text-muted small">Contraseña:</label>
        <input id="link_pass" name="password" type="password" placeholder="ej. 2414" required
               class="form-control form-control-sm" style="width:160px">
      </div>

      <button class="btn btn-primary btn-sm">
        <i class="fa-solid fa-link"></i> Generar link temporal
      </button>
    </form>

    @if(session('success'))
      <div class="mt-2">
        <div class="input-group input-group-sm">
          <span class="input-group-text"><i class="fa-solid fa-link text-primary"></i></span>
          <input id="lastLink" type="text" class="form-control" value="{{ session('success') }}" readonly>
          <button id="copyBtn" type="button" class="btn btn-outline-primary">
            <i class="fa-solid fa-copy"></i> Copiar
          </button>
        </div>
        <small class="text-muted">Compartí este link. Acceso temporal.</small>
      </div>
    @endif

  </div>
</div>

<script>
(() => {
  const btn = document.getElementById('copyBtn');
  const input = document.getElementById('lastLink');
  if (!btn || !input) return;
  btn.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(input.value);
      btn.innerHTML = '<i class="fa-solid fa-check"></i> Copiado';
      setTimeout(() => btn.innerHTML = '<i class="fa-solid fa-copy"></i> Copiar', 1500);
    } catch (e) {
      input.select();
      document.execCommand('copy'); // fallback
    }
  });
})();
</script>
@endif



    <form class="row g-3 my-0" id="form-busqueda">
        <div class="col-auto">
            <input placeholder="Buscar Publicador" type="text" class="form-control" id="buscador" autofocus>
        </div>
    </form>

    @foreach($publicadores as $grupo => $grupo_publicadores)
    <div class="card my-3">
        <div class="card-header d-flex justify-content-between py-3">
            <h5 class="m-0">Grupo - {{ $grupo }}</h5>
        </div>
        <div class="card-body table-responsive">

            <table class="table table-sm table-bordered table-hover my-1">
                <thead>
                    <tr>
                        <th style="width: 25%;">Nombre</th>
                        <th style="width: 10%;">&nbsp;</th>
                        <th style="width: 10%;">&nbsp;</th>
                        <th style="width: 10%;">&nbsp;</th>
                        <th style="width: 10%;">&nbsp;</th>
                        <th style="width: 25%;">Teléfono</th>
                        <th style="width: 10%;">&nbsp;</th>
                    </tr>
                    
                </thead>
                <tbody>
                    @foreach ($grupo_publicadores as $publicador)
                        @php
                            $class = '';
                            if (!empty($publicador->anciano)) {
                                $class = 'table-warning';
                            } elseif (!empty($publicador->sv)) {
                                $class = 'table-success';
                            }
                        @endphp
                        <tr class="{{ $class }}">
                            <td>{{ $publicador->nombre }}</td>
                            <td>{{ $publicador->rol }}</td>
                            <td>{{ !empty($publicador->precursor) ? 'PR' : '' }}</td>
                            <td>{{ !empty($publicador->anciano) ? 'Anciano' : '' }}</td>
                            <td>{{ !empty($publicador->sv) ? 'S. Ministerial' : '' }}</td>
                            <td>{{ substr_replace(substr_replace(substr_replace($publicador->telefono, ' ', 3, 0), ' ', 7, 0), ' ', 11, 0) }}</td>
                            <td>
                                {{-- <a class="btn btn-secondary btn-sm" href="{{ route('pub.s21', ['id' => $publicador->id]) }}">Detalles (S-21)</a> --}}
                                <a class="btn btn-secondary btn-sm" href="{{ session('free_access') ? route('pub.s21.free', ['id' => $publicador->id]) : route('pub.s21', ['id' => $publicador->id]) }}">
                                    Detalles (S-21)
                                </a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total : <strong>{{ $grupo_publicadores->count() }}</strong></td>
                        <td></td>
                        <td>Precursores: <strong>{{ $grupo_publicadores->where('precursor', true)->count() }}</strong></td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div> 
    <br>
    @endforeach
</div>
@endsection
