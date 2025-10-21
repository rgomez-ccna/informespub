@extends('layouts.app')

@section('content')
<div class="container">

@if (session('success'))
    <div class="alert alert-success">
        <p>{{ session('success') }}</p>
    </div>
@endif

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <a href="{{ route('pub.index') }}" class="text-white btn bg-secondary bg-gradient float-right">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('reg.create', $publicador->id) }}" class="text-white btn bg-primary bg-gradient float-right mx-2">
                    <i class="fas fa-plus"></i> Agregar Informe
                </a>
                <a href="{{ route('pub.listado') }}" class="text-white btn bg-dark bg-gradient float-right mx-2">
                    <i class="fas fa-id-card"></i> Ver Tarjetas [ S-21 ]
                </a>
            </div>

            <div class="card-body table-responsive">

                @php
                    $registrosAgrupados = $registros->groupBy('a_servicio')->sortKeysDesc();
                @endphp

                @foreach ($registrosAgrupados as $anio => $registrosPorAnio)
                    <h5 class="text-center">Año de Servicio: {{ $anio }}</h5>
                    <table class="table table-striped table-bordered mb-4 table-sm">
                        <thead>
                            <tr>
                                <th>Publicador</th>
                                <th>Mes</th>
                                <th>Actividad</th>
                                <th>Horas</th>
                                <th>Cursos</th>
                                <th>Notas</th>
                                <th>Aux</th>
                                <th>Opciones</th>
                                <th>Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrosPorAnio as $registro)
                                <tr>
                                    <td>{{ $publicador->nombre }}</td>
                                    <td>{{ $registro->mes }}</td>
                                    <td class="text-center">
                                     

                                        @if($registro->aux == '(Auxiliar)')
                                            {{-- Auxiliar, sin ícono --}}
                                        @elseif(!is_null($registro->actividad))
                                            {!! $registro->actividad ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!}
                                        @else
                                            {{-- Precursor o caso sin actividad → vacío --}}
                                        @endif

                                    </td>
                                    
                                    <td>{{ $registro->horas }}</td>
                                    <td>{{ $registro->cursos }}</td>
                                    <td>{{ $registro->notas }}</td>
                                    <td>{{ $registro->aux }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('reg.edit', $registro->id) }}" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-edit"></i> Editar FILA
                                        </a>
                                        
                                        <form action="{{ route('reg.destroy', $registro->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta FILA?')">
                                                <i class="fa fa-trash"></i> Eliminar FILA
                                            </button>
                                        </form>
                                    </td>
                                    
                                    <td>{{ $registro->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach

            </div>
        </div>
    </div>
</div>
</div>
@endsection
