@extends('layouts.app')

@section('content')
<div class="container">

    <h5>Enviar Informes Mensuales</h5>

    {{-- Filtro --}}
    <form class="row g-2 mb-3" method="get" action="{{ route('reg.enviar-informes') }}">
        <div class="col-12 col-lg-3">
            <select name="mes" class="form-select form-select-sm" required>
                @foreach(['Septiembre','Octubre','Noviembre','Diciembre','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto'] as $m)
                    <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-lg-3">
            <select name="anho" class="form-select form-select-sm" required>
                @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ request('anho') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-12 col-lg-2">
            <button class="btn btn-success btn-sm w-100" type="submit">Calcular</button>
        </div>
    </form>
    

    {{-- Mostrar resultados solo si se envió el filtro --}}
    @if(request('mes') && request('anho'))

    <div class="alert alert-info">Informe de: <strong>{{ request('mes') }} {{ request('anho') }}</strong></div>

    {{-- ======================= --}}
    {{-- ===== PUBLICADORES ==== --}}
    {{-- ======================= --}}
    <div class="card mb-3">
        <div class="card-header alert alert-primary">Publicadores</div>
        <div class="card-body table-responsive">

            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Publicador</th>
                        <th>Participación</th>
                        <th>Cursos</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPub = 0;
                        $sinInformePub = [];
                    @endphp
                    @foreach($publicadores->where('precursor', null) as $pub)
                        @php
                            $reg = $pub->registros->where('mes', request('mes'))->where('a_servicio', request('anho'))->first();
                        @endphp

                        {{-- Publicador que informó --}}
                        @if($reg && empty($reg->aux))
                            <tr>
                                <td>{{ $pub->nombre }}</td>
                                <td class="text-center">@if($reg->actividad) <i class="fa fa-check text-success"></i> @endif</td>
                                <td class="text-center">{{ $reg->cursos ?? '-' }}</td>
                                <td>{{ $reg->notas ?? '' }}</td>
                            </tr>
                            @php $totalPub++; @endphp

                        {{-- Publicador sin registro --}}
                        @elseif(!$reg)
                            @php $sinInformePub[] = $pub; @endphp
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Total Publicadores: {{ $publicadores->where('precursor', null)->count() }} | Informaron: {{ $totalPub }} | Sin informar: {{ count($sinInformePub) }}</td>
                    </tr>
                </tfoot>
            </table>

            {{-- Publicadores sin informar --}}
            @if(count($sinInformePub))
                <p><b>Sin Informe:</b></p>
                <ul>
                    @foreach($sinInformePub as $pub)
                        <li>{{ $pub->nombre }}</li>
                    @endforeach
                </ul>
            @endif

        </div>
    </div>

    {{-- ======================= --}}
    {{-- ===== AUXILIARES ====== --}}
    {{-- ======================= --}}
    <div class="card mb-3">
        <div class="card-header alert alert-secondary">Auxiliares</div>
        <div class="card-body table-responsive">

            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Publicador</th>
                        <th>Horas</th>
                        <th>Cursos</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalAux = 0;
                    @endphp
                    @foreach($publicadores->where('precursor', null) as $pub)
                        @php
                            $reg = $pub->registros->where('mes', request('mes'))->where('a_servicio', request('anho'))->first();
                        @endphp
                        @if($reg && $reg->aux == '(Auxiliar)')
                            <tr>
                                <td>{{ $pub->nombre }}</td>
                                <td class="text-center">{{ $reg->horas ?? '-' }}</td>
                                <td class="text-center">{{ $reg->cursos ?? '-' }}</td>
                                <td>{{ $reg->notas ?? '' }}</td>
                            </tr>
                            @php $totalAux++; @endphp
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Total Auxiliares del mes: {{ $totalAux }}</td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>

    {{-- ======================= --}}
    {{-- ===== REGULARES ======= --}}
    {{-- ======================= --}}
    <div class="card mb-3">
        <div class="card-header alert alert-dark">Regulares</div>
        <div class="card-body table-responsive">

            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Publicador</th>
                        <th>Horas</th>
                        <th>Cursos</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPrec = 0;
                        $sinInformePrec = [];
                    @endphp
                    @foreach($publicadores->where('precursor', '1') as $pub)
                        @php
                            $reg = $pub->registros->where('mes', request('mes'))->where('a_servicio', request('anho'))->first();
                        @endphp
                        @if($reg)
                            <tr>
                                <td>{{ $pub->nombre }}</td>
                                <td class="text-center">{{ $reg->horas ?? '-' }}</td>
                                <td class="text-center">{{ $reg->cursos ?? '-' }}</td>
                                <td>{{ $reg->notas ?? '' }}</td>
                            </tr>
                            @php $totalPrec++; @endphp
                        @else
                            @php $sinInformePrec[] = $pub; @endphp
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Total Regulares: {{ $publicadores->where('precursor', '1')->count() }} | Informaron: {{ $totalPrec }} | Sin informar: {{ count($sinInformePrec) }}</td>
                    </tr>
                </tfoot>
            </table>

            {{-- Regulares sin informar --}}
            @if(count($sinInformePrec))
                <p><b>Sin Informe:</b></p>
                <ul>
                    @foreach($sinInformePrec as $pub)
                        <li>{{ $pub->nombre }}</li>
                    @endforeach
                </ul>
            @endif

        </div>
    </div>

    @endif

</div>
@endsection
