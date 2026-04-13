@extends('layouts.app')

@section('content')
<div class="container">

@if (session('success'))
    <div class="alert alert-success">
        <p class="mb-0">{{ session('success') }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<nav class="navbar navbar-light bg-light mb-2">
    <div class="container-fluid">
        <a class="text-white btn bg-secondary" href="{{ route('pub.create') }}">
            <i class="fa fa-user-plus"></i> Agregar nuevo PUB
        </a>

        <form class="d-flex" action="{{ url('/pub') }}" method="GET">
            <input name="nombre" class="form-control me-2" type="search" placeholder="Buscar Publicador" value="{{ request('nombre') }}">
            <button class="btn btn-outline-primary" type="submit">Buscar</button>
        </form>
    </div>
</nav>

<style>
    .bg-soft-yellow { background-color: #fffbb9; }
    .bg-soft-red { background-color: #ffe5e5; }
</style>

<div class="accordion" id="accordionExample">
    @foreach ($publicadors->groupBy('grupo') as $grupo => $grupo_publicadores)
        @php
            $grupoId = \Illuminate\Support\Str::slug($grupo ?: 'sin-grupo');
            $publicadores_precursor = $grupo_publicadores->where('precursor', true);
        @endphp

        <div class="accordion-item">
            <div class="accordion-header" id="heading{{ $grupoId }}">
                <button class="card-header alert alert-secondary accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse{{ $grupoId }}">
                    Grupo - <b class="ms-2"> {{ $grupo ?: 'Sin grupo' }} </b>
                </button>
            </div>

            <div id="collapse{{ $grupoId }}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">

                  

                    <table class="table table-striped table-bordered mb-1">
                        <thead>
                            <tr>
                                <th style="width:45px;" class="text-center">
                                    <input type="checkbox"
                                           onclick="document.querySelectorAll('.check-grupo-{{ $grupoId }}').forEach(cb => cb.checked = this.checked)">
                                </th>
                                <th>#</th>
                                <th>Publicador</th>
                                <th></th>
                                <th>Informar:</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($grupo_publicadores as $pub)
                                <tr class="{{ $publisherActivityStatuses[$pub->id] == 'activo' ? '' : ($publisherActivityStatuses[$pub->id] == 'irregular' ? 'bg-soft-yellow' : 'bg-soft-red') }}">
                                    <td class="text-center">
                                        <input type="checkbox"
                                               value="{{ $pub->id }}"
                                               class="check-grupo-{{ $grupoId }}">
                                    </td>

                                    <td>{{ $loop->index + 1 }}</td>

                                    @if($pub->precursor)
                                        <td style="background:#cfffed">{{ $pub->nombre }}</td>
                                    @else
                                        <td>{{ $pub->nombre }}</td>
                                    @endif

                                    <td class="p-0">
                                        <p class="text-{{ $lastReportStatuses[$pub->id] }} m-0 d-block text-center" style="width: 100%; height: 100%;">
                                            {{ $lastReportStatuses[$pub->id] == 'success' ? 'Registro cargado' : 'Registro pendiente' }}
                                        </p>
                                    </td>

                                    <td>
                                        <a href="{{ route('reg.s21', ['id_publicador' => $pub->id]) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-id-card"></i> Agregar Registro
                                        </a>
                                    </td>

                                    <td class="text-end">
                                        <a href="{{ route('pub.edit', $pub->id) }}" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-edit"></i> Editar PUB
                                        </a>

                                        <form method="POST" action="{{ route('pub.destroy', $pub->id) }}" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('⚠️ ATENCIÓN:\n\nSe eliminará el publicador {{ $pub->nombre }} y TODOS sus registros.\n\n❌ Esta acción NO se puede deshacer.\n\n¿Deseas continuar?')">
                                                <i class="fa fa-trash"></i> Eliminar PUB
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="6">Precursores : {{ $publicadores_precursor->count() }}</th>
                            </tr>
                        </tfoot>
                    </table>

                   
                        <div class="row g-3 align-items-end mb-5">

                            {{-- RENOMBRAR --}}
                            <div class="col-lg-4">
                                <form action="{{ route('pub.renombrarGrupo') }}" method="POST" class="row g-1 align-items-end m-0">
                                    @csrf
                                    <input type="hidden" name="grupo_actual" value="{{ $grupo }}">

                                    <div class="col-8 p-0 pe-1">
                                        <label class="form-label small mb-0 fw-semibold">
                                        Renombrar grupo
                                        </label>
                                        <input type="text" name="grupo_nuevo" class="form-control form-control-sm" placeholder="Nuevo nombre" required>
                                    </div>

                                    <div class="col-4 p-0">
                                        <button type="submit" class="btn btn-warning btn-sm w-100">
                                            <i class="fa fa-pen me-1"></i>Renombrar
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- PASAR TODO --}}
                            <div class="col-lg-4">
                                <form action="{{ route('pub.fusionarGrupo') }}" method="POST" class="row g-1 align-items-end m-0">
                                    @csrf
                                    <input type="hidden" name="grupo_actual" value="{{ $grupo }}">

                                    <div class="col-8 p-0 pe-1">
                                        <label class="form-label small mb-0 fw-semibold">
                                            Pasar todo el grupo a
                                        </label>
                                        <select name="grupo_destino" class="form-select form-select-sm" required>
                                            <option value="">-- Seleccione grupo --</option>
                                            @foreach ($gruposDisponibles as $grupoItem)
                                                @if ($grupoItem !== $grupo)
                                                    <option value="{{ $grupoItem }}">{{ $grupoItem }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-4 p-0">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm w-100"
                                            onclick="return confirm('Se moverán todos los publicadores. ¿Continuar?')">
                                            <i class="fa fa-right-left me-1"></i>Pasar
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- MOVER SELECCIONADOS --}}
                            <div class="col-lg-4">
                                <form action="{{ route('pub.cambiarGrupoMasivo') }}" method="POST"
                                    id="form-mover-{{ $grupoId }}"
                                    onsubmit="return prepararMoverGrupo('{{ $grupoId }}')"
                                    class="row g-1 align-items-end m-0">
                                    @csrf

                                    <div id="hidden-publicadores-{{ $grupoId }}"></div>

                                    <div class="col-8 p-0 pe-1">
                                        <label class="form-label small mb-0 fw-semibold">
                                            Mover seleccionados a
                                        </label>
                                        <select name="grupo_destino" class="form-select form-select-sm" required>
                                            <option value="">-- Seleccione grupo --</option>
                                            @foreach ($gruposDisponibles as $grupoItem)
                                                @if ($grupoItem !== $grupo)
                                                    <option value="{{ $grupoItem }}">{{ $grupoItem }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-4 p-0">
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="fa fa-share me-1"></i>Mover
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                


                </div>
            </div>
        </div>
    @endforeach
</div>
</div>

<script>
function prepararMoverGrupo(grupoId) {
    const checks = document.querySelectorAll('.check-grupo-' + grupoId + ':checked');
    const container = document.getElementById('hidden-publicadores-' + grupoId);

    container.innerHTML = '';

    if (checks.length === 0) {
        alert('Seleccioná al menos un publicador.');
        return false;
    }

    checks.forEach(check => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'publicadores[]';
        input.value = check.value;
        container.appendChild(input);
    });

    return true;
}
</script>
@endsection