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
                            <td><a class="btn btn-secondary btn-sm" href="{{ route('pub.s21', ['id' => $publicador->id]) }}">Detalles (S-21)</a></td>
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
