@extends('layouts.app')

@section('content')
<div class="container">

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<nav class="navbar navbar-light bg-light mb-3">
    <div class="container-fluid">
        <a href="{{ route('pub.create') }}" class="btn btn-primary btn-sm text-white">Crear Nuevo Publicador</a>
        <form class="d-flex" action="{{ url('/pub') }}" method="GET">
            <input name="nombre" class="form-control form-control-sm me-2" type="search" placeholder="Buscar Publicador">
            <button class="btn btn-outline-success btn-sm" type="submit">Buscar</button>
        </form>
    </div>
</nav>

<div class="accordion" id="accordionExample">

    @foreach ($publicadors->groupBy('grupo') as $grupo => $pubsGrupo)
        <div class="accordion-item">
            <div class="accordion-header" id="heading{{ $grupo }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $grupo }}">
                    <b>GRUPO - {{ $grupo }}</b>
                </button>
            </div>
            <div id="collapse{{ $grupo }}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">

                    <table class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Publicador</th>
                                <th>Informe</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pubsGrupo as $pub)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $pub->nombre }}
                                        @if($pub->rol)
                                            <small class="text-muted">({{ $pub->rol }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('reg.s21', $pub->id) }}" class="btn btn-primary btn-sm">Agregar Informe</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('pub.edit', $pub->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                        <form action="{{ route('pub.destroy', $pub->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar al publicador {{ $pub->nombre }}?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">Total Precursores: {{ $pubsGrupo->where('precursor', true)->count() }}</td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    @endforeach

</div>
</div>
@endsection
