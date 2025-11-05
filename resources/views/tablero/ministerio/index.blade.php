@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 850px;">

    {{-- BOTONES SUPERIORES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>
        <button class="btn btn-primary btn-sm" onclick="openFormModal('{{ route('ministerio.create') }}')">
            <i class="fa-solid fa-plus"></i> Agregar salida / Exhibidor
        </button>
    </div>

    <div class=" text-center mb-3">
        <h4 class="titulo">PROGRAMAS - SALIDAS AL MINISTERIO</h4>
    </div>

    <div id="contenedorMinisterio"><!-- CONTENIDO -->
    {{-- ACORDEÓN --}}
    <div class="accordion" id="accordionProgramas">
        @foreach($bloques as $i => $registros)
            @php
                $primeraFecha = $registros->keys()->first();
                $ultimaFecha  = $registros->keys()->last();
                $idUnico = 'bloque_' . $i;
            @endphp

            <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $i }}">
                        <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $i }}"
                            aria-expanded="false" aria-controls="collapse{{ $i }}">
                            Semana del {{ \Carbon\Carbon::parse($primeraFecha)->format('d/m/Y') }}
                            al {{ \Carbon\Carbon::parse($ultimaFecha)->format('d/m/Y') }}
                        </button>
                    </h2>
                    <div id="collapse{{ $i }}" class="accordion-collapse collapse"
                        aria-labelledby="heading{{ $i }}" data-bs-parent="#accordionProgramas">

                    <div class="accordion-body">
                        {{-- CONTENIDO DE LA SEMANA --}}
                        <div id="{{ $idUnico }}">
                            {{-- BANNER INTERNO --}}
                            <div class="banner-programa text-center mb-3">
                                <h4 class="titulo">SALIDAS AL MINISTERIO</h4>
                                <h6 class="subtitulo">
                                    SEMANA DEL {{ \Carbon\Carbon::parse($primeraFecha)->format('d') }}
                                    AL {{ \Carbon\Carbon::parse($ultimaFecha)->format('d') }}
                                    DE {{ strtoupper(\Carbon\Carbon::parse($ultimaFecha)->translatedFormat('F Y')) }}
                                </h6>
                            </div>

                            {{-- TABLA --}}
                            <div class="table-responsive">
                                <table class="tabla-programa text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th>DÍA</th>
                                            <th>FECHA</th>
                                            <th>HORA</th>
                                            <th>CONDUCTOR /<br> Voluntarios de EXHIVIDORES</th>
                                            <th>PUNTO DE ENCUENTRO</th>
                                            <th>TERRITORIO</th>
                                            <th class="no-print text-nowrap" style="width:110px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $colorIndex = 0; @endphp

                                        @foreach($registros as $fecha => $items)
                                            @php
                                                $dia = \Carbon\Carbon::parse($fecha)->translatedFormat('l');
                                                $fechaForm = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                                $rowspan = $items->where('es_fila_info', false)->count();
                                                $rowClass = $colorIndex % 2 === 0 ? 'fila-blanca' : 'fila-violeta';
                                            @endphp

                                            {{-- Fila informativa + normal salida--}}
                                           @foreach($items->values() as $i => $r)
                                                <tr>
                                                    @if($i === 0)
                                                        <td rowspan="{{ count($items) }}" class="{{ $rowClass }}">{{ strtoupper($dia) }}</td>
                                                        <td rowspan="{{ count($items) }}" class="{{ $rowClass }}"><strong>{{ $fechaForm }}</strong></td>
                                                    @endif

                                                    @if($r->es_fila_info)
                                                        <td colspan="4" class="{{ $rowClass }} fw-bold text-center">
                                                                {{ strtoupper($r->territorio) }}
                                                            </td>

                                                    @else
                                                        <td class="{{ $rowClass }}">{{ $r->hora }}</td>
                                                        <td class="{{ $rowClass }}">{{ $r->conductor }}</td>
                                                        <td class="{{ $rowClass }}">{{ $r->punto_encuentro }}</td>
                                                        <td class="{{ $rowClass }}">{{ $r->territorio }}</td>
                                                    @endif

                                                    <td class="no-print {{ $rowClass }}">
                                                        <button class="btn btn-sm btn-warning"
                                                                onclick="openFormModal('{{ route('ministerio.edit',$r) }}')">
                                                            <i class="fa-solid fa-edit"></i>
                                                        </button>

                                                        <form action="{{ route('ministerio.destroy', $r) }}" method="POST" class="d-inline ajaxDel">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endforeach


                                            @php $colorIndex++; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        

                        </div>
                        <div class="text-end no-print mb-2">
                            <button onclick="imprimirBloque('{{ $idUnico }}')" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-print"></i> Imprimir esta semana
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    </div><!-- FIN CONTENEDOR -->
</div>


{{-- MODAL --}}
<div class="modal fade" id="modalForm" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
        <div class="modal-body p-0" id="bodyModalForm"></div>
    </div>
  </div>
</div>

{{-- JS --}}
<script>
function imprimirBloque(id) {
    const original = document.body.innerHTML;
    const contenido = document.getElementById(id).innerHTML;
    document.body.innerHTML = contenido;
    window.print();
    document.body.innerHTML = original;
}

// ID del panel abierto (ej: "collapse3")
let accordionOpen = null;

// guardar qué panel quedó abierto
document.addEventListener('shown.bs.collapse', e => {
    accordionOpen = e.target.id; // ej: collapse2
});

