@php
    $esAviso = ($programa->estado ?? '') === 'aviso';

    $partes = collect($programa->partes ?? [])->sortBy('orden')->values();

    $partePresidente = $partes->firstWhere('tipo_asignacion', 'presidente');
    $parteAyudanteAuditorio = $partes->firstWhere('tipo_asignacion', 'ayudante_auditorio');
    $parteConsejeroAuxiliar = $partes->firstWhere('tipo_asignacion', 'consejero_auxiliar');
    $parteAyudanteAuxiliar = $partes->firstWhere('tipo_asignacion', 'ayudante_auxiliar');

    $parteOracionInicio = $partes
        ->where('seccion', 'encabezado')
        ->where('tipo_asignacion', 'oracion')
        ->first();

    $parteOracionFinal = $partes
        ->where('seccion', 'final')
        ->where('tipo_asignacion', 'oracion')
        ->first();

    $parteTesoro = $partes->firstWhere('tipo_asignacion', 'tesoro');
    $partePerlas = $partes->firstWhere('tipo_asignacion', 'perlas');
    $parteLectura = $partes->firstWhere('tipo_asignacion', 'lectura_biblia');

    $partesMaestros = $partes
        ->where('seccion', 'maestros')
        ->where('tipo_asignacion', 'maestro_estudiante')
        ->values();

    $partesVida = $partes
        ->where('seccion', 'vida')
        ->where('tipo_asignacion', 'vida_cristiana')
        ->values();

    $parteEstudio = $partes->firstWhere('tipo_asignacion', 'estudio_conductor');

    $nombreSalaAuxiliar = $programa->nombre_sala_auxiliar ?: 'Sala auxiliar';

    $hayAuxiliarLectura = $parteLectura
        && $nombreAsignado($parteLectura, 'estudiante', 'auxiliar');

    $horaCancionMedia = $partesVida->first()
        ? $hora($partesVida->first())
        : ($parteEstudio ? $hora($parteEstudio) : '');

    $hayContenidoMaestros = $partesMaestros->contains(function ($parte) use ($nombreAsignado) {
        return trim($parte->titulo ?? '') !== ''
            || $nombreAsignado($parte, 'estudiante', 'principal')
            || $nombreAsignado($parte, 'ayudante', 'principal')
            || $nombreAsignado($parte, 'estudiante', 'auxiliar')
            || $nombreAsignado($parte, 'ayudante', 'auxiliar');
    });

    $hayContenidoVida = $partesVida->contains(function ($parte) use ($nombreAsignado) {
        return trim($parte->titulo ?? '') !== ''
            || $nombreAsignado($parte, 'disertante');
    });

    $timeStyle = 'width:10% !important; min-width:10% !important; max-width:10% !important;';
    $topicStyle = 'width:30% !important; min-width:30% !important; max-width:30% !important;';
    $assignStyle = 'width:60% !important; min-width:60% !important; max-width:60% !important;';
    $restStyle = 'width:90% !important; min-width:90% !important; max-width:90% !important;';
@endphp

