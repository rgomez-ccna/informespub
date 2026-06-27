@extends('layouts.app')

@section('content')
@php
    $labelsTipo = [
        'presidente' => 'Presidente',
        'ayudante_auditorio' => 'Ayudante auditorio',
        'consejero_auxiliar' => 'Consejero auxiliar',
        'ayudante_auxiliar' => 'Ayudante auxiliar',
        'oracion' => 'Oración',
        'tesoro' => 'Tesoros',
        'perlas' => 'Perlas',
        'lectura_biblia' => 'Lectura Biblia',
        'maestro_estudiante' => 'Estudiante',
        'maestro_ayudante' => 'Ayudante',
        'vida_cristiana' => 'Vida cristiana',
        'estudio_conductor' => 'Conductor estudio',
        'estudio_lector' => 'Lector estudio',
    ];

    $partes = $programa->partes->sortBy('orden')->values();

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

    $opcionesPorTipo = function ($tipo) use ($publicadoresPorTipo) {
        return $publicadoresPorTipo->get($tipo, collect());
    };

    $asignacionActual = function ($parteId, $rol, $sala) use ($asignacionesActuales) {
        $items = $asignacionesActuales->get($parteId, collect());

        return $items->first(function ($asignacion) use ($rol, $sala) {
            return $asignacion->rol === $rol && $asignacion->sala === $sala;
        });
    };

    $ultimaLabel = function ($publicador, $tipo) use ($historial, $programa) {
        $item = $historial->get($publicador->id . '|' . $tipo);

        if (!$item) {
            return 'Nunca';
        }

        $fechaUltima = \Carbon\Carbon::parse($item->fecha);
        $semanas = $fechaUltima->diffInWeeks($programa->fecha);

        return 'Últ. ' . $fechaUltima->format('d/m/Y') . ' · hace ' . $semanas . ' sem.';
    };

    $selectAsignacion = function ($parte, $slot, $tipo, $rol, $sala, $label) use (
        $opcionesPorTipo,
        $asignacionActual,
        $ultimaLabel
    ) {
        if (!$parte) {
            return '';
        }

        $actual = $asignacionActual($parte->id, $rol, $sala);
        $actualId = $actual?->publicador_id;
        $opciones = $opcionesPorTipo($tipo);
        $existeActual = $actualId && $opciones->contains('id', $actualId);

        $base = 'asignaciones[' . $parte->id . '][' . $slot . ']';

        $html = '<div class="col-md-6 col-lg-4 campo-asignacion">';
        $html .= '<label class="form-label small mb-1 fw-semibold">' . e($label) . '</label>';

        $html .= '<input class="slot-field" type="hidden" name="' . e($base) . '[rol]" value="' . e($rol) . '">';
        $html .= '<input class="slot-field" type="hidden" name="' . e($base) . '[sala]" value="' . e($sala) . '">';
        $html .= '<input class="slot-field" type="hidden" name="' . e($base) . '[tipo_asignacion]" value="' . e($tipo) . '">';

        $html .= '<select name="' . e($base) . '[publicador_id]" class="form-select form-select-sm slot-field">';
        $html .= '<option value="">Sin asignar</option>';

        if ($actual && !$existeActual && $actual->publicador) {
            $html .= '<option value="' . e($actual->publicador_id) . '" selected>';
            $html .= e($actual->publicador->nombre . ' — actual, revisar calificación');
            $html .= '</option>';
        }

        foreach ($opciones as $publicador) {
            $selected = (int) $actualId === (int) $publicador->id ? 'selected' : '';
            $html .= '<option value="' . e($publicador->id) . '" ' . $selected . '>';
            $html .= e($publicador->nombre . ' — ' . $ultimaLabel($publicador, $tipo));
            $html .= '</option>';
        }

        if ($opciones->isEmpty()) {
            $html .= '<option disabled>No hay publicadores calificados</option>';
        }

        $html .= '</select>';
        $html .= '</div>';

        return $html;
    };

    $duracionParte = function ($parte, $default = null) {
        if (!$parte) {
            return $default;
        }

        return old('partes.' . $parte->id . '.duracion_minutos', $parte->duracion_minutos ?? $default);
    };

    $tituloParte = function ($parte, $default = null) {
        if (!$parte) {
            return $default;
        }

        return old('partes.' . $parte->id . '.titulo', $parte->titulo ?? $default);
    };

    $tieneAuxiliarLectura = $parteLectura
        && $asignacionActual($parteLectura->id, 'estudiante', 'auxiliar');

    $nombreSalaAuxiliar = $programa->nombre_sala_auxiliar ?: 'Sala auxiliar';
