@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-3 text-secondary"> 
        Nuevo Programa – Vida y Ministerio </h4>
        <a href="{{ route('vidaministerio.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al programa
        </a>
    </div>

    <form action="{{ route('vidaministerio.store') }}" method="POST">@csrf

        {{-- FECHA + LECTURA SEMANAL --}}
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Lectura semanal de la Biblia (ej: Proverbios 12 y 13)</label>
                <input type="text" name="lectura_semanal" class="form-control form-control-sm">
            </div>
        </div>

       {{-- ENCABEZADO --}}
<div class="row g-2 mb-3">
    <div class="col-md-6">
       
        <div class="input-group">
            <span class="input-group-text">Presidente</span>
            <input type="text" name="presidente" class="form-control form-control-sm">
        </div>
    </div>
    <div class="col-md-6">
        
        <div class="input-group">
            <span class="input-group-text">Consejero Sala Auxiliar</span>
            <input type="text" name="consejero_auxiliar" class="form-control form-control-sm">
        </div>
    </div>
</div>

<div class="row g-2 mb-2">
    <div class="col-md-6">

        <div class="input-group">
            <span class="input-group-text">Canción de inicio</span>
            <input type="text" name="cancion_inicio" class="form-control form-control-sm">
        </div>
        <p class="small text-muted mt-1 mb-0">• Palabras de introducción (1 min.)</p>
    </div>
    <div class="col-md-6">
       
        <div class="input-group">
            <span class="input-group-text">Oración de inicio</span>
            <input type="text" name="oracion_inicio" class="form-control form-control-sm">
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
                <input type="text" name="tesoro_titulo" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <label class="form-label">Disertante</label>
                <input type="text" name="tesoro_disertante" class="form-control form-control-sm">
            </div>
        </div>

        <div class="row g-2 mb-2">
            <div class="col-md-5">
                <label class="form-label">2. Busquemos perlas escondidas – Disertante</label>
                <input type="text" name="perlas_disertante" class="form-control form-control-sm">
            </div>
            <div class="col-md-7">
                <label class="form-label">3. Lectura de la Biblia – Estudiantes</label>
                <div class="input-group">
                    <span class="input-group-text">Sala Principal</span>
                    <input type="text" name="lectura_lector_principal" class="form-control form-control-sm">
                    <span class="input-group-text">Sala Auxiliar</span>
                    <input type="text" name="lectura_lector_auxiliar" class="form-control form-control-sm">
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
        <div class="border p-2 mb-2 rounded">
            <small class="text-muted">Asignación {{ $i + 4 }}</small>

            <div class="mb-2">
                <label class="form-label">Título (solo si es discurso)</label>
                <input type="text" name="asignaciones_maestros[{{ $i }}][titulo]" class="form-control form-control-sm">
            </div>

            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label">Auditorio Principal – Estudiante / Ayudante</label>
                    <div class="input-group">
                        <input type="text" name="asignaciones_maestros[{{ $i }}][principal][estudiante]" class="form-control form-control-sm" placeholder="Estudiante">
                        <input type="text" name="asignaciones_maestros[{{ $i }}][principal][ayudante]" class="form-control form-control-sm" placeholder="Ayudante">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sala Auxiliar – Estudiante / Ayudante</label>
                    <div class="input-group">
                        <input type="text" name="asignaciones_maestros[{{ $i }}][auxiliar][estudiante]" class="form-control form-control-sm" placeholder="Estudiante">
                        <input type="text" name="asignaciones_maestros[{{ $i }}][auxiliar][ayudante]" class="form-control form-control-sm" placeholder="Ayudante">
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
            <input type="text" name="cancion_medio" class="form-control form-control-sm">
        </div>
    </div>
</div>


        @for ($i = 0; $i < 2; $i++)
        <div class="row g-2 mb-2">
            <div class="col-md-8">
                <label class="form-label">{{ $i + 8 }}. Tema</label>
                <input type="text" name="vida_cristiana[{{ $i }}][titulo]" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <label class="form-label">Disertante</label>
                <input type="text" name="vida_cristiana[{{ $i }}][disertante]" class="form-control form-control-sm">
            </div>
        </div>
        @endfor

  
        {{-- ESTUDIO --}}
        <div class="row g-2 mb-3">
            <label class="form-label">10. Estudio bíblico de la congregación</label>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">Conductor</span>
                    <input type="text" name="estudio_conductor" class="form-control form-control-sm">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">Lector</span>
                    <input type="text" name="estudio_lector" class="form-control form-control-sm">
                </div>
            </div>
        </div>



        <p class="small text-muted mb-3">• Palabras de conclusión (3 min.)</p>

        {{-- FINAL --}}
      <div class="row g-2 mb-4">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">Canción final</span>
            <input type="text" name="cancion_final" class="form-control form-control-sm">
        </div>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">Oración final</span>
            <input type="text" name="oracion_final" class="form-control form-control-sm">
        </div>
    </div>
</div>


        <button class="btn btn-primary btn-sm">
            <i class="fa-solid fa-check"></i> Guardar programa semanal
        </button>
    </form>
</div>
@endsection
