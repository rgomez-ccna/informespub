@php
    $esEdit = $modo === 'edit';

    $partes = $esEdit
        ? $programa->partes->sortBy('orden')->values()
        : collect();

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

    $partesMaestrosOriginales = $partes
        ->where('seccion', 'maestros')
        ->where('tipo_asignacion', 'maestro_estudiante')
        ->values();

    $partesVidaOriginales = $partes
        ->where('seccion', 'vida')
        ->where('tipo_asignacion', 'vida_cristiana')
        ->values();

    $parteEstudio = $partes->firstWhere('tipo_asignacion', 'estudio_conductor');

    $nombreSalaAuxiliar = old(
        'nombre_sala_auxiliar',
        $programa?->nombre_sala_auxiliar ?: 'Sala auxiliar'
    );

    $filtrarVariables = function ($items) {
        $visibles = collect();
        $vaciaYaMostrada = false;

        foreach ($items as $parte) {
            $tieneContenido = trim((string) $parte->titulo) !== ''
                || ((int) $parte->duracion_minutos > 0)
                || $parte->asignaciones->count() > 0;

            if ($tieneContenido) {
                $visibles->push($parte);
                continue;
            }

            if (!$vaciaYaMostrada) {
                $visibles->push($parte);
                $vaciaYaMostrada = true;
            }
        }

        return $visibles;
    };

    $partesMaestros = $filtrarVariables($partesMaestrosOriginales);
    $partesVida = $filtrarVariables($partesVidaOriginales);

    $opcionesPorTipo = function ($tipo) use ($publicadoresPorTipo) {
        return $publicadoresPorTipo->get($tipo, collect());
    };

    $ultimaLabel = function ($publicador, $tipo) use ($historial, $fechaReferencia) {
        $item = $historial->get($publicador->id . '|' . $tipo);

        if (!$item) {
            return 'Nunca';
        }

        $fechaUltima = \Carbon\Carbon::parse($item->fecha);
        $semanas = $fechaUltima->diffInWeeks(\Carbon\Carbon::parse($fechaReferencia));

        return 'Últ. ' . $fechaUltima->format('d/m/Y') . ' · hace ' . $semanas . ' sem.';
    };

    $asignacionActual = function ($parte, $rol, $sala) use ($asignacionesActuales) {
        if (!$parte) {
            return null;
        }

        $items = $asignacionesActuales->get($parte->id, collect());

        return $items->first(function ($asignacion) use ($rol, $sala) {
            return $asignacion->rol === $rol && $asignacion->sala === $sala;
        });
    };

    $parteName = function ($key, $parte, $campo) use ($esEdit) {
        if ($esEdit && $parte) {
            return 'partes[' . $parte->id . '][' . $campo . ']';
        }

        return 'partes_base[' . $key . '][' . $campo . ']';
    };

    $slotBase = function ($key, $parte, $slot) use ($esEdit) {
        if ($esEdit && $parte) {
            return 'asignaciones[' . $parte->id . '][' . $slot . ']';
        }

        return 'partes_base[' . $key . '][slots][' . $slot . ']';
    };

    $valorParte = function ($key, $parte, $campo, $default = null) use ($esEdit) {
        if ($esEdit && $parte) {
            return old('partes.' . $parte->id . '.' . $campo, $parte->{$campo} ?? $default);
        }

        return old('partes_base.' . $key . '.' . $campo, $default);
    };

    $hiddenParte = function ($key, $parte, $seccion, $tipoAsignacion, $titulo = null) use ($parteName) {
        $html = '';

        $html .= '<input type="hidden" name="' . e($parteName($key, $parte, 'seccion')) . '" value="' . e($seccion) . '">';
        $html .= '<input type="hidden" name="' . e($parteName($key, $parte, 'tipo_asignacion')) . '" value="' . e($tipoAsignacion) . '">';

        if ($titulo !== null) {
            $html .= '<input type="hidden" name="' . e($parteName($key, $parte, 'titulo')) . '" value="' . e($titulo) . '">';
        }

        return $html;
    };

    $selectPublicador = function ($name, $tipo, $selected = null, $actual = null) use ($opcionesPorTipo, $ultimaLabel) {
        $opciones = $opcionesPorTipo($tipo);
        $existeActual = $selected && $opciones->contains('id', $selected);

        $html = '<select name="' . e($name) . '" class="form-select form-select-sm vm-select">';
        $html .= '<option value="">Sin asignar</option>';

        if ($actual && !$existeActual && $actual->publicador) {
            $html .= '<option value="' . e($actual->publicador_id) . '" selected>';
            $html .= e($actual->publicador->nombre . ' — actual, revisar calificación');
            $html .= '</option>';
        }

        foreach ($opciones as $publicador) {
            $isSelected = (string) $selected === (string) $publicador->id ? 'selected' : '';

            $html .= '<option value="' . e($publicador->id) . '" ' . $isSelected . '>';
            $html .= e($publicador->nombre . ' — ' . $ultimaLabel($publicador, $tipo));
            $html .= '</option>';
        }

        if ($opciones->isEmpty()) {
            $html .= '<option disabled>No hay publicadores calificados</option>';
        }

        $html .= '</select>';

        return $html;
    };

    $selectAsignacion = function ($key, $parte, $slot, $tipo, $rol, $sala) use (
        $slotBase,
        $selectPublicador,
        $asignacionActual
    ) {
        $base = $slotBase($key, $parte, $slot);
        $actual = $asignacionActual($parte, $rol, $sala);
        $actualId = $actual?->publicador_id;

        $html = '';
        $html .= '<input type="hidden" name="' . e($base) . '[rol]" value="' . e($rol) . '">';
        $html .= '<input type="hidden" name="' . e($base) . '[sala]" value="' . e($sala) . '">';
        $html .= '<input type="hidden" name="' . e($base) . '[tipo_asignacion]" value="' . e($tipo) . '">';
        $html .= $selectPublicador($base . '[publicador_id]', $tipo, $actualId, $actual);

        return $html;
    };

    $selectNueva = function ($index, $slot, $tipo, $rol, $sala) use ($selectPublicador) {
        $base = 'nuevas_partes[' . $index . '][slots][' . $slot . ']';

        $html = '';
        $html .= '<input type="hidden" name="' . e($base) . '[rol]" value="' . e($rol) . '">';
        $html .= '<input type="hidden" name="' . e($base) . '[sala]" value="' . e($sala) . '">';
        $html .= '<input type="hidden" name="' . e($base) . '[tipo_asignacion]" value="' . e($tipo) . '">';
        $html .= $selectPublicador($base . '[publicador_id]', $tipo);

        return $html;
    };

    $maestrosRows = $esEdit && $partesMaestros->isNotEmpty()
        ? $partesMaestros->map(fn ($parte) => ['key' => 'parte_' . $parte->id, 'parte' => $parte])
        : collect([
            ['key' => 'maestro_1', 'parte' => null],
        ]);

    $vidaRows = $esEdit && $partesVida->isNotEmpty()
        ? $partesVida->map(fn ($parte) => ['key' => 'parte_' . $parte->id, 'parte' => $parte])
        : collect([
            ['key' => 'vida_1', 'parte' => null],
        ]);
