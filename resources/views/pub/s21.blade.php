@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10">
            <a href="{{ route('pub.listado') }}" class="btn btn-secondary btn-sm mb-2">
                <i class="fa fa-arrow-left"></i> Volver a la Lista de PUB
            </a>
            

            @foreach($registros as $anio => $registrosAnio)
                <div class="card" data-nombre="{{ $publicador->nombre }}">
                    <div class="card-header py-1 px-1 m-0 table-responsive">
                        <table class="w-100" style="font-size: 1.2em;">
                            <tbody>
                                <tr>
                                    <td>Nombre:</td>
                                    <td><b>{{ $publicador->nombre }}</b></td>
                                    <td class="text-right"></td>
                                    <td><input class="form-check-input" type="checkbox" {{ $publicador->hombre ? 'checked' : '' }} disabled> <small>Hombre</small></td>
                                    <td><input class="form-check-input" type="checkbox" {{ $publicador->mujer ? 'checked' : '' }} disabled> <small>Mujer</small></td>
                                </tr>
                                <tr>
                                    <td>F. de Bautismo:</td>
                                    <td>{{ $publicador->fbautismo }}</td>
                                    <td></td>
                                    <td><input class="form-check-input" type="checkbox" {{ $publicador->oo ? 'checked' : '' }} disabled> <small>O.o.</small></td>
                                    <td><input class="form-check-input" type="checkbox" {{ $publicador->ungido ? 'checked' : '' }} disabled> <small>Ungido</small></td>
                                </tr>
                                <tr>
                                    <td>Fecha de Nac.:</td>
                                    <td>{{ $publicador->fnacimiento }}</td>
                                    <td><input class="form-check-input" type="checkbox" {{ $publicador->anciano ? 'checked' : '' }} disabled> <small>Anciano</small></td>
                                    <td><input class="form-check-input" type="checkbox" {{ $publicador->sv ? 'checked' : '' }} disabled> <small>S.Ministerial</small></td>
                                    <td><input class="form-check-input" type="checkbox" {{ $publicador->precursor ? 'checked' : '' }} disabled> <small>P.Regular</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body table-responsive py-1 px-1">
                        <table class="table table-sm table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 140px; text-align: center;">Año de servicio: {{ $anio }}</th>
                                    <th style="width: 140px; text-align: center;">Participación en el ministerio</th>
                                    <th style="width: 140px; text-align: center;">Cursos bíblicos</th>
                                    <th style="width: 140px; text-align: center;">Precursor auxiliar</th>
                                    <td style="width: 140px; text-align: center;">
                                        <strong>Horas</strong><br><small>(Si es precursor o misionero que sirve en el campo)</small>
                                    </td>
                                    <th style="text-align: center;">Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registrosAnio as $registro)
                                    <tr>
                                        <td style="text-align: center;">{{ $registro->mes }}</td>
                                        <td style="text-align: center;"><input class="form-check-input" type="checkbox" {{ $registro->videos == 'PREDICO' ? 'checked' : '' }} disabled></td>
                                        <td style="text-align: center;">{{ $registro->cursos }}</td>
                                        <td style="text-align: center;"><input class="form-check-input" type="checkbox" {{ $registro->aux == '(Auxiliar)' ? 'checked' : '' }} disabled></td>
                                        <td style="text-align: center;">{{ $registro->horas }}</td>
                                        <td style="text-align: center;">{{ $registro->notas }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right;"><strong>Total</strong></td>
                                    <td style="text-align: center;"><strong>{{ $registrosAnio->sum('horas') }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
            @endforeach

        </div>
    </div>
</div>

@endsection
