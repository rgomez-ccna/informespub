@extends('layouts.app')
@section('content')
<div class="container mb-3">

    <a href="{{ route('asist.create') }}" class="btn btn-secondary btn-sm mb-3">Registrar (mes) <i class="fa fa-calendar-alt ms-2"></i></a>

    <div class="row g-3">

        {{-- FIN DE SEMANA --}}
        <div class="col-12 col-md-6">
            
            <div class="text-center fw-bold fs-6 mb-2 alert alert-primary">FIN DE SEMANA</div>

            @foreach($asistencias['FS'] ?? [] as $year => $data)
            <div class="card col-12 py-1 px-1 mb-3">
                <div class="card-header">
                    <h5 class="text-center">
                        Asistencia Reuniones [ FIN DE SEMANA ] - Año {{ $year }}
                    </h5>
                </div>
                <div class="card-body table-responsive py-1 px-1">
                    <table class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th># Reuniones</th>
                                <th>Asistencia Total</th>
                                <th>Promedio Semanal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totA=0;$totR=0; @endphp
                            @foreach($data as $a)
                            <tr>
                                <td>{{ $a->mes }}</td>
                                <td>{{ $a->reuniones }}</td>
                                <td>{{ $a->total }}</td>
                                <td>{{ $a->reuniones!=0 ? round($a->total/$a->reuniones,2) : 0 }}</td>
                                <td class="text-center">
                                    <a href="{{ route('asist.edit', $a->id) }}" class="text-muted" style="font-size:11px;">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>

                                @php $totA+=$a->total;$totR+=$a->reuniones; @endphp
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="text-end"><b>Promedio Mensual:</b></td>
                                <td><b>{{ $totR>0?round($totA/$totR,2):0 }}</b></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

        </div>


        {{-- ENTRE SEMANA --}}
        <div class="col-12 col-md-6">

            <div class="text-center fw-bold fs-6 mb-2 alert alert-info">ENTRE SEMANA</div>

            @foreach($asistencias['ES'] ?? [] as $year => $data)
            <div class="card col-12 py-1 px-1 mb-3">
                <div class="card-header">
                    <h5 class="text-center">
                        Asistencia Reuniones [ ENTRE SEMANA ] - Año {{ $year }}
                    </h5>
                </div>
                <div class="card-body table-responsive py-1 px-1">
                    <table class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th># Reuniones</th>
                                <th>Asistencia Total</th>
                                <th>Promedio Semanal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totA=0;$totR=0; @endphp
                            @foreach($data as $a)
                            <tr>
                                <td>{{ $a->mes }}</td>
                                <td>{{ $a->reuniones }}</td>
                                <td>{{ $a->total }}</td>
                                <td>{{ $a->reuniones!=0 ? round($a->total/$a->reuniones,2) : 0 }}</td>
                                 <td class="text-center">
                                    <a href="{{ route('asist.edit', $a->id) }}" class="text-muted" style="font-size:11px;">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                @php $totA+=$a->total;$totR+=$a->reuniones; @endphp
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="text-end"><b>Promedio Mensual:</b></td>
                                <td><b>{{ $totR>0?round($totA/$totR,2):0 }}</b></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

        </div>

    </div>

</div>
@endsection
