@php
    use Carbon\Carbon;

    $programas = $programas ?? collect([$programa ?? null])->filter();

    $nombreAsignado = function ($parte, $rol, $sala = 'general') {
        if (!$parte) {
            return '';
        }

        $item = $parte->asignaciones
            ->first(fn ($a) => $a->rol === $rol && $a->sala === $sala);

        return $item?->publicador?->nombre ?? '';
    };

    $hora = fn ($parte) => $parte && $parte->hora_inicio ? substr($parte->hora_inicio, 0, 5) : '';
    $horaFin = fn ($parte) => $parte && $parte->hora_fin ? substr($parte->hora_fin, 0, 5) : '';

    $duracion = function ($parte) {
        return $parte && $parte->duracion_minutos
            ? '(' . $parte->duracion_minutos . ' min.)'
            : '';
    };

    $sumarMinutos = function ($horaBase, $minutos) {
        if (!$horaBase) {
            return '';
        }

        return Carbon::createFromFormat('H:i', substr($horaBase, 0, 5))
            ->addMinutes($minutos)
            ->format('H:i');
    };

    $textoSemana = function ($fecha) {
        $fecha = Carbon::parse($fecha);
        $inicio = $fecha->copy()->startOfWeek(Carbon::MONDAY);
        $fin = $fecha->copy()->endOfWeek(Carbon::SUNDAY);

        $mesInicio = mb_strtoupper($inicio->locale('es')->translatedFormat('F'));
        $mesFin = mb_strtoupper($fin->locale('es')->translatedFormat('F'));

        return $mesInicio !== $mesFin
            ? $inicio->format('d') . ' DE ' . $mesInicio . ' A ' . $fin->format('d') . ' DE ' . $mesFin
            : $inicio->format('d') . '-' . $fin->format('d') . ' DE ' . $mesFin;
    };

    $tituloSeguro = function ($parte, $default = '') {
        $titulo = trim($parte->titulo ?? '');
        return $titulo !== '' ? $titulo : $default;
    };
@endphp

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Programa Vida y Ministerio</title>

<style>
@page {
    margin: 6mm 6mm; /* MARGEN GENERAL DEL PDF */
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: DejaVu Sans, sans-serif;
    color: #111;
    font-size: 10.4px; /* TAMAÑO GENERAL DE TODO EL DOCUMENTO */
}

.page {
    page-break-after: always;
    height: 285mm; /* ALTO TOTAL DE LA PÁGINA */
    overflow: hidden;
}

.page:last-child {
    page-break-after: auto;
}

.programa-card.blank {
    visibility: hidden;
}

/* =========================
   TÍTULO GENERAL DEL PDF
   ========================= */

.doc-title {
    border: 1.2px solid #3b2f63;
    border-radius: 5px;
    text-align: center;
    padding: 3px 5px;
    height: 10.5mm; /* ALTO DEL TÍTULO PRINCIPAL */
    margin-bottom: 2mm; /* ESPACIO ENTRE TÍTULO Y PRIMER PROGRAMA */
}

.doc-title h1 {
    margin: 0;
    font-size: 14.4px; /* TAMAÑO DEL TÍTULO PRINCIPAL */
    line-height: 14.8px;
    color: #3b2f63;
    font-weight: bold;
    text-transform: uppercase;
}

.doc-title div {
    font-size: 9.4px; /* TAMAÑO DEL SUBTÍTULO DEL PERÍODO */
    color: #3b2f63;
    font-weight: bold;
    margin-top: 1px;
}

.page:not(.first-page) .doc-title {
    display: none;
}

/* =========================
   ESPACIO DE CADA BLOQUE
   ========================= */

.slot {
    width: 100%;
    height: 130mm; /* ALTO DE CADA PROGRAMA EN LA PRIMERA PÁGINA */
    overflow: hidden;
}

.slot-uno {
    margin-bottom: 2mm; /* ESPACIO ENTRE PROGRAMA DE ARRIBA Y ABAJO */
}

.slot-dos {
    margin-top: 0;
}

.first-page .slot {
    height: 130mm;
}

.first-page .programa-card {
    height: 126mm; /* ALTO INTERNO DE LA CARD EN PRIMERA PÁGINA */
}

.page:not(.first-page) .slot {
    height: 139mm; /* ALTO DE CADA PROGRAMA EN PÁGINAS SIN TÍTULO */
}

.page:not(.first-page) .programa-card {
    height: 136mm;
}

/* =========================
   CARD PRINCIPAL DEL PROGRAMA
   ========================= */

.programa-card {
    border: 1.3px solid #3b2f63;
    border-radius: 6px;
    padding: 5px 5px; /* AIRE INTERNO DE TODA LA CARD */
    overflow: hidden;
    background: #fff;
    margin-bottom: 0;
}

.programa-card.aviso {
    background: #fbfbfb;
}

/* =========================
   ENCABEZADO: SEMANA + PUBLICADORES
   IMPORTANTE:
   70% izquierda | 30% derecha
   Así la columna derecha queda alineada con la columna principal inferior.
   ========================= */