<div class="programa-card {{ $esAviso ? 'aviso' : '' }}">

    @if($esAviso)
        <div class="aviso-box">
            <div class="aviso-top">
                {{ $textoSemana($programa->fecha) }}
            </div>

            <div class="aviso-center">
                <div class="aviso-title">{{ $textoSemana($programa->fecha) }}</div>

                <div class="aviso-text">
                    {{ $programa->observaciones ?: 'No hay reunión Vida y Ministerio esta semana.' }}
                </div>
            </div>
        </div>
    @else

        <table class="program-header">
            <tr>
                <td class="head-left">
                    <div class="week-title">
                        {{ $textoSemana($programa->fecha) }}
                        @if($programa->lectura_semanal)
                            | {{ mb_strtoupper($programa->lectura_semanal) }}
                        @endif
                    </div>

                    @if($programa->cancion_inicio)
                        <div class="line">
                            <span class="line-time">{{ $hora($parteOracionInicio) }}</span>
                            <span class="dot">•</span>
                            Canción {{ $programa->cancion_inicio }}
                        </div>
                    @endif

                    <div class="line">
                        <span class="line-time">
                            {{ $parteOracionInicio ? $sumarMinutos($hora($parteOracionInicio), max(($parteOracionInicio->duracion_minutos ?? 6) - 1, 0)) : '' }}
                        </span>
                        <span class="dot">•</span>
                        Palabras de introducción <span class="duration">(1 min.)</span>
                    </div>
                </td>

                <td class="head-right">
                    <div class="assign-line">
                        <span class="assign-label">Presidente:</span>
                        <span class="assign-name">{{ $nombreAsignado($partePresidente, 'principal') }}</span>
                    </div>

                    <div class="assign-line">
                        <span class="assign-label">Ayudante sala principal:</span>
                        <span class="assign-name">{{ $nombreAsignado($parteAyudanteAuditorio, 'principal') }}</span>
                    </div>

                    <div class="assign-line">
                        <span class="assign-label">Consejero sala auxiliar:</span>
                        <span class="assign-name">{{ $nombreAsignado($parteConsejeroAuxiliar, 'principal') }}</span>
                    </div>

                    @if($nombreAsignado($parteAyudanteAuxiliar, 'principal'))
                        <div class="assign-line">
                            <span class="assign-label">Ayudante sala auxiliar:</span>
                            <span class="assign-name">{{ $nombreAsignado($parteAyudanteAuxiliar, 'principal') }}</span>
                        </div>
                    @endif

                    <div class="assign-line">
                        <span class="assign-label">Oración:</span>
                        <span class="assign-name">{{ $nombreAsignado($parteOracionInicio, 'principal') }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-bar bar-tesoros">TESOROS DE LA BIBLIA</div>

        <table class="program-table">
            @if($parteTesoro)
                <tr>
                    <td class="time" width="10%" style="{{ $timeStyle }}">{{ $hora($parteTesoro) }}</td>
                    <td class="topic" width="30%" style="{{ $topicStyle }}">
                        <strong>{{ $parteTesoro->numero ?: 1 }}.</strong>
                        {{ $tituloSeguro($parteTesoro, 'Tema pendiente') }}
                        <span class="duration">{{ $duracion($parteTesoro) }}</span>
                    </td>
                    <td class="assign-cell" width="60%" style="{{ $assignStyle }}">
                        <span class="row-label">Disertante:</span>
                        <span class="row-person">{{ $nombreAsignado($parteTesoro, 'disertante') }}</span>
                    </td>
                </tr>
            @endif

            @if($partePerlas)
                <tr>
                    <td class="time" width="10%" style="{{ $timeStyle }}">{{ $hora($partePerlas) }}</td>
                    <td class="topic" width="30%" style="{{ $topicStyle }}">
                        <strong>{{ $partePerlas->numero ?: 2 }}.</strong>
                        Busquemos perlas escondidas
                        <span class="duration">{{ $duracion($partePerlas) }}</span>
                    </td>
                    <td class="assign-cell" width="60%" style="{{ $assignStyle }}">
                        <span class="row-label">Disertante:</span>
                        <span class="row-person">{{ $nombreAsignado($partePerlas, 'disertante') }}</span>
                    </td>
                </tr>
            @endif

            @if($parteLectura)
                <tr>
                    <td class="time" width="10%" style="{{ $timeStyle }}">{{ $hora($parteLectura) }}</td>
                    <td class="topic" width="30%" style="{{ $topicStyle }}">
                        <strong>{{ $parteLectura->numero ?: 3 }}.</strong>
                        Lectura de la Biblia
                        <span class="duration">{{ $duracion($parteLectura) }}</span>
                    </td>
                    <td class="assign-cell" width="60%" style="{{ $assignStyle }}">
                        <div class="assign-title">
                            <span class="row-label">Estudiante:</span>
                        </div>

                        @if($hayAuxiliarLectura)
                            <table class="rooms-table">
                                <tr class="rooms-head">
                                    <td>{{ $nombreSalaAuxiliar }}</td>
                                    <td>Sala principal</td>
                                </tr>
                                <tr>
                                    <td>{{ $nombreAsignado($parteLectura, 'estudiante', 'auxiliar') }}</td>
                                    <td>{{ $nombreAsignado($parteLectura, 'estudiante', 'principal') }}</td>
                                </tr>
                            </table>
                        @else
                            <span class="row-person">{{ $nombreAsignado($parteLectura, 'estudiante', 'principal') }}</span>
                        @endif
                    </td>
                </tr>
            @endif
        </table>

        <div class="section-bar bar-maestros">SEAMOS MEJORES MAESTROS</div>

        <table class="program-table">
            @if($hayContenidoMaestros)
                @foreach($partesMaestros as $parte)
                    @php
                        $principalEstudiante = $nombreAsignado($parte, 'estudiante', 'principal');
                        $principalAyudante = $nombreAsignado($parte, 'ayudante', 'principal');
                        $auxiliarEstudiante = $nombreAsignado($parte, 'estudiante', 'auxiliar');
                        $auxiliarAyudante = $nombreAsignado($parte, 'ayudante', 'auxiliar');

                        $principal = trim($principalEstudiante . ($principalEstudiante && $principalAyudante ? ' / ' : '') . $principalAyudante);
                        $auxiliar = trim($auxiliarEstudiante . ($auxiliarEstudiante && $auxiliarAyudante ? ' / ' : '') . $auxiliarAyudante);

                        $tieneFila = trim(($parte->titulo ?? '') . $principal . $auxiliar) !== '';
                    @endphp

                    @continue(!$tieneFila)

                    <tr>
                        <td class="time" width="10%" style="{{ $timeStyle }}">{{ $hora($parte) }}</td>
                        <td class="topic" width="30%" style="{{ $topicStyle }}">
                            <strong>{{ $parte->numero }}.</strong>
                            {{ $parte->titulo }}
                            <span class="duration">{{ $duracion($parte) }}</span>
                        </td>
                        <td class="assign-cell" width="60%" style="{{ $assignStyle }}">
                            <div class="assign-title">
                                <span class="row-label">Est./Ayud.:</span>
                            </div>

                            @if($auxiliar)
                                <table class="rooms-table">
                                    <tr class="rooms-head">
                                        <td>{{ $nombreSalaAuxiliar }}</td>
                                        <td>Sala principal</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $auxiliar }}</td>
                                        <td>{{ $principal }}</td>
                                    </tr>
                                </table>
                            @else
                                <span class="row-person">{{ $principal }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="time" width="10%" style="{{ $timeStyle }}"></td>
                    <td class="topic empty-line" colspan="2" width="90%" style="{{ $restStyle }}">Sin partes cargadas.</td>
                </tr>
            @endif
        </table>

        <div class="section-bar bar-vida">NUESTRA VIDA CRISTIANA</div>

        <table class="program-table">
            @if($programa->cancion_medio)
                <tr>
                    <td class="time" width="10%" style="{{ $timeStyle }}">{{ $horaCancionMedia }}</td>
                    <td class="topic" colspan="2" width="90%" style="{{ $restStyle }}">
                        <span class="dot">•</span>
                        Canción {{ $programa->cancion_medio }}
                    </td>
                </tr>
            @endif

            @if($hayContenidoVida)
                @foreach($partesVida as $parte)
                    @php
                        $disertante = $nombreAsignado($parte, 'disertante');
                        $tieneFila = trim(($parte->titulo ?? '') . $disertante) !== '';
                    @endphp

                    @continue(!$tieneFila)

                    <tr>
                        <td class="time" width="10%" style="{{ $timeStyle }}">{{ $hora($parte) }}</td>
                        <td class="topic" width="30%" style="{{ $topicStyle }}">
                            <strong>{{ $parte->numero }}.</strong>
                            {{ $parte->titulo }}
                            <span class="duration">{{ $duracion($parte) }}</span>
                        </td>
                        <td class="assign-cell" width="60%" style="{{ $assignStyle }}">
                            <span class="row-label">Disertante:</span>
                            <span class="row-person">{{ $disertante }}</span>
                        </td>
                    </tr>
                @endforeach
            @endif

            @if($parteEstudio)
                @php
                    $conductor = $nombreAsignado($parteEstudio, 'conductor');
                    $lector = $nombreAsignado($parteEstudio, 'lector');
                    $conductorLector = trim($conductor . ($conductor && $lector ? ' / ' : '') . $lector);
                @endphp

                <tr>
                    <td class="time" width="10%" style="{{ $timeStyle }}">{{ $hora($parteEstudio) }}</td>
                    <td class="topic" width="30%" style="{{ $topicStyle }}">
                        <strong>{{ $parteEstudio->numero }}.</strong>
                        {{ $tituloSeguro($parteEstudio, 'Estudio bíblico de la congregación') }}
                        <span class="duration">{{ $duracion($parteEstudio) }}</span>
                    </td>
                    <td class="assign-cell" width="60%" style="{{ $assignStyle }}">
                        <span class="row-label">Cond./Lector:</span>
                        <span class="row-person">{{ $conductorLector }}</span>
                    </td>
                </tr>
            @endif

            @if($parteOracionFinal)
                <tr>
                    <td class="time" width="10%" style="{{ $timeStyle }}">{{ $hora($parteOracionFinal) }}</td>
                    <td class="topic" colspan="2" width="90%" style="{{ $restStyle }}">
                        <span class="dot">•</span>
                        Palabras de conclusión
                        <span class="duration">{{ $duracion($parteOracionFinal) }}</span>
                    </td>
                </tr>

                <tr>
                    <td class="time" width="10%" style="{{ $timeStyle }}">{{ $horaFin($parteOracionFinal) }}</td>
                    <td class="topic" width="30%" style="{{ $topicStyle }}">
                        <span class="dot">•</span>
                        Canción {{ $programa->cancion_final }}
                    </td>
                    <td class="assign-cell" width="60%" style="{{ $assignStyle }}">
                        <span class="row-label">Oración:</span>
                        <span class="row-person">{{ $nombreAsignado($parteOracionFinal, 'principal') }}</span>
                    </td>
                </tr>
            @endif
        </table>

    @endif
</div>