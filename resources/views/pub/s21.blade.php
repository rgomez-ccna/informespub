@extends('layouts.app')
@section('content')
<div class="container">

<a href="{{ route('pub.listado') }}" class="btn btn-secondary btn-sm mb-2">Volver</a>

<div class="card">
    <div class="card-header">
        <h5>Tarjeta S-21 - {{ $publicador->nombre }}</h5>
    </div>
    <div class="card-body">

        <p><b>Grupo:</b> {{ $publicador->grupo }}</p>
        <p><b>Fecha de Bautismo:</b> {{ $publicador->fbautismo }}</p>
        <p><b>Rol:</b> {{ $publicador->rol }}</p>

        @foreach ($registros as $anio => $registrosAnio)
            <h6 class="mt-3">Año de Servicio: {{ $anio }}</h6>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Participación</th>
                        <th>Precursor Auxiliar</th>
                        <th>Horas</th>
                        <th>Cursos</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registrosAnio as $reg)
                        <tr>
                            <td>{{ $reg->mes }}</td>
                            <td class="text-center"><input type="checkbox" {{ $reg->videos == 'PREDICO' ? 'checked' : '' }} disabled></td>
                            <td class="text-center"><input type="checkbox" {{ $reg->aux == '(Auxiliar)' ? 'checked' : '' }} disabled></td>
                            <td>{{ $reg->horas }}</td>
                            <td>{{ $reg->cursos }}</td>
                            <td>{{ $reg->notas }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><b>Total Horas</b></td>
                        <td><b>{{ $registrosAnio->sum('horas') }}</b></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        @endforeach

    </div>
</div>

</div>
@endsection