.program-header {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin-bottom: 3px; /* ESPACIO DEBAJO DEL ENCABEZADO */
}

.program-header td {
    vertical-align: top;
}

.head-left {
    width: 70%; /* ANCHO DEL BLOQUE IZQUIERDO: FECHA, LECTURA, CANCIÓN */
    padding-right: 5px;
}

.head-right {
    width: 30%; /* ANCHO DEL BLOQUE DERECHO: PRESIDENTE, AYUDANTES, ORACIÓN */
    text-align: left;
    padding-left: 6px;
}

.week-title {
    font-size: 11.8px; /* TAMAÑO DE FECHA + LECTURA SEMANAL */
    line-height: 1.15;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 3px;
    color: #111;
}

.line {
    font-size: 10.3px; /* TAMAÑO DE CANCIÓN E INTRODUCCIÓN */
    line-height: 1.16;
    margin-bottom: 1px;
}

.line-time {
    display: inline-block;
    width: 10mm; /* ANCHO DE LA HORA EN EL ENCABEZADO */
    color: #555;
    font-weight: bold;
    font-size: 9.3px; /* TAMAÑO DE LA HORA EN EL ENCABEZADO */
}

.dot {
    color: #9f1239;
    font-size: 11.4px; /* TAMAÑO DEL PUNTO ROJO */
    font-weight: bold;
}

.assign-line {
    line-height: 1.1; /* ALTO ENTRE LÍNEAS DE PUBLICADORES DEL ENCABEZADO */
    margin-bottom: 1px;
    white-space: normal;
    text-align: left;
}

.assign-label {
    font-size: 7.5px; /* TAMAÑO DE "Presidente:", "Ayudante:", ETC. */
    color: #555;
    font-weight: bold;
}

.assign-name {
    font-size: 9.6px; /* TAMAÑO DEL NOMBRE EN EL ENCABEZADO */
    font-weight: bold;
    margin-left: 2px;
}

/* =========================
   BARRAS DE SECCIÓN
   ========================= */

.section-bar {
    color: #fff;
    font-size: 10.1px; /* TAMAÑO DEL TEXTO DE LAS BARRAS */
    font-weight: bold;
    padding: 2px 6px; /* ALTO Y AIRE DE LAS BARRAS */
    line-height: 1.05;
    margin-top: 3px; /* ESPACIO ARRIBA DE CADA BARRA */
    margin-bottom: 2px; /* ESPACIO DEBAJO DE CADA BARRA */
    text-transform: uppercase;
}

.bar-tesoros {
    background: #2a6f74;
}

.bar-maestros {
    background: #c69214;
}

.bar-vida {
    background: #a73229;
}

/* =========================
   TABLA PRINCIPAL
   IMPORTANTE:
   El ancho real está forzado en el partial:
   10% hora | 30% tema | 60% asignados
   Dentro de asignados:
   50% auxiliar | 50% principal
   Por eso la columna principal empieza visualmente en 70%.
   ========================= */

.program-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    border-spacing: 0;
}

.program-table tr {
    page-break-inside: avoid;
}

.program-table td {
    vertical-align: top;
    padding-top: 1px; /* ESPACIO VERTICAL ARRIBA DE CADA FILA */
    padding-bottom: 1px; /* ESPACIO VERTICAL ABAJO DE CADA FILA */
    line-height: 1.13;
}

/* COLUMNA DE HORARIOS */
.time {
    color: #555;
    font-weight: bold;
    font-size: 9.3px; /* TAMAÑO DE LOS HORARIOS */
    white-space: nowrap;
    padding-left: 0 !important;
    padding-right: 3px !important; /* ESPACIO ENTRE HORA Y TEMA */
}

/* COLUMNA DE TEMAS */
.topic {
    font-size: 10.1px; /* TAMAÑO DEL TEXTO DEL TEMA */
    padding-left: 0 !important;
    padding-right: 5px !important; /* ESPACIO ENTRE TEMA Y ASIGNADOS */
    white-space: normal;
    word-break: normal;
}

.topic strong {
    font-weight: bold;
}

