@extends('layouts.app')

@section('content')
<div class="container">

    <h5>Enviar Informes Mensuales</h5>

    {{-- Filtro --}}
@php
$mesActual = now()->month;
$añoServicioActual = ($mesActual >= 9) ? now()->year + 1 : now()->year;
@endphp

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
            @for($y = $añoServicioActual; $y >= 2024; $y--)
                <option value="{{ $y }}" {{ request('anho', $añoServicioActual) == $y ? 'selected' : '' }}>
                    {{ $y }} (Año de Servicio)
                </option>
            @endfor
        </select>
    </div>

    <div class="col-12 col-lg-2">
        <button class="btn btn-secondary btn-sm w-100" type="submit">Calcular</button>
    </div>
</form>


    @if(request('mes') && request('anho'))

   <div class="alert alert-info">
    <i class="fa-solid fa-calendar-check me-1"></i>
    <strong>Informe del mes de {{ request('mes') }}</strong>
    — Año de Servicio <strong>{{ request('anho') }}</strong>
</div>

    {{-- ======================= --}}
{{-- ===== PUBLICADORES ==== --}}
{{-- ======================= --}}
<div class="mb-3">
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>PUBLICADORES</th>
                    <th>Participación</th>
                    <th>Cursos</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPub = 0;
                    $totalCursosPub = 0;
                    $sinInformePub = [];
                @endphp
                @foreach($publicadores->where('precursor', null) as $pub)
                    @php
                        $reg = $pub->registros->where('mes', request('mes'))->where('a_servicio', request('anho'))->first();
                    @endphp
                    @if($reg && empty($reg->aux))
                        <tr>
                            <td>{{ $pub->nombre }}</td>
                            <td class="text-center">@if($reg->actividad) <i class="fa fa-check text-success"></i> @endif</td>
                            <td class="text-center">{{ $reg->cursos ?? '-' }}</td>
                            <td>{{ $reg->notas ?? '' }}</td>
                        </tr>
                        @php 
                            $totalPub++; 
                            $totalCursosPub += $reg->cursos ?? 0;
                        @endphp
                    @elseif(!$reg)
                        @php $sinInformePub[] = $pub; @endphp
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary">
                    <td><b>Totales</b></td>
                    <td></td>
                    <td class="text-center"><b>{{ $totalCursosPub }}</b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4">Total Publicadores: {{ $publicadores->where('precursor', null)->count() }} | Informaron: {{ $totalPub }} | Sin informar: {{ count($sinInformePub) }}</td>
                </tr>
                @if(count($sinInformePub))
                <tr>
                    <td colspan="4">
                        <b>Sin Informe:</b><br>
                        <ul class="mb-0 ps-3">
                            @foreach($sinInformePub as $pub)
                                <li>{{ $pub->nombre }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>
</div>


    {{-- ======================= --}}
    {{-- ===== AUXILIARES ====== --}}
    {{-- ======================= --}}
    <div class="mb-3">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>PREC. AUXILIARES</th>
                        <th>Horas</th>
                        <th>Cursos</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalAux = 0;
                        $totalHorasAux = 0;
                        $totalCursosAux = 0;
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
                            @php 
                                $totalAux++; 
                                $totalHorasAux += $reg->horas ?? 0;
                                $totalCursosAux += $reg->cursos ?? 0;
                            @endphp
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <td><b>Totales</b></td>
                        <td class="text-center"><b>{{ $totalHorasAux }}</b></td>
                        <td class="text-center"><b>{{ $totalCursosAux }}</b></td>
                        <td></td>
                    </tr>
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
<div class="mb-3">
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover">
            <thead class="table-success">
                <tr>
                    <th>PREC. REGULARES</th>
                    <th>Horas</th>
                    <th>Cursos</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPrec = 0;
                    $totalHorasPrec = 0;
                    $totalCursosPrec = 0;
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
                        @php 
                            $totalPrec++; 
                            $totalHorasPrec += $reg->horas ?? 0;
                            $totalCursosPrec += $reg->cursos ?? 0;
                        @endphp
                    @else
                        @php $sinInformePrec[] = $pub; @endphp
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary">
                    <td><b>Totales</b></td>
                    <td class="text-center"><b>{{ $totalHorasPrec }}</b></td>
                    <td class="text-center"><b>{{ $totalCursosPrec }}</b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4">Total Regulares: {{ $publicadores->where('precursor', '1')->count() }} | Informaron: {{ $totalPrec }} | Sin informar: {{ count($sinInformePrec) }}</td>
                </tr>
                @if(count($sinInformePrec))
                <tr>
                    <td colspan="4">
                        <b>Sin Informe:</b><br>
                        <ul class="mb-0 ps-3">
                            @foreach($sinInformePrec as $pub)
                                <li>{{ $pub->nombre }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>
</div>


    @endif

</div>
@endsection
