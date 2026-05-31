<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Totales S-21</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        html, body {
            height: auto;
            min-height: 100%;
        }

        body {
            background: #f8f9fa;
            font-size: 13px;
        }

        .tabla-s21 th,
        .tabla-s21 td {
            vertical-align: middle;
            padding: .35rem .4rem;
            border-color: #dee2e6;
        }

        .card {
            border-color: #dee2e6;
        }

        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .titulo-s21 {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .subtitulo-s21 {
            font-size: .82rem;
            color: #6c757d;
        }

        .table thead th {
            font-weight: 700;
            background: #f8f9fa;
        }
    </style>
</head>

<body>

<div class="container-fluid py-2 min-vh-100">

    <div class="row g-3">

        {{-- ================= PRECURSORES REGULARES ================= --}}
        <div class="col-12 col-md-4">

            @foreach($precursoresRegulares as $anio => $registrosAnio)
                <div class="card mb-3">
                    <div class="card-header py-2 px-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="titulo-s21">Precursores regulares</div>
                                <div class="subtitulo-s21">Resumen mensual de totales</div>
                            </div>

                            <div class="text-end">
                                <div class="fw-semibold">Año de servicio</div>
                                <div class="h5 fw-bold">{{ $anio }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body table-responsive py-1 px-1">
                        <table class="table table-sm table-bordered table-hover tabla-s21 mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 28%;">Mes</th>
                                    <th style="width: 18%;">Cursos bíblicos</th>
                                    <th style="width: 18%;">Horas</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($registrosAnio as $registro)
                                    <tr class="text-center">
                                        <td class="text-start">{{ $registro->mes }}</td>
                                        <td>{{ $registro->cursos > 0 ? $registro->cursos : '—' }}</td>
                                        <td>{{ $registro->horas > 0 ? $registro->horas : '—' }}</td>
                                        <td>{{ $registro->notas_cantidad > 0 ? $registro->notas_cantidad . ' P. Reg.' : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr class="fw-bold text-center">
                                    <td class="text-end">Total</td>
                                    <td>{{ $registrosAnio->sum('cursos') > 0 ? $registrosAnio->sum('cursos') : '—' }}</td>
                                    <td>{{ $registrosAnio->sum('horas') > 0 ? $registrosAnio->sum('horas') : '—' }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach

        </div>

        {{-- ================= PRECURSORES AUXILIARES ================= --}}
        <div class="col-12 col-md-4">

            @foreach($precursoresAuxiliares as $anio => $registrosAnio)
                <div class="card mb-3">
                    <div class="card-header py-2 px-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="titulo-s21">Precursores auxiliares</div>
                                <div class="subtitulo-s21">Resumen mensual de totales</div>
                            </div>

                            <div class="text-end">
                                <div class="fw-semibold">Año de servicio</div>
                                <div class="h5 fw-bold">{{ $anio }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body table-responsive py-1 px-1">
                        <table class="table table-sm table-bordered table-hover tabla-s21 mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 28%;">Mes</th>
                                    <th style="width: 18%;">Cursos bíblicos</th>
                                    <th style="width: 18%;">Horas</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($registrosAnio as $registro)
                                    <tr class="text-center">
                                        <td class="text-start">{{ $registro->mes }}</td>
                                        <td>{{ $registro->cursos > 0 ? $registro->cursos : '—' }}</td>
                                        <td>{{ $registro->horas > 0 ? $registro->horas : '—' }}</td>
                                        <td>{{ $registro->notas_cantidad > 0 ? $registro->notas_cantidad . ' P. Aux.' : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr class="fw-bold text-center">
                                    <td class="text-end">Total</td>
                                    <td>{{ $registrosAnio->sum('cursos') > 0 ? $registrosAnio->sum('cursos') : '—' }}</td>
                                    <td>{{ $registrosAnio->sum('horas') > 0 ? $registrosAnio->sum('horas') : '—' }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach

        </div>

        {{-- ================= PUBLICADORES ================= --}}
<div class="col-12 col-md-4">

    @foreach($publicadoresActivos as $anio => $registrosAnio)
        <div class="card mb-3">
            <div class="card-header py-2 px-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="titulo-s21">Publicadores</div>
                        <div class="subtitulo-s21">Resumen mensual de totales</div>
                    </div>

                    <div class="text-end">
                        <div class="fw-semibold">Año de servicio</div>
                        <div class="h5 fw-bold">{{ $anio }}</div>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive py-1 px-1">
                <table class="table table-sm table-bordered table-hover tabla-s21 mb-0">
                    <thead>
                        <tr class="text-center">
                            <th style="width: 34%;">Mes</th>
                            <th style="width: 28%;">Cursos bíblicos</th>
                            <th>Publicadores</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($registrosAnio as $registro)
                            <tr class="text-center">
                                <td class="text-start">{{ $registro->mes }}</td>
                                <td>{{ $registro->cursos > 0 ? $registro->cursos : '—' }}</td>
                                <td>  {{ $registro->cantidad > 0 ? $registro->cantidad . ' Pub.' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="fw-bold text-center">
                            <td class="text-end">Total</td>
                            <td>{{ $registrosAnio->sum('cursos') > 0 ? $registrosAnio->sum('cursos') : '—' }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endforeach

</div>

    </div>
</div>

</body>
</html>