/* COLUMNA GRANDE DE ASIGNADOS */
.assign-cell {
    font-size: 9px;
    font-weight: bold;
    line-height: 1.1;
    white-space: normal;
    word-break: normal;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

/* =========================
   ASIGNADOS ALINEADOS:
   Izquierda = auxiliar cuando existe
   Derecha = sala principal / asignado principal siempre
   ========================= */

.assign-layout {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    border-spacing: 0;
}

.assign-layout td {
    width: 50%;
    vertical-align: top;
    text-align: left;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    font-size: 9.1px;
    font-weight: bold;
    line-height: 1.08;
    white-space: normal;
    word-break: normal;
}

/* COLUMNA AUXILIAR: queda en el medio visual */
.assign-layout .assign-aux {
    padding-left: 0 !important;
    padding-right: 6px !important;
    text-align: left;
}

/* COLUMNA PRINCIPAL: siempre última columna derecha */
.assign-layout .assign-principal {
    padding-left: 6px !important;
    padding-right: 0 !important;
    text-align: left;
}

/* Cuando no hay auxiliar, esta celda invisible mantiene la principal alineada a la derecha */
.assign-layout .assign-empty {
    color: transparent;
    font-size: 1px;
    line-height: 1px;
}

/* Encabezado pequeño: Sala auxiliar | Sala principal */
.assign-head td {
    font-size: 6.4px;
    color: #555;
    font-weight: bold;
    padding-bottom: 1px !important;
    line-height: 1;
}

/* Etiqueta pequeña dentro de la columna principal: Disertante, Oración, etc. */
.assign-simple-label {
    color: #555;
    font-size: 6.8px;
    font-weight: bold;
    margin-right: 3px;
}

/* Nombre principal */
.assign-simple-name {
    font-size: 9.1px;
    font-weight: bold;
}

/* =========================
   CLASES ANTIGUAS
   Se dejan para compatibilidad si alguna fila vieja aún las usa
   ========================= */

/* TEXTO PEQUEÑO: Disertante, Estudiante, Est./Ayud., Cond./Lector */
.row-label {
    color: #555;
    font-size: 6.8px;
    font-weight: bold;
    margin-right: 3px;
}

/* NOMBRES DE PUBLICADORES EN LAS FILAS */
.row-person {
    font-size: 9.1px;
    font-weight: bold;
}

.assign-title {
    line-height: 1;
    margin-bottom: 1px;
}

/* DURACIÓN: (10 min.), (5 min.), ETC. */
.duration {
    color: #444;
    font-size: 8.3px;
}

/* =========================
   TABLA INTERNA ANTIGUA DE SALAS
   Se mantiene por seguridad, pero el nuevo partial usa assign-layout.
   ========================= */

.rooms-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    border-spacing: 0;
    margin-top: 1px;
}

.rooms-table td {
    width: 50%;
    padding: 0 7px 0 0;
    vertical-align: top;
    font-size: 10px;
    font-weight: bold;
    line-height: 1.08;
    white-space: normal;
    word-break: normal;
    text-align: left;
}

.rooms-head td {
    font-size: 6.4px;
    color: #555;
    font-weight: bold;
    padding-bottom: 1px;
    line-height: 1;
}

.empty-line {
    color: #777;
    font-style: italic;
    font-size: 8.6px;
}

/* =========================
   CARD DE AVISO
   ========================= */

.aviso-box {
    height: 100%;
    text-align: center;
}

.aviso-top {
    text-align: left;
    font-size: 10.6px; /* TAMAÑO DE FECHA EN AVISO */
    font-weight: bold;
    text-transform: uppercase;
    color: #111;
    padding: 2px 0 0 2px;
}

.aviso-center {
    padding-top: 39mm; /* POSICIÓN VERTICAL DEL AVISO */
}

.aviso-title {
    display: inline-block;
    border: 1.2px solid #3b2f63;
    border-radius: 4px;
    color: #3b2f63;
    font-size: 16.4px; /* TAMAÑO DEL TÍTULO "AVISO" */
    font-weight: bold;
    text-transform: uppercase;
    padding: 4px 18px;
    margin-bottom: 8px;
}

.aviso-text {
    font-size: 14px; /* TAMAÑO DEL TEXTO DEL AVISO */
    font-weight: bold;
    color: #111;
    line-height: 1.35;
    padding: 0 22px;
    text-transform: uppercase;
}
</style>
</head>
<body>

@foreach($programas->values()->chunk(2) as $grupoOriginal)
    @php
        $grupo = $grupoOriginal->values();
    @endphp

    <div class="page {{ $loop->first ? 'first-page' : '' }}">

        <div class="doc-title">
            <h1>Programa para la reunión Vida y Ministerio Cristianos</h1>
            <div>{{ $tituloPeriodo ?? '' }}</div>
        </div>

        <div class="slot slot-uno">
            @include('vida_ministerio.partials.pdf_programa_card', [
                'programa' => $grupo->get(0),
                'nombreAsignado' => $nombreAsignado,
                'hora' => $hora,
                'horaFin' => $horaFin,
                'duracion' => $duracion,
                'sumarMinutos' => $sumarMinutos,
                'textoSemana' => $textoSemana,
                'tituloSeguro' => $tituloSeguro,
            ])
        </div>

        <div class="slot slot-dos">
            @if($grupo->get(1))
                @include('vida_ministerio.partials.pdf_programa_card', [
                    'programa' => $grupo->get(1),
                    'nombreAsignado' => $nombreAsignado,
                    'hora' => $hora,
                    'horaFin' => $horaFin,
                    'duracion' => $duracion,
                    'sumarMinutos' => $sumarMinutos,
                    'textoSemana' => $textoSemana,
                    'tituloSeguro' => $tituloSeguro,
                ])
            @else
                <div class="programa-card blank"></div>
            @endif
        </div>

    </div>
@endforeach

</body>
</html>