// RECARGA SOLO EL PANEL ABIERTO (sin cerrar/abrir = sin "pestañeo")
async function recargarPanelAbierto(){
    // si nunca abriste un panel, fallback a recarga parcial segura
    if(!accordionOpen){
        return recargarFallback();
    }

    // capto el body actual y congelo altura para evitar salto visual
    const panelActual = document.getElementById(accordionOpen);
    if(!panelActual) return recargarFallback();

    const bodyActual = panelActual.querySelector('.accordion-body');
    if(!bodyActual) return recargarFallback();

    const alto = bodyActual.offsetHeight;
    bodyActual.style.minHeight = alto + 'px'; // congela

    // traigo HTML del index y extraigo SOLO el body del mismo panel
    const res = await fetch("{{ route('ministerio.index') }}", {
        headers: {'X-Requested-With':'XMLHttpRequest'}
    });
    const html = await res.text();
    const dom = new DOMParser().parseFromString(html, 'text/html');
    const panelNuevo = dom.getElementById(accordionOpen);
    const bodyNuevo  = panelNuevo?.querySelector('.accordion-body');

    if(bodyNuevo){
        bodyActual.innerHTML = bodyNuevo.innerHTML; // swap limpio
    }else{
        // si no lo encuentra, fallback suave
        await recargarFallback();
    }

    // libero el lock visual
    bodyActual.style.minHeight = '';
}

// Fallback: reinyecta sólo #contenedorMinisterio (lo mínimo)
async function recargarFallback(){
    const res = await fetch("{{ route('ministerio.index') }}", {
        headers: {'X-Requested-With':'XMLHttpRequest'}
    });
    const html = await res.text();
    const dom = new DOMParser().parseFromString(html,'text/html');
    const nuevo = dom.querySelector('#contenedorMinisterio');
    if(nuevo){
        document.querySelector('#contenedorMinisterio').innerHTML = nuevo.innerHTML;
        // si había panel abierto, volvemos a mostrarlo, pero SIN toggle (evita flash)
        if(accordionOpen){
            const el = document.getElementById(accordionOpen);
            if(el && !el.classList.contains('show')){
                el.classList.add('show');
            }
        }
    }
}


// --- AUTOCOMPLETAR GLOBAL ---
window.initBuscadorNombres = function(){
    const root = document.getElementById('bodyModalForm');
    if(!root) return;

    root.querySelectorAll('.buscador-nombre').forEach(input => {
        const contenedor = input.parentElement.querySelector('.dropdown-sugerencias');
        let indice = -1;

       input.addEventListener('input', () => {
    let raw = input.value;
    let partes = raw.split('/');
    let ultima = partes[partes.length-1].trim(); // <-- BUSCAR SOBRE ESTA PARTE SOLAMENTE

    if (ultima.length < 2) {
        contenedor.style.display = 'none';
        return;
    }

    fetch(`/buscar-publicadores?q=${encodeURIComponent(ultima)}`)
        .then(r => r.json())
        .then(data => {
            contenedor.innerHTML = '';
            indice = -1;
            if (!data.length) { contenedor.style.display = 'none'; return; }

            data.forEach((nombre, idx) => {
                const opcion = document.createElement('div');
                opcion.textContent = nombre;
                opcion.classList.add('dropdown-item');

                opcion.onclick = () => {
                    partes[partes.length-1] = " "+nombre; // reemplaza SOLO la última parte
                    input.value = partes.join(' / ').trim();
                    contenedor.style.display = 'none';
                };

                contenedor.appendChild(opcion);
            });

            contenedor.style.display = 'block';
        });
});


        input.addEventListener('keydown', (e) => {
            const opciones = contenedor.querySelectorAll('div');
            if (!opciones.length) return;
            if (e.key === 'ArrowDown') { e.preventDefault(); indice = (indice + 1) % opciones.length; }
            else if (e.key === 'ArrowUp') { e.preventDefault(); indice = (indice - 1 + opciones.length) % opciones.length; }
            else if (e.key === 'Enter') { e.preventDefault(); if (indice >= 0) { input.value = opciones[indice].textContent; contenedor.style.display = 'none'; indice = -1; } }
            opciones.forEach((op, i) => op.classList.toggle('activo', i === indice));
        });

        document.addEventListener('click', e => {
            if (!contenedor.contains(e.target) && e.target !== input) {
                contenedor.style.display = 'none';
            }
        });
    });
};

// MODAL
async function openFormModal(url){
    const r = await fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}});
    const h = await r.text();
    document.querySelector('#bodyModalForm').innerHTML = h;

    window.initBuscadorNombres(); // <- ahora sí existe

    new bootstrap.Modal(document.getElementById('modalForm')).show();
}


// SUBMIT AJAX (crear/editar/eliminar)
// SUBMIT AJAX (crear/editar/eliminar)
document.addEventListener('submit', async(e)=>{
    e.preventDefault();

    let form = e.target;
    let fd = new FormData(form);

    let r = await fetch(form.action,{
        method: form.method,
        body: fd,
        headers:{
            'X-Requested-With':'XMLHttpRequest',
            'Accept':'application/json'
        }
    });

    let j = { ok:false };
    try{ j = await r.json(); }catch(_){}

    if(j.ok){

        if(j.full_reload){
            await recargarFallback();   // <--- acá hay full
        }else{
            await recargarPanelAbierto(); // <--- acá suave
        }

        const modal = document.getElementById('modalForm');
        if (modal) bootstrap.Modal.getInstance(modal)?.hide();
    }
});


</script>


@endsection
