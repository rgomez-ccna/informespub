@php
    $name = 'campos[' . $campo->id . ']';
@endphp

@if($campo->tipo === 'textarea')
    <textarea name="{{ $name }}" class="form-control" rows="3">{{ $valorActual }}</textarea>

@elseif($campo->tipo === 'numero')
    <input type="number"
           step="0.01"
           name="{{ $name }}"
           class="form-control"
           value="{{ $valorActual }}">

@elseif($campo->tipo === 'fecha')
    <input type="date"
           name="{{ $name }}"
           class="form-control"
           value="{{ $valorActual }}">

@elseif($campo->tipo === 'hora')
    <input type="time"
           name="{{ $name }}"
           class="form-control"
           value="{{ $valorActual }}">

@elseif($campo->tipo === 'select')
    <select name="{{ $name }}" class="form-select">
        <option value="">Seleccionar</option>

        @foreach(($campo->opciones ?? []) as $opcion)
            <option value="{{ $opcion }}" {{ $valorActual == $opcion ? 'selected' : '' }}>
                {{ $opcion }}
            </option>
        @endforeach
    </select>

@elseif($campo->tipo === 'checkbox')
    <div class="form-check">
        <input type="checkbox"
               name="{{ $name }}"
               value="1"
               class="form-check-input"
               {{ $valorActual ? 'checked' : '' }}>

        <label class="form-check-label">Sí</label>
    </div>

@else
    <input type="text"
           name="{{ $name }}"
           class="form-control"
           value="{{ $valorActual }}">
@endif