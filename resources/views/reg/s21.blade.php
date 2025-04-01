@extends('layouts.app')

@section('content')
<div class="container">

@if ($message = Session::get('success'))
    <div class="alert alert-success"><p>{{ $message }}</p></div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div>
            <a href="{{ route('pub.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
            {{-- <a href="{{ route('visita.resumen') }}" class="btn btn-dark btn-sm"><i class="fas fa-id-card"></i> Ver Tarjetas [ S-21 ]</a> --}}
            <a href="{{ route('pub.listado') }}" class="btn btn-sm btn-primary">Ver Publicadores por Grupo</a>

        </div>
        <a href="{{ route('reg.create', $publicador->id) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Agregar Informe
        </a>
    </div>

    <div class="card-body table-responsive">

        @php
            $registrosAgrupados = $registros->groupBy('a_servicio')->sortKeysDesc();
        @endphp

        @foreach ($registrosAgrupados as $anio => $registrosPorAnio)
        <h4 class="text-center">Año de Servicio: {{ $anio }}</h4>
        <table class="table table-striped table-bordered mb-4 table-sm">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Actividad</th>
                    <th>Horas</th>
                    <th>Cursos</th>
                    <th>Notas</th>
                    <th>Aux</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registrosPorAnio as $registro)
                <tr>
                    <td>{{ $registro->mes }}</td>
                    <td>{{ $registro->actividad ? 'Predicó' : 'No' }}</td>
                    <td>{{ $registro->horas }}</td>
                    <td>{{ $registro->cursos }}</td>
                    <td>{{ $registro->notas }}</td>
                    <td>{{ $registro->aux }}</td>
                    <td>
                        <a href="{{ route('reg.edit', $registro->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('reg.destroy', $registro->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar registro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endforeach

    </div>
</div>
</div>
@endsection
