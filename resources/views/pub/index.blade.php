@extends('layouts.app')

@section('content')
<div class="container">

@if (session('success'))
    <div class="alert alert-success">
        <p>{{ session('success') }}</p>
    </div>
@endif

<nav class="navbar navbar-light bg-light mb-2">
    <div class="container-fluid">
        <a class="text-white btn bg-primary " href="{{ route('pub.create') }}">Agregar Nuevo Pub</a>
        <form class="d-flex" action="{{ url('/pub') }}" method="GET">
            <input name="nombre" class="form-control me-2" type="search" placeholder="Buscar Publicador">
            <button class="btn btn-outline-success" type="submit">Buscar</button>
        </form>
    </div>
</nav>

<div class="accordion" id="accordionExample">
    @foreach ($publicadors->groupBy('grupo') as $grupo => $grupo_publicadores)
        <div class="accordion-item">
            <div class="accordion-header" id="heading{{ $grupo }}">
                <button class="card-header alert alert-secondary accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $grupo }}">
                    <b>GRUPO - {{ $grupo }}</b>
                </button>
            </div>

            <div id="collapse{{ $grupo }}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Publicador</th>
                                <th></th>
                                <th>Informar:</th>
                                <th></th>
                            </tr>
                        </thead>
                        <style>
                            .bg-soft-yellow { background-color: #fffbb9; }
                            .bg-soft-red { background-color: #ffe5e5; }
                        </style>
                        <tbody>
                            @foreach ($grupo_publicadores as $pub)
                                <tr class="{{ $publisherActivityStatuses[$pub->id] == 'activo' ? '' : ($publisherActivityStatuses[$pub->id] == 'irregular' ? 'bg-soft-yellow' : 'bg-soft-red') }}">
                                    <td>{{ $loop->index + 1 }}</td>

                                    @if($pub->precursor)
                                        <td style="background:#b6ffe4">{{ $pub->nombre }}</td>
                                    @else
                                        <td>{{ $pub->nombre }}</td>
                                    @endif

                                    @php
                                        $publicadores_precursor = $grupo_publicadores->where('precursor', true);
                                    @endphp

                                    <td class="p-0">
                                        <p class="text-{{ $lastReportStatuses[$pub->id] }} m-0 d-block text-center" style="width: 100%; height: 100%;">
                                            {{ $lastReportStatuses[$pub->id] == 'success' ? 'Informó' : 'No informó' }}
                                        </p>
                                    </td>

                                    <td>
                                        <a href="{{ route('reg.s21', ['id_publicador' => $pub->id]) }}" class="btn btn-primary btn-sm">Agregar Informe</a>
                                    </td>

                                    <td>
                                        <a href="{{ route('pub.edit', $pub->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                        <form method="POST" action="{{ route('pub.destroy', $pub->id) }}" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar al publicador {{ $pub->nombre }}?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Precursores : {{ $publicadores_precursor->count() }}</th>
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
