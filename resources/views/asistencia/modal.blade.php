<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Asistencia</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        html, body {
            height: auto;
            min-height: 100%;
        }
    </style>
</head>

<body class="bg-light">

<div class="container-fluid py-2 min-vh-100">

    <div class="row g-3">

        {{-- ================= FIN DE SEMANA ================= --}}
        <div class="col-12 col-md-6">

            <div class="alert alert-primary text-center fw-bold py-2 mb-2">
                FIN DE SEMANA
            </div>

            {{-- scroll SOLO en md+ --}}
            <div class="h-md-100 overflow-md-auto pe-md-1">

                @foreach($asistencias['FS'] ?? [] as $year => $data)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="text-center mb-0">
                            Asistencia Reuniones – Año {{ $year }}
                        </h6>
                    </div>

                    <div class="card-body p-2 table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>Mes</th>
                                    <th># Reun.</th>
                                    <th>Total</th>
                                    <th>Prom.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totA=0; $totR=0; @endphp
                                @foreach($data as $a)
                                <tr class="text-center">
                                    <td class="text-start">{{ $a->mes }}</td>
                                    <td>{{ $a->reuniones }}</td>
                                    <td>{{ $a->total }}</td>
                                    <td>{{ $a->reuniones ? round($a->total / $a->reuniones, 2) : 0 }}</td>
                                    @php
                                        $totA += $a->total;
                                        $totR += $a->reuniones;
                                    @endphp
                                </tr>
                                @endforeach
                                <tr class="fw-bold text-center table-secondary">
                                    <td colspan="3" class="text-end">Promedio mensual</td>
                                    <td>{{ $totR ? round($totA / $totR, 2) : 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- ================= ENTRE SEMANA ================= --}}
        <div class="col-12 col-md-6">

            <div class="alert alert-info text-center fw-bold py-2 mb-2">
                ENTRE SEMANA
            </div>

            {{-- scroll SOLO en md+ --}}
            <div class="h-md-100 overflow-md-auto pe-md-1">

                @foreach($asistencias['ES'] ?? [] as $year => $data)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="text-center mb-0">
                            Asistencia Reuniones – Año {{ $year }}
                        </h6>
                    </div>

                    <div class="card-body p-2 table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>Mes</th>
                                    <th># Reun.</th>
                                    <th>Total</th>
                                    <th>Prom.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totA=0; $totR=0; @endphp
                                @foreach($data as $a)
                                <tr class="text-center">
                                    <td class="text-start">{{ $a->mes }}</td>
                                    <td>{{ $a->reuniones }}</td>
                                    <td>{{ $a->total }}</td>
                                    <td>{{ $a->reuniones ? round($a->total / $a->reuniones, 2) : 0 }}</td>
                                    @php
                                        $totA += $a->total;
                                        $totR += $a->reuniones;
                                    @endphp
                                </tr>
                                @endforeach
                                <tr class="fw-bold text-center table-secondary">
                                    <td colspan="3" class="text-end">Promedio mensual</td>
                                    <td>{{ $totR ? round($totA / $totR, 2) : 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

    </div>
</div>

</body>
</html>