@endphp

<div class="container py-4" style="max-width: 1380px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">
                {{ $esEdit ? 'Editar Vida y Ministerio' : 'Crear programa Vida y Ministerio' }}
            </h3>

            <p class="text-muted mb-0">
                Carga compacta en el mismo orden del PDF.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('vida-ministerio.calificaciones.index') }}" class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-list-check"></i> Calificaciones
            </a>

          <a href="{{ route('vida-ministerio.index', $filtros ?? request()->only(['desde', 'hasta'])) }}"
            class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
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

    <form action="{{ $esEdit ? route('vida-ministerio.update', $programa) : route('vida-ministerio.store') }}"
          method="POST"
          id="formVidaMinisterio">
        @csrf

        @if($esEdit)
            @method('PUT')
        @endif

        <input type="hidden" name="desde" value="{{ request('desde') }}">
        <input type="hidden" name="hasta" value="{{ request('hasta') }}">

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Fecha <span class="text-danger">*</span></label>
                        <input type="date"
                               name="fecha"
                               class="form-control form-control-sm @error('fecha') is-invalid @enderror"
                               value="{{ old('fecha', $programa?->fecha?->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small mb-1">Tipo</label>
                        <select name="estado" id="tipoSemana" class="form-select form-select-sm">
                            <option value="normal" {{ old('estado', $programa->estado ?? 'normal') !== 'aviso' ? 'selected' : '' }}>
                                Reunión normal
                            </option>
                            <option value="aviso" {{ old('estado', $programa->estado ?? '') === 'aviso' ? 'selected' : '' }}>
                                Aviso / no hay reunión
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 normal-block">
                        <label class="form-label small mb-1">Hora inicio</label>
                        <input type="time"
                               name="hora_inicio"
                               id="hora_inicio"
                               class="form-control form-control-sm"
                               value="{{ old('hora_inicio', $programa?->hora_inicio ? substr($programa->hora_inicio, 0, 5) : '') }}">
                    </div>

                    <div class="col-md-3 normal-block">
                        <label class="form-label small mb-1">Lectura semanal</label>
                        <input type="text"
                               name="lectura_semanal"
                               class="form-control form-control-sm"
                               value="{{ old('lectura_semanal', $programa?->lectura_semanal) }}"
                               placeholder="Ej: Jeremías 32, 33">
                    </div>

                    <div class="col-md-3 normal-block">
                        <label class="form-label small mb-1">Sala auxiliar</label>
                        <input type="text"
                               name="nombre_sala_auxiliar"
                               id="nombreSalaAuxiliar"
                               class="form-control form-control-sm"
                               value="{{ old('nombre_sala_auxiliar', $programa?->nombre_sala_auxiliar) }}"
                               placeholder="Sala auxiliar">
                    </div>
                </div>

                <div class="row g-2 mt-2 normal-block">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Canción inicial</label>
                        <input type="text"
                            name="cancion_inicio"
                            class="form-control form-control-sm"
                            value="{{ old('cancion_inicio', $programa?->cancion_inicio) }}">
                    </div>
                </div>

                <div class="row g-2 mt-2 aviso-block d-none">
                    <div class="col-md-12">
                        <label class="form-label small mb-1">Texto del aviso</label>
                        <input type="text"
                            name="observaciones"
                            class="form-control form-control-sm"
                            value="{{ old('observaciones', $programa?->observaciones) }}"
                            placeholder="Ej: ASAMBLEA DE CIRCUITO 2026 ... ">
                    </div>
                </div>
            </div>
        </div>

        <div class="normal-block">

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white fw-bold py-2">
                    Encargados e inicio
                </div>

                <div class="card-body py-3">
                    {!! $hiddenParte('presidente', $partePresidente, 'encabezado', 'presidente', 'Presidente') !!}
                    {!! $hiddenParte('ayudante_auditorio', $parteAyudanteAuditorio, 'encabezado', 'ayudante_auditorio', 'Ayudante auditorio principal') !!}
                    {!! $hiddenParte('consejero_auxiliar', $parteConsejeroAuxiliar, 'encabezado', 'consejero_auxiliar', 'Consejero sala auxiliar') !!}
                    {!! $hiddenParte('ayudante_auxiliar', $parteAyudanteAuxiliar, 'encabezado', 'ayudante_auxiliar', 'Ayudante sala auxiliar') !!}
                    {!! $hiddenParte('oracion_inicio', $parteOracionInicio, 'encabezado', 'oracion', 'Canción, oración y palabras de introducción') !!}

                    <div class="vm-grid-encargados">
                        <div>
                            <div class="vm-mini-label">Presidente</div>
                            {!! $selectAsignacion('presidente', $partePresidente, 'principal', 'presidente', 'principal', 'general') !!}
                        </div>

                        <div>
                            <div class="vm-mini-label">Ayudante sala principal</div>
                            {!! $selectAsignacion('ayudante_auditorio', $parteAyudanteAuditorio, 'principal', 'ayudante_auditorio', 'principal', 'general') !!}
                        </div>

                        <div>
                            <div class="vm-mini-label">Consejero sala auxiliar</div>
                            {!! $selectAsignacion('consejero_auxiliar', $parteConsejeroAuxiliar, 'principal', 'consejero_auxiliar', 'principal', 'general') !!}
                        </div>

                        <div>
                            <div class="vm-mini-label">Ayudante sala auxiliar</div>
                            {!! $selectAsignacion('ayudante_auxiliar', $parteAyudanteAuxiliar, 'principal', 'ayudante_auxiliar', 'principal', 'general') !!}
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm align-middle mb-0 vm-table">
                            <thead>
                                <tr>
                                    <th style="width:115px;">Horario</th>
                                    <th>Parte</th>
                                    <th style="width:100px;">Min</th>
                                    <th style="width:360px;">Asignado</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="bloque-horario">
                                    <td>
                                        <div class="hora-preview">-</div>
                                    </td>

                                    <td>
                                        <div class="form-control form-control-sm bg-light">
                                            Canción, oración y palabras de introducción
                                        </div>
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="{{ $parteName('oracion_inicio', $parteOracionInicio, 'duracion_minutos') }}"
                                               class="form-control form-control-sm duracion-input"
                                               value="{{ $valorParte('oracion_inicio', $parteOracionInicio, 'duracion_minutos', 6) }}"
                                               min="0"
                                               max="30">
                                    </td>

                                    <td>
                                        {!! $selectAsignacion('oracion_inicio', $parteOracionInicio, 'principal', 'oracion', 'principal', 'general') !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="vm-section vm-tesoros">TESOROS DE LA BIBLIA</div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 vm-table">
                            <thead>
                                <tr>
                                    <th style="width:55px;">N°</th>
                                    <th style="width:105px;">Horario</th>
                                    <th>Parte / tema</th>
                                    <th style="width:85px;">Min</th>
                                    <th style="width:320px;">{{ $nombreSalaAuxiliar }}</th>
                                    <th style="width:320px;">Sala principal</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="bloque-horario fila-numerable" data-fija="1">
                                    <td><div class="numero-preview">{{ $parteTesoro?->numero ?: 1 }}</div></td>
                                    <td><div class="hora-preview">-</div></td>

                                    <td>
                                        {!! $hiddenParte('tesoro', $parteTesoro, 'tesoros', 'tesoro') !!}
                                        <input type="text"
                                               name="{{ $parteName('tesoro', $parteTesoro, 'titulo') }}"
                                               class="form-control form-control-sm"
                                               value="{{ $valorParte('tesoro', $parteTesoro, 'titulo') }}"
                                               placeholder="Tema del discurso">
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="{{ $parteName('tesoro', $parteTesoro, 'duracion_minutos') }}"
                                               class="form-control form-control-sm duracion-input"
                                               value="{{ $valorParte('tesoro', $parteTesoro, 'duracion_minutos', 10) }}"
                                               min="0"
                                               max="300">
                                    </td>

                                    <td class="vm-na">No aplica</td>

                                    <td>
                                        {!! $selectAsignacion('tesoro', $parteTesoro, 'disertante', 'tesoro', 'disertante', 'general') !!}
                                    </td>
                                </tr>

                                <tr class="bloque-horario fila-numerable" data-fija="1">
                                    <td><div class="numero-preview">{{ $partePerlas?->numero ?: 2 }}</div></td>
                                    <td><div class="hora-preview">-</div></td>

                                    <td>
                                        {!! $hiddenParte('perlas', $partePerlas, 'tesoros', 'perlas', 'Busquemos perlas escondidas') !!}
                                        <div class="form-control form-control-sm bg-light">
                                            Busquemos perlas escondidas
                                        </div>
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="{{ $parteName('perlas', $partePerlas, 'duracion_minutos') }}"
                                               class="form-control form-control-sm duracion-input"
                                               value="{{ $valorParte('perlas', $partePerlas, 'duracion_minutos', 10) }}"
                                               min="0"
                                               max="300">
                                    </td>

                                    <td class="vm-na">No aplica</td>

                                    <td>
                                        {!! $selectAsignacion('perlas', $partePerlas, 'disertante', 'perlas', 'disertante', 'general') !!}
                                    </td>
                                </tr>

                                @php
                                    $tieneAuxLectura = $asignacionActual($parteLectura, 'estudiante', 'auxiliar');
                                @endphp

                                <tr class="bloque-horario fila-numerable" data-fija="1">
                                    <td><div class="numero-preview">{{ $parteLectura?->numero ?: 3 }}</div></td>
                                    <td><div class="hora-preview">-</div></td>

                                    <td>
                                        {!! $hiddenParte('lectura_biblia', $parteLectura, 'tesoros', 'lectura_biblia', 'Lectura de la Biblia') !!}
                                        <div class="form-control form-control-sm bg-light">
                                            Lectura de la Biblia
                                        </div>
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="{{ $parteName('lectura_biblia', $parteLectura, 'duracion_minutos') }}"
                                               class="form-control form-control-sm duracion-input"
                                               value="{{ $valorParte('lectura_biblia', $parteLectura, 'duracion_minutos', 4) }}"
                                               min="0"
                                               max="300">
                                    </td>

                                    <td>
                                        <div class="form-check vm-check mb-1">
                                            <input type="checkbox"
                                                   class="form-check-input toggle-auxiliar"
                                                   id="aux_lectura"
                                                   data-target="#aux_lectura_box"
                                                   {{ $tieneAuxLectura ? 'checked' : '' }}>
                                            <label class="form-check-label" for="aux_lectura">
                                                Usar auxiliar
                                            </label>
                                        </div>

                                        <div id="aux_lectura_box" class="{{ $tieneAuxLectura ? '' : 'd-none' }}">
                                            {!! $selectAsignacion('lectura_biblia', $parteLectura, 'auxiliar', 'lectura_biblia', 'estudiante', 'auxiliar') !!}
                                        </div>
                                    </td>

                                    <td>
                                        {!! $selectAsignacion('lectura_biblia', $parteLectura, 'principal', 'lectura_biblia', 'estudiante', 'principal') !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="vm-section vm-maestros d-flex justify-content-between align-items-center">
                <span>SEAMOS MEJORES MAESTROS</span>

                <button type="button" class="btn btn-light btn-sm border" onclick="agregarParteMaestros()">
                    <i class="fa-solid fa-plus"></i> Agregar parte
                </button>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 vm-table">
                            <thead>
                                <tr>
                                    <th style="width:55px;">N°</th>
                                    <th style="width:105px;">Horario</th>
                                    <th>Parte / tema</th>
                                    <th style="width:85px;">Min</th>
                                    <th style="width:320px;">
                                        <div>{{ $nombreSalaAuxiliar }}</div>
                                        <div class="vm-subhead-grid">
                                            <span>Estudiante</span>
                                            <span>Ayudante</span>
                                        </div>
                                    </th>
                                    <th style="width:320px;">
                                        <div>Sala principal</div>
                                        <div class="vm-subhead-grid">
                                            <span>Estudiante</span>
                                            <span>Ayudante</span>
                                        </div>
                                    </th>
                                    <th style="width:75px;"></th>
                                </tr>
                            </thead>

                            <tbody id="contenedor_maestros">
                                @foreach($maestrosRows as $index => $row)
                                    @php
                                        $key = $row['key'];
                                        $parte = $row['parte'];

                                        $tieneAuxMaestros = $asignacionActual($parte, 'estudiante', 'auxiliar')
                                            || $asignacionActual($parte, 'ayudante', 'auxiliar');
                                    @endphp

                                    <tr class="bloque-horario fila-numerable" data-fija="1">
                                        <td><div class="numero-preview">{{ $parte?->numero ?: ($index + 4) }}</div></td>
                                        <td><div class="hora-preview">-</div></td>

                                        <td>
                                            {!! $hiddenParte($key, $parte, 'maestros', 'maestro_estudiante') !!}
                                            <input type="text"
                                                   name="{{ $parteName($key, $parte, 'titulo') }}"
                                                   class="form-control form-control-sm"
                                                   value="{{ $valorParte($key, $parte, 'titulo') }}"
                                                   placeholder="Ej: Empiece conversaciones">
                                        </td>

                                        <td>
                                            <input type="number"
                                                   name="{{ $parteName($key, $parte, 'duracion_minutos') }}"
                                                   class="form-control form-control-sm duracion-input"
                                                   value="{{ $valorParte($key, $parte, 'duracion_minutos') }}"
                                                   min="0"
                                                   max="300">
                                        </td>

                                        <td>
                                            <div class="form-check vm-check mb-1">
                                                <input type="checkbox"
                                                       class="form-check-input toggle-auxiliar"
                                                       id="aux_{{ $key }}"
                                                       data-target="#aux_box_{{ $key }}"
                                                       {{ $tieneAuxMaestros ? 'checked' : '' }}>
                                                <label class="form-check-label" for="aux_{{ $key }}">
                                                    Usar auxiliar
                                                </label>
                                            </div>

                                            <div id="aux_box_{{ $key }}" class="{{ $tieneAuxMaestros ? '' : 'd-none' }}">
                                                <div class="vm-pair">
                                                    {!! $selectAsignacion($key, $parte, 'auxiliar_estudiante', 'maestro_estudiante', 'estudiante', 'auxiliar') !!}
                                                    {!! $selectAsignacion($key, $parte, 'auxiliar_ayudante', 'maestro_ayudante', 'ayudante', 'auxiliar') !!}
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="vm-pair">
                                                {!! $selectAsignacion($key, $parte, 'principal_estudiante', 'maestro_estudiante', 'estudiante', 'principal') !!}
                                                {!! $selectAsignacion($key, $parte, 'principal_ayudante', 'maestro_ayudante', 'ayudante', 'principal') !!}
                                            </div>
                                        </td>

                                        <td>
                                            @if($esEdit && $parte)
                                                <label class="small text-danger">
                                                    <input type="checkbox"
                                                           name="partes[{{ $parte->id }}][eliminar]"
                                                           value="1"
                                                           class="form-check-input eliminar-parte">
                                                    Eliminar
                                                </label>
                                            @else
                                                <span class="badge bg-light text-dark border">Base</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="vm-section vm-vida d-flex justify-content-between align-items-center">
                <span>NUESTRA VIDA CRISTIANA</span>

                <button type="button" class="btn btn-light btn-sm border" onclick="agregarTemaVida()">
                    <i class="fa-solid fa-plus"></i> Agregar tema
                </button>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 vm-table">
                            <thead>
                                <tr>
                                    <th style="width:55px;">N°</th>
                                    <th style="width:105px;">Horario</th>
                                    <th>Parte / tema</th>
                                    <th style="width:85px;">Min</th>
                                    <th style="width:320px;">Asignado</th>
                                    <th style="width:75px;"></th>
                                </tr>
                            </thead>

                            <tbody id="contenedor_vida">
                                <tr id="fila_cancion_medio">
                                    <td></td>
                                    <td>
                                        <div class="hora-preview" id="horaCancionMedioPreview">-</div>
                                    </td>
                                    <td colspan="3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Canción intermedia</span>
                                            <input type="text"
                                                   name="cancion_medio"
                                                   class="form-control"
                                                   value="{{ old('cancion_medio', $programa?->cancion_medio) }}"
                                                   placeholder="Ej: 45">
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>

                                @foreach($vidaRows as $index => $row)
                                    @php
                                        $key = $row['key'];
                                        $parte = $row['parte'];
                                    @endphp

                                    <tr class="bloque-horario fila-numerable" data-fija="1">
                                        <td><div class="numero-preview">{{ $parte?->numero ?: 7 }}</div></td>
                                        <td><div class="hora-preview">-</div></td>

                                        <td>
                                            {!! $hiddenParte($key, $parte, 'vida', 'vida_cristiana') !!}
                                            <input type="text"
                                                   name="{{ $parteName($key, $parte, 'titulo') }}"
                                                   class="form-control form-control-sm"
                                                   value="{{ $valorParte($key, $parte, 'titulo') }}"
                                                   placeholder="Ej: Necesidades de la congregación">
                                        </td>

                                        <td>
                                            <input type="number"
                                                   name="{{ $parteName($key, $parte, 'duracion_minutos') }}"
                                                   class="form-control form-control-sm duracion-input"
                                                   value="{{ $valorParte($key, $parte, 'duracion_minutos') }}"
                                                   min="0"
                                                   max="300">
                                        </td>

                                        <td>
                                            {!! $selectAsignacion($key, $parte, 'disertante', 'vida_cristiana', 'disertante', 'general') !!}
                                        </td>

                                        <td>
                                            @if($esEdit && $parte)
                                                <label class="small text-danger">
                                                    <input type="checkbox"
                                                           name="partes[{{ $parte->id }}][eliminar]"
                                                           value="1"
                                                           class="form-check-input eliminar-parte">
                                                    Eliminar
                                                </label>
                                            @else
                                                <span class="badge bg-light text-dark border">Base</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                @php
                                    $key = 'estudio';
                                    $parte = $parteEstudio;
                                @endphp

                                <tr class="bloque-horario fila-numerable" data-fija="1" id="fila_estudio_biblico">
                                    <td><div class="numero-preview">{{ $parteEstudio?->numero ?: 8 }}</div></td>
                                    <td><div class="hora-preview">-</div></td>

                                    <td>
                                        {!! $hiddenParte('estudio', $parteEstudio, 'vida', 'estudio_conductor') !!}
                                        <input type="text"
                                               name="{{ $parteName('estudio', $parteEstudio, 'titulo') }}"
                                               class="form-control form-control-sm"
                                               value="{{ $valorParte('estudio', $parteEstudio, 'titulo', 'Estudio bíblico de la congregación') }}">
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="{{ $parteName('estudio', $parteEstudio, 'duracion_minutos') }}"
                                               class="form-control form-control-sm duracion-input"
                                               value="{{ $valorParte('estudio', $parteEstudio, 'duracion_minutos', 30) }}"
                                               min="0"
                                               max="300">
                                    </td>

                                    <td>
                                        <div class="vm-pair">
                                            {!! $selectAsignacion('estudio', $parteEstudio, 'conductor', 'estudio_conductor', 'conductor', 'general') !!}
                                            {!! $selectAsignacion('estudio', $parteEstudio, 'lector', 'estudio_lector', 'lector', 'general') !!}
                                        </div>
                                        <div class="vm-pair-labels mt-1">
                                            <span>Conductor</span>
                                            <span>Lector</span>
                                        </div>
                                    </td>

                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white fw-bold py-2">
                    Conclusión, canción final y oración
                </div>

                <div class="card-body p-0">
                    {!! $hiddenParte('oracion_final', $parteOracionFinal, 'final', 'oracion', 'Palabras de conclusión, canción final y oración') !!}

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 vm-table">
                            <thead>
                                <tr>
                                    <th style="width:105px;">Horario</th>
                                    <th>Parte</th>
                                    <th style="width:85px;">Min</th>
                                    <th style="width:180px;">Canción final</th>
                                    <th style="width:360px;">Oración</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="bloque-horario">
                                    <td>
                                        <div class="hora-preview">-</div>
                                    </td>

                                    <td>
                                        <div class="form-control form-control-sm bg-light">
                                            Palabras de conclusión
                                        </div>
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="{{ $parteName('oracion_final', $parteOracionFinal, 'duracion_minutos') }}"
                                               class="form-control form-control-sm duracion-input"
                                               value="{{ $valorParte('oracion_final', $parteOracionFinal, 'duracion_minutos', 3) }}"
                                               min="0"
                                               max="30">
                                    </td>

                                    <td>
                                        <input type="text"
                                               name="cancion_final"
                                               class="form-control form-control-sm"
                                               value="{{ old('cancion_final', $programa?->cancion_final) }}">
                                    </td>

                                    <td>
                                        {!! $selectAsignacion('oracion_final', $parteOracionFinal, 'principal', 'oracion', 'principal', 'general') !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end gap-2">
           <a href="{{ route('vida-ministerio.index', request()->only(['desde', 'hasta'])) }}" class="btn btn-light border">
                Cancelar
            </a>

            <button class="btn btn-primary">
                <i class="fa-solid fa-save"></i>
                {{ $esEdit ? 'Guardar cambios' : 'Crear programa' }}
            </button>
        </div>
    </form>
</div>

<template id="template_maestros">
    <tr class="bloque-horario fila-numerable nueva-parte" data-fija="1">
        <td><div class="numero-preview">+</div></td>
        <td><div class="hora-preview">-</div></td>

        <td>
            <input type="hidden" name="nuevas_partes[__INDEX__][seccion]" value="maestros">
            <input type="hidden" name="nuevas_partes[__INDEX__][tipo_asignacion]" value="maestro_estudiante">

            <input type="text"
                   name="nuevas_partes[__INDEX__][titulo]"
                   class="form-control form-control-sm"
                   placeholder="Ej: Empiece conversaciones">
        </td>

        <td>
            <input type="number"
                   name="nuevas_partes[__INDEX__][duracion_minutos]"
                   class="form-control form-control-sm duracion-input"
                   min="0"
                   max="300">
        </td>

        <td>
            <div class="form-check vm-check mb-1">
                <input type="checkbox"
                       class="form-check-input toggle-auxiliar"
                       id="aux_new_maestros___INDEX__"
                       data-target="#aux_new_maestros_box___INDEX__">

                <label class="form-check-label" for="aux_new_maestros___INDEX__">
                    Usar auxiliar
                </label>
            </div>

            <div id="aux_new_maestros_box___INDEX__" class="d-none">
                <div class="vm-pair">
                    {!! $selectNueva('__INDEX__', 'auxiliar_estudiante', 'maestro_estudiante', 'estudiante', 'auxiliar') !!}
                    {!! $selectNueva('__INDEX__', 'auxiliar_ayudante', 'maestro_ayudante', 'ayudante', 'auxiliar') !!}
                </div>
            </div>
        </td>

        <td>
            <div class="vm-pair">
                {!! $selectNueva('__INDEX__', 'principal_estudiante', 'maestro_estudiante', 'estudiante', 'principal') !!}
                {!! $selectNueva('__INDEX__', 'principal_ayudante', 'maestro_ayudante', 'ayudante', 'principal') !!}
            </div>
        </td>

        <td>
            <button type="button"
                    class="btn btn-outline-danger btn-sm"
                    onclick="this.closest('tr').remove(); recalcularVistaHorarios(); actualizarNumerosPartes();">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<template id="template_vida">
    <tr class="bloque-horario fila-numerable nueva-parte" data-fija="1">
        <td><div class="numero-preview">+</div></td>
        <td><div class="hora-preview">-</div></td>

        <td>
            <input type="hidden" name="nuevas_partes[__INDEX__][seccion]" value="vida">
            <input type="hidden" name="nuevas_partes[__INDEX__][tipo_asignacion]" value="vida_cristiana">

            <input type="text"
                   name="nuevas_partes[__INDEX__][titulo]"
                   class="form-control form-control-sm"
                   placeholder="Ej: Necesidades de la congregación">
        </td>

        <td>
            <input type="number"
                   name="nuevas_partes[__INDEX__][duracion_minutos]"
                   class="form-control form-control-sm duracion-input"
                   min="0"
                   max="300">
        </td>

        <td>
            {!! $selectNueva('__INDEX__', 'disertante', 'vida_cristiana', 'disertante', 'general') !!}
        </td>

        <td>
            <button type="button"
                    class="btn btn-outline-danger btn-sm"
                    onclick="this.closest('tr').remove(); recalcularVistaHorarios(); actualizarNumerosPartes();">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<style>
.vm-section {
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    padding: 8px 12px;
    border-radius: 8px 8px 0 0;
    margin-top: 14px;
}

.vm-tesoros {
    background: #2a6f74;
}

.vm-maestros {
    background: #c69214;
}

.vm-vida {
    background: #a73229;
}

.vm-table th {
    font-size: 11px;
    color: #555;
    background: #f8fafc;
    white-space: nowrap;
    vertical-align: middle;
}

.vm-table td {
    font-size: 12.5px;
    vertical-align: top;
}

.vm-table .form-control-sm,
.vm-table .form-select-sm,
.vm-select {
    font-size: 12px;
    min-height: 30px;
    padding-top: 3px;
    padding-bottom: 3px;
}

.vm-select {
    padding-left: 5px;
    padding-right: 22px;
}

.numero-preview,
.hora-preview {
    font-size: 12.5px;
    font-weight: 700;
    color: #374151;
    padding-top: 4px;
    white-space: nowrap;
}

.vm-na {
    color: #9ca3af;
    font-size: 12px;
    padding-top: 10px !important;
}

.vm-pair {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px;
}

.vm-pair-labels,
.vm-subhead-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px;
    color: #6b7280;
    font-size: 10px;
    font-weight: 600;
}

.vm-check {
    font-size: 11px;
    color: #555;
}

.vm-mini-label {
    font-size: 11px;
    color: #555;
    font-weight: 700;
    margin-bottom: 2px;
}

.vm-grid-encargados {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 8px;
}

.form-label {
    font-weight: 600;
}

.nueva-parte {
    background: #f8fafc;
}

@media (max-width: 992px) {
    .vm-grid-encargados {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .vm-pair {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
let nuevaParteIndex = 0;

function agregarParteMaestros() {
    agregarNuevaParte('template_maestros', 'contenedor_maestros', null);
}

function agregarTemaVida() {
    const estudio = document.getElementById('fila_estudio_biblico');
    agregarNuevaParte('template_vida', 'contenedor_vida', estudio);
}

function agregarNuevaParte(templateId, contenedorId, beforeNode = null) {
    const template = document.getElementById(templateId);
    const clone = template.content.cloneNode(true);
    const wrapper = clone.querySelector('.nueva-parte');
    const index = Date.now() + '_' + nuevaParteIndex++;

    wrapper.querySelectorAll('[name]').forEach(input => {
        input.name = input.name.replaceAll('__INDEX__', index);
    });

    wrapper.querySelectorAll('[id]').forEach(input => {
        input.id = input.id.replaceAll('__INDEX__', index);
    });

    wrapper.querySelectorAll('[for]').forEach(input => {
        input.setAttribute('for', input.getAttribute('for').replaceAll('__INDEX__', index));
    });

    wrapper.querySelectorAll('[data-target]').forEach(input => {
        input.dataset.target = input.dataset.target.replaceAll('__INDEX__', index);
    });

    const contenedor = document.getElementById(contenedorId);

    if (beforeNode) {
        contenedor.insertBefore(wrapper, beforeNode);
    } else {
        contenedor.appendChild(wrapper);
    }

    wrapper.querySelectorAll('.duracion-input').forEach(input => {
        input.addEventListener('input', function () {
            recalcularVistaHorarios();
            actualizarNumerosPartes();
        });
    });

    wrapper.querySelectorAll('input[name*="[titulo]"]').forEach(input => {
        input.addEventListener('input', actualizarNumerosPartes);
    });

    wrapper.querySelectorAll('.toggle-auxiliar').forEach(check => {
        check.addEventListener('change', aplicarEstadoAuxiliares);
    });

    aplicarEstadoAuxiliares();
    recalcularVistaHorarios();
    actualizarNumerosPartes();
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
        actualizarHoraCancionMedio();
        return;
    }

    let cursor = horaInicio;

    document.querySelectorAll('.bloque-horario').forEach(row => {
        const duracionInput = row.querySelector('.duracion-input');
        const preview = row.querySelector('.hora-preview');

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

    actualizarHoraCancionMedio();
}

function actualizarHoraCancionMedio() {
    const previewCancion = document.getElementById('horaCancionMedioPreview');
    const contenedorVida = document.getElementById('contenedor_vida');

    if (!previewCancion || !contenedorVida) {
        return;
    }

    const primeraParteVida = Array.from(contenedorVida.querySelectorAll('tr.bloque-horario'))
        .find(row => row.id !== 'fila_estudio_biblico');

    const textoHorario = primeraParteVida?.querySelector('.hora-preview')?.textContent || '-';

    if (!textoHorario || textoHorario === '-') {
        previewCancion.textContent = '-';
        return;
    }

    previewCancion.textContent = textoHorario.split(' - ')[0];
}

function actualizarNumerosPartes() {
    let numero = 1;

    document.querySelectorAll('.fila-numerable').forEach(row => {
        const preview = row.querySelector('.numero-preview');

        if (!preview) {
            return;
        }

        const eliminado = row.querySelector('.eliminar-parte')?.checked;

        if (eliminado) {
            preview.textContent = '-';
            return;
        }

        preview.textContent = numero++;
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

function actualizarNombreSala() {
    const value = document.getElementById('nombreSalaAuxiliar')?.value || 'Sala auxiliar';

    document.querySelectorAll('.nombre-sala-preview').forEach(el => {
        el.textContent = value;
    });

    document.querySelectorAll('.vm-table th').forEach(th => {
        if (th.innerText.includes('Sala auxiliar')) {
            const firstDiv = th.querySelector('div:first-child');
            if (firstDiv) {
                firstDiv.textContent = value;
            }
        }
    });
}

function actualizarVistaPorTipo() {
    const tipoSemana = document.getElementById('tipoSemana');
    const esAviso = tipoSemana?.value === 'aviso';

    document.querySelectorAll('.normal-block').forEach(block => {
        block.classList.toggle('d-none', esAviso);

        block.querySelectorAll('input, select, textarea').forEach(input => {
            input.disabled = esAviso;
        });
    });

    document.querySelectorAll('.aviso-block').forEach(block => {
        block.classList.toggle('d-none', !esAviso);

        block.querySelectorAll('input, select, textarea').forEach(input => {
            input.disabled = !esAviso;
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('hora_inicio')?.addEventListener('input', recalcularVistaHorarios);
    document.getElementById('nombreSalaAuxiliar')?.addEventListener('input', actualizarNombreSala);
    document.getElementById('tipoSemana')?.addEventListener('change', actualizarVistaPorTipo);

    document.querySelectorAll('.duracion-input').forEach(input => {
        input.addEventListener('input', function () {
            recalcularVistaHorarios();
            actualizarNumerosPartes();
        });
    });

    document.querySelectorAll('input[name*="[titulo]"]').forEach(input => {
        input.addEventListener('input', actualizarNumerosPartes);
    });

    document.querySelectorAll('.toggle-auxiliar').forEach(check => {
        check.addEventListener('change', aplicarEstadoAuxiliares);
    });

    document.querySelectorAll('.eliminar-parte').forEach(check => {
        check.addEventListener('change', actualizarNumerosPartes);
    });

    actualizarNombreSala();
    aplicarEstadoAuxiliares();
    actualizarVistaPorTipo();
    recalcularVistaHorarios();
    actualizarNumerosPartes();
});
</script>