@endphp

<div class="container py-4" style="max-width: 1180px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Editar Vida y Ministerio</h3>
            <p class="text-muted mb-0">
                {{ $programa->fecha->format('d/m/Y') }} · {{ $programa->lectura_semanal ?: 'Sin lectura cargada' }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('vida-ministerio.calificaciones.index') }}" class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-list-check"></i> Calificaciones
            </a>

            <a href="{{ route('vida-ministerio.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2">
            Revisá los campos marcados.
        </div>
    @endif

    <form action="{{ route('vida-ministerio.update', $programa) }}" method="POST" id="formVidaMinisterio">
        @csrf
        @method('PUT')

        {{-- DATOS GENERALES --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white">
                <strong>1. Datos generales</strong>
            </div>

            <div class="card-body">

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date"
                               name="fecha"
                               class="form-control @error('fecha') is-invalid @enderror"
                               value="{{ old('fecha', $programa->fecha->format('Y-m-d')) }}"
                               required>

                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Hora de inicio</label>
                        <input type="time"
                               name="hora_inicio"
                               id="hora_inicio"
                               class="form-control @error('hora_inicio') is-invalid @enderror"
                               value="{{ old('hora_inicio', $programa->hora_inicio ? substr($programa->hora_inicio, 0, 5) : '') }}">

                        @error('hora_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tipo de semana</label>
                        <select name="estado" id="tipoSemana" class="form-select">
                            <option value="normal" {{ old('estado', $programa->estado ?? 'normal') !== 'aviso' ? 'selected' : '' }}>
                                Reunión normal
                            </option>
                            <option value="aviso" {{ old('estado', $programa->estado) === 'aviso' ? 'selected' : '' }}>
                                Aviso / no hay reunión
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Nombre sala auxiliar</label>
                        <input type="text"
                               name="nombre_sala_auxiliar"
                               class="form-control"
                               value="{{ old('nombre_sala_auxiliar', $programa->nombre_sala_auxiliar) }}"
                               placeholder="Ej: Sala Auxiliar — RHOMANES">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lectura semanal</label>
                    <input type="text"
                           name="lectura_semanal"
                           class="form-control"
                           value="{{ old('lectura_semanal', $programa->lectura_semanal) }}"
                           placeholder="Ej: Jeremías 32, 33">
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Canción inicial</label>
                        <input type="text"
                               name="cancion_inicio"
                               class="form-control"
                               value="{{ old('cancion_inicio', $programa->cancion_inicio) }}"
                               placeholder="Ej: 1">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Canción intermedia</label>
                        <input type="text"
                               name="cancion_medio"
                               class="form-control"
                               value="{{ old('cancion_medio', $programa->cancion_medio) }}"
                               placeholder="Ej: 128">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Canción final</label>
                        <input type="text"
                               name="cancion_final"
                               class="form-control"
                               value="{{ old('cancion_final', $programa->cancion_final) }}"
                               placeholder="Ej: 143">
                    </div>
                </div>
            </div>
        </div>

        {{-- ENCARGADOS --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <span class="section-icon" style="background:#6b7280;">
                    <i class="fa-solid fa-user-tie"></i>
                </span>
                <strong>2. Encargados de la reunión</strong>
            </div>

            <div class="card-body">
                <div class="row g-2">
                    @if($partePresidente)
                        <input type="hidden" name="partes[{{ $partePresidente->id }}][seccion]" value="encabezado">
                        <input type="hidden" name="partes[{{ $partePresidente->id }}][tipo_asignacion]" value="presidente">
                        <input type="hidden" name="partes[{{ $partePresidente->id }}][titulo]" value="Presidente">
                        {!! $selectAsignacion($partePresidente, 'presidente', 'presidente', 'principal', 'general', 'Presidente') !!}
                    @endif

                    @if($parteAyudanteAuditorio)
                        <input type="hidden" name="partes[{{ $parteAyudanteAuditorio->id }}][seccion]" value="encabezado">
                        <input type="hidden" name="partes[{{ $parteAyudanteAuditorio->id }}][tipo_asignacion]" value="ayudante_auditorio">
                        <input type="hidden" name="partes[{{ $parteAyudanteAuditorio->id }}][titulo]" value="Ayudante auditorio principal">
                        {!! $selectAsignacion($parteAyudanteAuditorio, 'ayudante_auditorio', 'ayudante_auditorio', 'principal', 'general', 'Ayudante sala principal') !!}
                    @endif

                    @if($parteConsejeroAuxiliar)
                        <input type="hidden" name="partes[{{ $parteConsejeroAuxiliar->id }}][seccion]" value="encabezado">
                        <input type="hidden" name="partes[{{ $parteConsejeroAuxiliar->id }}][tipo_asignacion]" value="consejero_auxiliar">
                        <input type="hidden" name="partes[{{ $parteConsejeroAuxiliar->id }}][titulo]" value="Consejero sala auxiliar">
                        {!! $selectAsignacion($parteConsejeroAuxiliar, 'consejero_auxiliar', 'consejero_auxiliar', 'principal', 'general', 'Consejero sala auxiliar') !!}
                    @endif

                    @if($parteAyudanteAuxiliar)
                        <input type="hidden" name="partes[{{ $parteAyudanteAuxiliar->id }}][seccion]" value="encabezado">
                        <input type="hidden" name="partes[{{ $parteAyudanteAuxiliar->id }}][tipo_asignacion]" value="ayudante_auxiliar">
                        <input type="hidden" name="partes[{{ $parteAyudanteAuxiliar->id }}][titulo]" value="Ayudante sala auxiliar">
                        {!! $selectAsignacion($parteAyudanteAuxiliar, 'ayudante_auxiliar', 'ayudante_auxiliar', 'principal', 'general', 'Ayudante sala auxiliar') !!}
                    @endif

                    @if($parteOracionInicio)
                        <input type="hidden" name="partes[{{ $parteOracionInicio->id }}][seccion]" value="encabezado">
                        <input type="hidden" name="partes[{{ $parteOracionInicio->id }}][tipo_asignacion]" value="oracion">
                        <input type="hidden" name="partes[{{ $parteOracionInicio->id }}][titulo]" value="Canción, oración y palabras de introducción">

                        <div class="col-md-6 col-lg-4 bloque-horario">
                            <label class="form-label small mb-1 fw-semibold">Bloque inicial</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Duración</span>
                                <input type="number"
                                       min="0"
                                       max="30"
                                       name="partes[{{ $parteOracionInicio->id }}][duracion_minutos]"
                                       class="form-control duracion-input"
                                       value="{{ $duracionParte($parteOracionInicio, 6) }}">
                                <span class="input-group-text">min</span>
                            </div>
                            <div class="small text-muted mt-1">
                                Canción, oración y palabras de introducción.
                            </div>
                            <div class="small fw-semibold mt-1">
                                Horario: <span class="hora-preview">-</span>
                            </div>
                        </div>

                        {!! $selectAsignacion($parteOracionInicio, 'oracion', 'oracion', 'principal', 'general', 'Oración inicial') !!}
                    @endif

                    @if($parteOracionFinal)
                        <input type="hidden" name="partes[{{ $parteOracionFinal->id }}][seccion]" value="final">
                        <input type="hidden" name="partes[{{ $parteOracionFinal->id }}][tipo_asignacion]" value="oracion">
                        <input type="hidden" name="partes[{{ $parteOracionFinal->id }}][titulo]" value="Palabras de conclusión, canción final y oración">

                        <div class="col-md-6 col-lg-4 bloque-horario">
                            <label class="form-label small mb-1 fw-semibold">Conclusión</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Duración</span>
                                <input type="number"
                                       min="0"
                                       max="30"
                                       name="partes[{{ $parteOracionFinal->id }}][duracion_minutos]"
                                       class="form-control duracion-input"
                                       value="{{ $duracionParte($parteOracionFinal, 3) }}">
                                <span class="input-group-text">min</span>
                            </div>
                            <div class="small text-muted mt-1">
                                Palabras de conclusión. La canción final se toma del dato general.
                            </div>
                            <div class="small fw-semibold mt-1">
                                Horario: <span class="hora-preview">-</span>
                            </div>
                        </div>

                        {!! $selectAsignacion($parteOracionFinal, 'oracion', 'oracion', 'principal', 'general', 'Oración final') !!}
                    @endif
                </div>
            </div>
        </div>

        {{-- TESOROS --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <span class="section-icon" style="background:#2a6f74;">
                    <i class="fa-solid fa-gem"></i>
                </span>
                <strong style="color:#2a6f74;">3. TESOROS DE LA BIBLIA</strong>
            </div>

            <div class="card-body">

                @if($parteTesoro)
                    <div class="program-row bloque-horario">
                        <input type="hidden" name="partes[{{ $parteTesoro->id }}][seccion]" value="tesoros">
                        <input type="hidden" name="partes[{{ $parteTesoro->id }}][tipo_asignacion]" value="tesoro">

                        <div class="row g-2 align-items-end">
                            <div class="col-md-1">
                                <label class="form-label small mb-1">Nro.</label>
                                <div class="form-control form-control-sm bg-light text-center">
                                    {{ $parteTesoro->numero ?: 1 }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small mb-1">Tema</label>
                                <input type="text"
                                       name="partes[{{ $parteTesoro->id }}][titulo]"
                                       class="form-control form-control-sm"
                                       value="{{ $tituloParte($parteTesoro) }}"
                                       placeholder="Ej: Meditar en las cualidades de Jehová fortalece nuestra fe">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small mb-1">Duración</label>
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                           name="partes[{{ $parteTesoro->id }}][duracion_minutos]"
                                           class="form-control duracion-input"
                                           value="{{ $duracionParte($parteTesoro, 10) }}">
                                    <span class="input-group-text">min</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Horario</label>
                                <div class="form-control form-control-sm bg-light hora-preview">-</div>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            {!! $selectAsignacion($parteTesoro, 'disertante', 'tesoro', 'disertante', 'general', 'Disertante') !!}
                        </div>
                    </div>
                @endif

                @if($partePerlas)
                    <div class="program-row bloque-horario">
                        <input type="hidden" name="partes[{{ $partePerlas->id }}][seccion]" value="tesoros">
                        <input type="hidden" name="partes[{{ $partePerlas->id }}][tipo_asignacion]" value="perlas">
                        <input type="hidden" name="partes[{{ $partePerlas->id }}][titulo]" value="Busquemos perlas escondidas">

                        <div class="row g-2 align-items-end">
                            <div class="col-md-1">
                                <label class="form-label small mb-1">Nro.</label>
                                <div class="form-control form-control-sm bg-light text-center">
                                    {{ $partePerlas->numero ?: 2 }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small mb-1">Parte</label>
                                <div class="form-control form-control-sm bg-light">
                                    Busquemos perlas escondidas
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small mb-1">Duración</label>
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                           name="partes[{{ $partePerlas->id }}][duracion_minutos]"
                                           class="form-control duracion-input"
                                           value="{{ $duracionParte($partePerlas, 10) }}">
                                    <span class="input-group-text">min</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Horario</label>
                                <div class="form-control form-control-sm bg-light hora-preview">-</div>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            {!! $selectAsignacion($partePerlas, 'disertante', 'perlas', 'disertante', 'general', 'Disertante') !!}
                        </div>
                    </div>
                @endif

                @if($parteLectura)
                    <div class="program-row bloque-horario">
                        <input type="hidden" name="partes[{{ $parteLectura->id }}][seccion]" value="tesoros">
                        <input type="hidden" name="partes[{{ $parteLectura->id }}][tipo_asignacion]" value="lectura_biblia">
                        <input type="hidden" name="partes[{{ $parteLectura->id }}][titulo]" value="Lectura de la Biblia">

                        <div class="row g-2 align-items-end">
                            <div class="col-md-1">
                                <label class="form-label small mb-1">Nro.</label>
                                <div class="form-control form-control-sm bg-light text-center">
                                    {{ $parteLectura->numero ?: 3 }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small mb-1">Parte</label>
                                <div class="form-control form-control-sm bg-light">
                                    Lectura de la Biblia
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small mb-1">Duración</label>
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                           name="partes[{{ $parteLectura->id }}][duracion_minutos]"
                                           class="form-control duracion-input"
                                           value="{{ $duracionParte($parteLectura, 4) }}">
                                    <span class="input-group-text">min</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Horario</label>
                                <div class="form-control form-control-sm bg-light hora-preview">-</div>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            {!! $selectAsignacion($parteLectura, 'principal', 'lectura_biblia', 'estudiante', 'principal', 'Estudiante · Sala principal') !!}
                        </div>

                        <div class="form-check mt-2">
                            <input type="checkbox"
                                   class="form-check-input toggle-auxiliar"
                                   id="aux_lectura_{{ $parteLectura->id }}"
                                   data-target="#bloque_aux_lectura_{{ $parteLectura->id }}"
                                   {{ $tieneAuxiliarLectura ? 'checked' : '' }}>
                            <label class="form-check-label small" for="aux_lectura_{{ $parteLectura->id }}">
                                También se hace en {{ $nombreSalaAuxiliar }}
                            </label>
                        </div>

                        <div id="bloque_aux_lectura_{{ $parteLectura->id }}"
                             class="row g-2 mt-2 auxiliar-bloque {{ $tieneAuxiliarLectura ? '' : 'd-none' }}">
                            {!! $selectAsignacion($parteLectura, 'auxiliar', 'lectura_biblia', 'estudiante', 'auxiliar', 'Estudiante · ' . $nombreSalaAuxiliar) !!}
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- MAESTROS --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="section-icon" style="background:#c69214;">
                        <i class="fa-solid fa-wheat-awn"></i>
                    </span>
                    <strong style="color:#c69214;">4. SEAMOS MEJORES MAESTROS</strong>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm" onclick="agregarParteMaestros()">
                    <i class="fa-solid fa-plus"></i> Agregar parte
                </button>
            </div>

            <div class="card-body">
                <div class="alert alert-light border small py-2">
                    Cada parte se carga una vez. Si también se hace en sala auxiliar, activá “También se hace en sala auxiliar”.
                </div>

                <div id="contenedor_maestros">
                    @forelse($partesMaestros as $parte)
                        @php
                            $tieneAuxiliarMaestros = $asignacionActual($parte->id, 'estudiante', 'auxiliar')
                                || $asignacionActual($parte->id, 'ayudante', 'auxiliar');
                        @endphp

                        <div class="program-row bloque-horario">
                            <input type="hidden" name="partes[{{ $parte->id }}][seccion]" value="maestros">
                            <input type="hidden" name="partes[{{ $parte->id }}][tipo_asignacion]" value="maestro_estudiante">

                            <div class="row g-2 align-items-end">
                                <div class="col-md-1">
                                    <label class="form-label small mb-1">Nro.</label>
                                    <div class="form-control form-control-sm bg-light text-center">
                                        {{ $parte->numero ?: '-' }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small mb-1">Tema</label>
                                    <input type="text"
                                           name="partes[{{ $parte->id }}][titulo]"
                                           class="form-control form-control-sm"
                                           value="{{ $tituloParte($parte) }}"
                                           placeholder="Ej: Empiece conversaciones">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label small mb-1">Duración</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                               name="partes[{{ $parte->id }}][duracion_minutos]"
                                               class="form-control duracion-input"
                                               value="{{ $duracionParte($parte) }}"
                                               placeholder="Min">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small mb-1">Horario</label>
                                    <div class="form-control form-control-sm bg-light hora-preview">-</div>
                                </div>
                            </div>

                            <div class="row g-2 mt-2">
                                {!! $selectAsignacion($parte, 'principal_estudiante', 'maestro_estudiante', 'estudiante', 'principal', 'Principal · Estudiante') !!}
                                {!! $selectAsignacion($parte, 'principal_ayudante', 'maestro_ayudante', 'ayudante', 'principal', 'Principal · Ayudante') !!}
                            </div>

                            <div class="form-check mt-2">
                                <input type="checkbox"
                                       class="form-check-input toggle-auxiliar"
                                       id="aux_maestros_{{ $parte->id }}"
                                       data-target="#bloque_aux_maestros_{{ $parte->id }}"
                                       {{ $tieneAuxiliarMaestros ? 'checked' : '' }}>
                                <label class="form-check-label small" for="aux_maestros_{{ $parte->id }}">
                                    También se hace en {{ $nombreSalaAuxiliar }}
                                </label>
                            </div>

                            <div id="bloque_aux_maestros_{{ $parte->id }}"
                                 class="row g-2 mt-2 auxiliar-bloque {{ $tieneAuxiliarMaestros ? '' : 'd-none' }}">
                                {!! $selectAsignacion($parte, 'auxiliar_estudiante', 'maestro_estudiante', 'estudiante', 'auxiliar', 'Auxiliar · Estudiante') !!}
                                {!! $selectAsignacion($parte, 'auxiliar_ayudante', 'maestro_ayudante', 'ayudante', 'auxiliar', 'Auxiliar · Ayudante') !!}
                            </div>

                            <div class="mt-2">
                                <label class="form-check small text-danger">
                                    <input type="checkbox"
                                           name="partes[{{ $parte->id }}][eliminar]"
                                           value="1"
                                           class="form-check-input">
                                    Eliminar esta parte al guardar
                                </label>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small mb-3">
                            No hay partes de maestros. Agregá una con el botón.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- VIDA CRISTIANA --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="section-icon" style="background:#a73229;">
                        <i class="fa-solid fa-book"></i>
                    </span>
                    <strong style="color:#a73229;">5. NUESTRA VIDA CRISTIANA</strong>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm" onclick="agregarTemaVida()">
                    <i class="fa-solid fa-plus"></i> Agregar tema
                </button>
            </div>

            <div class="card-body">
                <div class="alert alert-light border small py-2">
                    En esta sección no se usa sala auxiliar. Solo tema, duración y disertante.
                </div>

                <div id="contenedor_vida">
                    @forelse($partesVida as $parte)
                        <div class="program-row bloque-horario">
                            <input type="hidden" name="partes[{{ $parte->id }}][seccion]" value="vida">
                            <input type="hidden" name="partes[{{ $parte->id }}][tipo_asignacion]" value="vida_cristiana">

                            <div class="row g-2 align-items-end">
                                <div class="col-md-1">
                                    <label class="form-label small mb-1">Nro.</label>
                                    <div class="form-control form-control-sm bg-light text-center">
                                        {{ $parte->numero ?: '-' }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small mb-1">Tema</label>
                                    <input type="text"
                                           name="partes[{{ $parte->id }}][titulo]"
                                           class="form-control form-control-sm"
                                           value="{{ $tituloParte($parte) }}"
                                           placeholder="Ej: En esta campaña, ni un golpe al aire">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label small mb-1">Duración</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                               name="partes[{{ $parte->id }}][duracion_minutos]"
                                               class="form-control duracion-input"
                                               value="{{ $duracionParte($parte) }}"
                                               placeholder="Min">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small mb-1">Horario</label>
                                    <div class="form-control form-control-sm bg-light hora-preview">-</div>
                                </div>
                            </div>

                            <div class="row g-2 mt-2">
                                {!! $selectAsignacion($parte, 'disertante', 'vida_cristiana', 'disertante', 'general', 'Disertante') !!}
                            </div>

                            <div class="mt-2">
                                <label class="form-check small text-danger">
                                    <input type="checkbox"
                                           name="partes[{{ $parte->id }}][eliminar]"
                                           value="1"
                                           class="form-check-input">
                                    Eliminar este tema al guardar
                                </label>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small mb-3">
                            No hay temas de Vida Cristiana. Agregá uno con el botón.
                        </div>
                    @endforelse
                </div>

                @if($parteEstudio)
                    <div class="program-row bloque-horario">
                        <input type="hidden" name="partes[{{ $parteEstudio->id }}][seccion]" value="vida">
                        <input type="hidden" name="partes[{{ $parteEstudio->id }}][tipo_asignacion]" value="estudio_conductor">

                        <div class="row g-2 align-items-end">
                            <div class="col-md-1">
                                <label class="form-label small mb-1">Nro.</label>
                                <div class="form-control form-control-sm bg-light text-center">
                                    {{ $parteEstudio->numero ?: '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small mb-1">Estudio bíblico de la congregación</label>
                                <input type="text"
                                       name="partes[{{ $parteEstudio->id }}][titulo]"
                                       class="form-control form-control-sm"
                                       value="{{ $tituloParte($parteEstudio, 'Estudio bíblico de la congregación') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small mb-1">Duración</label>
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                           name="partes[{{ $parteEstudio->id }}][duracion_minutos]"
                                           class="form-control duracion-input"
                                           value="{{ $duracionParte($parteEstudio, 30) }}">
                                    <span class="input-group-text">min</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Horario</label>
                                <div class="form-control form-control-sm bg-light hora-preview">-</div>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            {!! $selectAsignacion($parteEstudio, 'conductor', 'estudio_conductor', 'conductor', 'general', 'Conductor') !!}
                            {!! $selectAsignacion($parteEstudio, 'lector', 'estudio_lector', 'lector', 'general', 'Lector') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- OBSERVACIONES --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <label class="form-label">Aviso u observaciones</label>
                    <textarea name="observaciones"
                            class="form-control"
                            rows="3"
                            placeholder="Ej: ASAMBLEA DE CIRCUITO 2026">{{ old('observaciones', $programa->observaciones) }}</textarea>
                    <div class="form-text">
                        Si la semana es aviso, este texto aparecerá centrado en el PDF.
                    </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('vida-ministerio.index') }}" class="btn btn-light border">
                Cancelar
            </a>

            <button class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Guardar programa
            </button>
        </div>
    </form>
</div>

{{-- TEMPLATE NUEVA PARTE MAESTROS --}}
<template id="template_maestros">
    <div class="program-row bloque-horario nueva-parte bg-light">
        <input type="hidden" data-name="seccion" value="maestros">
        <input type="hidden" data-name="tipo_asignacion" value="maestro_estudiante">

        <div class="row g-2 align-items-end">
            <div class="col-md-7">
                <label class="form-label small mb-1">Tema</label>
                <input type="text"
                       data-name="titulo"
                       class="form-control form-control-sm"
                       placeholder="Ej: Empiece conversaciones">
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">Duración</label>
                <div class="input-group input-group-sm">
                    <input type="number"
                           data-name="duracion_minutos"
                           class="form-control duracion-input"
                           min="0"
                           max="300"
                           placeholder="Min">
                    <span class="input-group-text">min</span>
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">Horario</label>
                <div class="form-control form-control-sm bg-white hora-preview">-</div>
            </div>

            <div class="col-md-1">
                <button type="button"
                        class="btn btn-outline-danger btn-sm w-100"
                        onclick="this.closest('.nueva-parte').remove(); recalcularVistaHorarios();">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>

        <div class="small text-muted mt-2">
            Guardá para crear la parte. Luego vas a poder asignar estudiante, ayudante y sala auxiliar.
        </div>
    </div>
</template>

{{-- TEMPLATE NUEVO TEMA VIDA --}}
<template id="template_vida">
    <div class="program-row bloque-horario nueva-parte bg-light">
        <input type="hidden" data-name="seccion" value="vida">
        <input type="hidden" data-name="tipo_asignacion" value="vida_cristiana">

        <div class="row g-2 align-items-end">
            <div class="col-md-7">
                <label class="form-label small mb-1">Tema</label>
                <input type="text"
                       data-name="titulo"
                       class="form-control form-control-sm"
                       placeholder="Ej: Necesidades de la congregación">
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">Duración</label>
                <div class="input-group input-group-sm">
                    <input type="number"
                           data-name="duracion_minutos"
                           class="form-control duracion-input"
                           min="0"
                           max="300"
                           placeholder="Min">
                    <span class="input-group-text">min</span>
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">Horario</label>
                <div class="form-control form-control-sm bg-white hora-preview">-</div>
            </div>

            <div class="col-md-1">
                <button type="button"
                        class="btn btn-outline-danger btn-sm w-100"
                        onclick="this.closest('.nueva-parte').remove(); recalcularVistaHorarios();">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>

        <div class="small text-muted mt-2">
            Guardá para crear el tema. Luego vas a poder asignar el disertante.
        </div>
    </div>
</template>

<style>
.section-icon {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}

.program-row {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 12px;
    background: #fff;
}

.program-row:hover {
    background: #fcfcfc;
}

.form-label {
    font-weight: 600;
}

select.form-select-sm {
    font-size: 13px;
}

.hora-preview {
    font-size: 13px;
}

.campo-asignacion select option {
    font-size: 13px;
}
</style>

<script>
let nuevaParteIndex = 0;

function agregarParteMaestros() {
    agregarNuevaParte('maestros', 'template_maestros', 'contenedor_maestros');
}

function agregarTemaVida() {
    agregarNuevaParte('vida', 'template_vida', 'contenedor_vida');
}

function agregarNuevaParte(seccion, templateId, contenedorId) {
    const template = document.getElementById(templateId);
    const clone = template.content.cloneNode(true);
    const wrapper = clone.querySelector('.nueva-parte');

    const index = Date.now() + '_' + nuevaParteIndex++;

    wrapper.querySelectorAll('[data-name]').forEach(input => {
        input.name = `nuevas_partes[${index}][${input.dataset.name}]`;
    });

    document.getElementById(contenedorId).appendChild(wrapper);

    wrapper.querySelectorAll('.duracion-input').forEach(input => {
        input.addEventListener('input', recalcularVistaHorarios);
    });

    recalcularVistaHorarios();
}

function sumarMinutos(hora, minutos) {
    const [h, m] = hora.split(':').map(Number);
    const fecha = new Date();

    fecha.setHours(h, m, 0, 0);
    fecha.setMinutes(fecha.getMinutes() + minutos);

    return String(fecha.getHours()).padStart(2, '0') + ':' +
           String(fecha.getMinutes()).padStart(2, '0');
}

function recalcularVistaHorarios() {
    const horaInicio = document.getElementById('hora_inicio')?.value;

    if (!horaInicio) {
        document.querySelectorAll('.hora-preview').forEach(el => el.textContent = '-');
        return;
    }

    let cursor = horaInicio;

    document.querySelectorAll('.bloque-horario').forEach(card => {
        const duracionInput = card.querySelector('.duracion-input');
        const preview = card.querySelector('.hora-preview');

        if (!duracionInput || !preview) {
            return;
        }

        const duracion = parseInt(duracionInput.value || '0', 10);

        if (!duracion || duracion <= 0) {
            preview.textContent = '-';
            return;
        }

        const inicio = cursor;
        const fin = sumarMinutos(cursor, duracion);

        preview.textContent = inicio + ' - ' + fin;
        cursor = fin;
    });
}

function aplicarEstadoAuxiliares() {
    document.querySelectorAll('.toggle-auxiliar').forEach(check => {
        const target = document.querySelector(check.dataset.target);

        if (!target) {
            return;
        }

        const activo = check.checked;

        target.classList.toggle('d-none', !activo);

        target.querySelectorAll('input, select, textarea').forEach(input => {
            input.disabled = !activo;
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('hora_inicio')?.addEventListener('input', recalcularVistaHorarios);

    document.querySelectorAll('.duracion-input').forEach(input => {
        input.addEventListener('input', recalcularVistaHorarios);
    });

    document.querySelectorAll('.toggle-auxiliar').forEach(check => {
        check.addEventListener('change', aplicarEstadoAuxiliares);
    });

    aplicarEstadoAuxiliares();
    recalcularVistaHorarios();
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoSemana = document.getElementById('tipoSemana');

    function actualizarVistaPorTipo() {
        const esAviso = tipoSemana?.value === 'aviso';

        document.querySelectorAll('.card').forEach(card => {
            const texto = card.innerText || '';

            if (
                texto.includes('TESOROS DE LA BIBLIA') ||
                texto.includes('SEAMOS MEJORES MAESTROS') ||
                texto.includes('NUESTRA VIDA CRISTIANA') ||
                texto.includes('Encargados de la reunión')
            ) {
                card.style.opacity = esAviso ? '0.35' : '1';
            }
        });
    }

    tipoSemana?.addEventListener('change', actualizarVistaPorTipo);
    actualizarVistaPorTipo();
});
</script>
@endsection