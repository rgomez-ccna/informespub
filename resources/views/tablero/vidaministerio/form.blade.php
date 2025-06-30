@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-3 text-secondary"> 
    {{ $programa ? 'Editar Programa' : 'Nuevo Programa' }} – Vida y Ministerio
    </h4>

        <a href="{{ route('vidaministerio.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al programa
        </a>
    </div>

<form action="{{ $programa ? route('vidaministerio.update', $programa->id) : route('vidaministerio.store') }}" method="POST">
    @csrf
    @if($programa) @method('PUT') @endif


        {{-- FECHA + LECTURA SEMANAL --}}
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control form-control-sm" required
                    value="{{ old('fecha', $programa->fecha ?? '') }}">
            </div>
            <div class="col-md-8">
                <label class="form-label">Lectura semanal de la Biblia (ej: Proverbios 12 y 13)</label>
                <input type="text" name="lectura_semanal" class="form-control form-control-sm"
                    value="{{ old('lectura_semanal', $programa->lectura_semanal ?? '') }}">
            </div>
        </div>


{{-- ENCABEZADO --}}
<div class="row g-2 mb-3">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">Presidente</span>
            <input type="text" name="presidente" class="form-control form-control-sm buscador-nombre"
                autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('presidente', $programa->presidente ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
        </div>
        <div class="input-group mt-1">
            <span class="input-group-text">Ayudante Aud. Principal</span>
            <input type="text" name="presidente_ayudante" class="form-control form-control-sm buscador-nombre"
                autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('presidente_ayudante', $programa->presidente_ayudante ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">Consejero Sala Auxiliar</span>
            <input type="text" name="consejero_auxiliar" class="form-control form-control-sm buscador-nombre"
                autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('consejero_auxiliar', $programa->consejero_auxiliar ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
        </div>
        <div class="input-group mt-1">
            <span class="input-group-text">Ayudante Sala Aux.</span>
            <input type="text" name="consejero_ayudante" class="form-control form-control-sm buscador-nombre"
                autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('consejero_ayudante', $programa->consejero_ayudante ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
        </div>
    </div>
</div>


<div class="row g-2 mb-2">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">Canción de inicio</span>
            <input type="text" name="cancion_inicio" class="form-control form-control-sm"
                placeholder="Ej. 123"
                value="{{ old('cancion_inicio', $programa->cancion_inicio ?? '') }}">
        </div>
        <p class="small text-muted mt-1 mb-0">• Palabras de introducción (1 min.)</p>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">Oración de inicio</span>
            <input type="text" name="oracion_inicio" class="form-control form-control-sm buscador-nombre"
                autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('oracion_inicio', $programa->oracion_inicio ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
        </div>
    </div>
</div>




   
       

        {{-- TESOROS DE LA BIBLIA --}}
     <div class="d-flex align-items-center mb-2 mt-4">
    <div class="me-2" style="background: #2a6f74; padding: 6px; border-radius: 4px;">
        <i class="fa-solid fa-gem text-white"></i>
    </div>
        <h6 class="m-0 fw-bold text-uppercase" style="color: #2a6f74;">Tesoros de la Biblia</h6>
    </div>
    <hr class="mt-1 mb-3">

    <div class="row g-2 mb-2">
        <div class="col-md-8">
            <label class="form-label">1. Tema (10 min)</label>
            <input type="text" name="tesoro_titulo" class="form-control form-control-sm"
                value="{{ old('tesoro_titulo', $programa->tesoro_titulo ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Disertante</label>
            <div class="position-relative">
                <input type="text" name="tesoro_disertante" class="form-control form-control-sm buscador-nombre"
                    autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                    value="{{ old('tesoro_disertante', $programa->tesoro_disertante ?? '') }}">
                <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
            </div>
        </div>
    </div>


    <div class="row g-2 mb-2">
    <div class="col-md-5">
        <label class="form-label">2. Busquemos perlas escondidas – Disertante</label>
        <div class="position-relative">
            <input type="text" name="perlas_disertante" class="form-control form-control-sm buscador-nombre"
                autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('perlas_disertante', $programa->perlas_disertante ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
        </div>
    </div>

    <div class="col-md-7">
        <label class="form-label">3. Lectura de la Biblia – Estudiantes</label>
        <div class="input-group">
            <span class="input-group-text">Sala Principal</span>
            <input type="text" name="lectura_lector_principal" class="form-control form-control-sm buscador-nombre"
                autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('lectura_lector_principal', $programa->lectura_lector_principal ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>

            <span class="input-group-text">Sala Auxiliar</span>
            <input type="text" name="lectura_lector_auxiliar" class="form-control form-control-sm buscador-nombre"
                autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('lectura_lector_auxiliar', $programa->lectura_lector_auxiliar ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
        </div>
    </div>
</div>

        {{-- SEAMOS MEJORES MAESTROS --}}
       <div class="d-flex align-items-center mb-2 mt-4">
            <div class="me-2" style="background: #c69214; padding: 6px; border-radius: 4px;">
                <i class="fa-solid fa-wheat-awn text-white"></i>
            </div>
            <h6 class="m-0 fw-bold text-uppercase" style="color: #c69214;">Seamos Mejores Maestros</h6>
        </div>
        <hr class="mt-1 mb-3">

        @for ($i = 0; $i < 4; $i++)
            @php
                $asignacion = $programa->asignaciones_maestros[$i] ?? [];
            @endphp
            <div class="border p-2 mb-2 rounded">
                <small class="text-muted">Asignación {{ $i + 4 }}</small>

                <div class="mb-2">
                    <label class="form-label">TEMA</label>
                    <input type="text" name="asignaciones_maestros[{{ $i }}][titulo]" class="form-control form-control-sm"
                        placeholder="Ej: Empiece conversaciones (2min.), Haga revisitas (4min.) etc. o título del discurso"
                        value="{{ old("asignaciones_maestros.$i.titulo", $asignacion['titulo'] ?? '') }}">
                </div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label"><b>Auditorio Principal</b> – Estudiante / Ayudante</label>
                        <div class="input-group">
                            <input type="text" name="asignaciones_maestros[{{ $i }}][principal][estudiante]"
                                class="form-control form-control-sm buscador-nombre" autocomplete="off"
                                placeholder="Escribí 2 o más letras del nombre"
                                value="{{ old("asignaciones_maestros.$i.principal.estudiante", $asignacion['principal']['estudiante'] ?? '') }}">
                            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>

                            <input type="text" name="asignaciones_maestros[{{ $i }}][principal][ayudante]"
                                class="form-control form-control-sm buscador-nombre" autocomplete="off"
                                placeholder="Escribí 2 o más letras del nombre"
                                value="{{ old("asignaciones_maestros.$i.principal.ayudante", $asignacion['principal']['ayudante'] ?? '') }}">
                            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sala Auxiliar – Estudiante / Ayudante</label>
                        <div class="input-group">
                            <input type="text" name="asignaciones_maestros[{{ $i }}][auxiliar][estudiante]"
                                class="form-control form-control-sm buscador-nombre" autocomplete="off"
                                placeholder="Escribí 2 o más letras del nombre"
                                value="{{ old("asignaciones_maestros.$i.auxiliar.estudiante", $asignacion['auxiliar']['estudiante'] ?? '') }}">
                            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>

                            <input type="text" name="asignaciones_maestros[{{ $i }}][auxiliar][ayudante]"
                                class="form-control form-control-sm buscador-nombre" autocomplete="off"
                                placeholder="Escribí 2 o más letras del nombre"
                                value="{{ old("asignaciones_maestros.$i.auxiliar.ayudante", $asignacion['auxiliar']['ayudante'] ?? '') }}">
                            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endfor


        {{-- NUESTRA VIDA CRISTIANA --}}
     <div class="d-flex align-items-center mb-2 mt-4">
        <div class="me-2" style="background: #a73229; padding: 6px; border-radius: 4px;">
            <i class="fa-solid fa-book text-white"></i>
        </div>
        <h6 class="m-0 fw-bold text-uppercase" style="color: #a73229;">Nuestra Vida Cristiana</h6>
    </div>
    <hr class="mt-1 mb-3">

    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">Canción intermedia</span>
                <input type="text" name="cancion_medio" class="form-control form-control-sm" placeholder="Ej. 123"
                    value="{{ old('cancion_medio', $programa->cancion_medio ?? '') }}">
            </div>
        </div>
    </div>


      @for ($i = 0; $i < 2; $i++)
        @php
            $vida = $programa->vida_cristiana[$i] ?? [];
        @endphp
        <div class="row g-2 mb-2">
            <div class="col-md-8">
                <label class="form-label">{{ $i + 8 }}. Tema</label>
                <input type="text" name="vida_cristiana[{{ $i }}][titulo]" class="form-control form-control-sm"
                    value="{{ old("vida_cristiana.$i.titulo", $vida['titulo'] ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Disertante</label>
                <div class="position-relative">
                    <input type="text" name="vida_cristiana[{{ $i }}][disertante]" class="form-control form-control-sm buscador-nombre"
                        autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                        value="{{ old("vida_cristiana.$i.disertante", $vida['disertante'] ?? '') }}">
                    <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100"
                        style="z-index:9999; display:none;"></div>
                </div>
            </div>
        </div>
    @endfor


  
        {{-- ESTUDIO --}}
      <div class="row g-2 mb-3">
        <label class="form-label">10. Estudio bíblico de la congregación</label>

        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">Conductor</span>
                <input type="text" name="estudio_conductor" class="form-control form-control-sm buscador-nombre"
                    autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                    value="{{ old('estudio_conductor', $programa->estudio_conductor ?? '') }}">
                <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">Lector</span>
                <input type="text" name="estudio_lector" class="form-control form-control-sm buscador-nombre"
                    autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                    value="{{ old('estudio_lector', $programa->estudio_lector ?? '') }}">
                <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100" style="z-index:9999; display:none;"></div>
            </div>
        </div>
    </div>



        <p class="small text-muted mb-3">• Palabras de conclusión (3 min.)</p>

        {{-- FINAL --}}
     <div class="row g-2 mb-4">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">Canción final</span>
            <input type="text" name="cancion_final" class="form-control form-control-sm"
                placeholder="Ej. 123"
                value="{{ old('cancion_final', $programa->cancion_final ?? '') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">Oración final</span>
            <input type="text" name="oracion_final" class="form-control form-control-sm buscador-nombre"
                autocomplete="off" placeholder="Escribí 2 o más letras del nombre"
                value="{{ old('oracion_final', $programa->oracion_final ?? '') }}">
            <div class="dropdown-sugerencias border rounded bg-white shadow-sm position-absolute w-100"
                style="z-index:9999; display:none;"></div>
        </div>
    </div>
</div>



        <button class="btn btn-primary btn-sm">
            <i class="fa-solid fa-check"></i> Guardar programa semanal
        </button>
    </form>
</div>

<style>
.dropdown-sugerencias {
    position: absolute;
    top: 100%; /* ⬅ Esto lo baja justo debajo del input */
    left: 0;
    right: 0;
    max-height: 200px;
    overflow-y: auto;
    font-size: 0.85rem;
    border: 1px solid #ccc;
    background: #fff;
    z-index: 9999;
    display: none;
    box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.05);

}
.dropdown-sugerencias div:hover,
.dropdown-sugerencias div.activo {
    background-color: #afd5ff; /* azul claro */
    color: #000;
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
