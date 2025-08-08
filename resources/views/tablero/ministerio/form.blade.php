@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 640px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold text-secondary">
            {{ isset($registro) ? 'Editar salida' : 'Nueva salida' }}
        </h5>
        <a href="{{ route('ministerio.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <form action="{{ isset($registro) ? route('ministerio.update', $registro) : route('ministerio.store') }}"
          method="POST" >
        @csrf
        @isset($registro) @method('PUT') @endisset

        {{-- Fecha y hora --}}
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <label class="form-label mb-0">Fecha</label>
                <input type="date" name="fecha" class="form-control form-control-sm"
                       value="{{ old('fecha', $registro->fecha ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label mb-0">Hora (texto)</label>
                <input type="text" name="hora" class="form-control form-control-sm"
                       value="{{ old('hora', $registro->hora ?? '') }}">
            </div>
        </div>

        {{-- Detalles --}}
        <div class="mb-2" style="position: relative; "> {{-- max-width opcional --}}
            <label class="form-label mb-0">Conductor</label>
            <input type="text" name="conductor"
                class="form-control form-control-sm buscador-nombre"
                autocomplete="off"
                placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('conductor', $registro->conductor ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm"
                style="z-index:9999; display:none; position:absolute; top:100%; left:0; width:100%; max-height:200px; overflow-y:auto;"></div>
        </div>



        <div class="mb-2">
            <label class="form-label mb-0">Punto de encuentro / Zoom</label>
            <input type="text" name="punto_encuentro" class="form-control form-control-sm"
                   value="{{ old('punto_encuentro', $registro->punto_encuentro ?? '') }}">
        </div>

       <div class="mb-4">
    <label class="form-label mb-0">
        Territorio <span class="text-muted">(o texto del evento / nota especial)</span>
    </label>
    <input type="text" name="territorio" class="form-control form-control-sm"
           placeholder="Ej.: 12-A — o: “Evento especial, a definir por cada grupo, etc.”"
           value="{{ old('territorio', $registro->territorio ?? '') }}">
</div>


        {{-- Checkboxes --}}
        <div class="row g-2 mb-3">
            <div class="col-md-6 form-check">
                <input class="form-check-input" type="checkbox" name="es_nueva_semana" value="1"
                       {{ old('es_nueva_semana', $registro->es_nueva_semana ?? false) ? 'checked' : '' }}>
                 <label class="form-check-label fw-semibold text-dark">
                    Solo marcar si este registro arranca un nuevo programa
                </label>
                <small class="text-muted d-block">Se usará para agrupar desde esta fecha en adelante.</small>
            </div>

            <div class="col-md-6 form-check">
                <input class="form-check-input" type="checkbox" name="es_fila_info" value="1"
                       {{ old('es_fila_info', $registro->es_fila_info ?? false) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold">
                    Fila informativa (Evento especial, a definir por cada grupo, etc.)
                </label>
            </div>
        </div>

        <div class="text-end">
            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-check"></i> Guardar
            </button>
        </div>
    </form>
</div>

<style>
    .dropdown-sugerencias .dropdown-item {
    padding: 4px 8px;
    cursor: pointer;
}
.dropdown-sugerencias .dropdown-item:hover,
.dropdown-sugerencias .activo {
    background-color: #f0f0f0;
}

</style>

<script>
document.querySelectorAll('.buscador-nombre').forEach(input => {
    const contenedor = input.parentElement.querySelector('.dropdown-sugerencias');
    let indice = -1;

    input.addEventListener('input', () => {
        const valor = input.value.trim();
        if (valor.length < 2) {
            contenedor.style.display = 'none';
            return;
        }

        fetch(`/buscar-publicadores?q=${encodeURIComponent(valor)}`)
            .then(r => r.json())
            .then(data => {
                contenedor.innerHTML = '';
                indice = -1;
                if (data.length === 0) {
                    contenedor.style.display = 'none';
                    return;
                }

               data.forEach((nombre, idx) => {
                    const opcion = document.createElement('div');
                    opcion.textContent = nombre;
                    opcion.classList.add('dropdown-item');

                    opcion.onclick = () => {
                        input.value = nombre;
                        contenedor.style.display = 'none';
                    };

                    opcion.onmouseover = () => {
                        const todas = contenedor.querySelectorAll('div');
                        todas.forEach(op => op.classList.remove('activo'));
                        opcion.classList.add('activo');
                        indice = idx;
                    };

                    contenedor.appendChild(opcion);
                });

                contenedor.style.display = 'block';
            });
    });

    input.addEventListener('keydown', (e) => {
        const opciones = contenedor.querySelectorAll('div');
        if (!opciones.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            indice = (indice + 1) % opciones.length;
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            indice = (indice - 1 + opciones.length) % opciones.length;
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (indice >= 0) {
                input.value = opciones[indice].textContent;
                contenedor.style.display = 'none';
                indice = -1;
            }
        }

        opciones.forEach((op, i) => {
            op.classList.toggle('activo', i === indice);
        });
    });

    document.addEventListener('click', e => {
        if (!contenedor.contains(e.target) && e.target !== input) {
            contenedor.style.display = 'none';
        }
    });
});
</script>
@endsection
