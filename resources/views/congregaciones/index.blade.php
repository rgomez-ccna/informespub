@extends('layouts.app')

@section('content')
<div class="container">

@if (session('success'))
    <div class="alert alert-success">
        <p>{{ session('success') }}</p>
    </div>
@endif


<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Congregaciones</h5>

    <a href="{{ route('congregaciones.create') }}" class="text-white btn bg-primary bg-gradient">
        <i class="fas fa-plus"></i> Nueva Congregación
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-sm">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Ciudad</th>
                <th>Provincia</th>
                <th class="text-center">Usuarios</th>
                <th class="text-center">Publicadores</th>
                <th class="text-center">Registros</th>
                <th class="text-center">Estado</th>
                <th class="text-end">Opciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($congregaciones as $congregacion)
                <tr>
                    <td>{{ $congregacion->nombre }}</td>
                    <td>{{ $congregacion->ciudad }}</td>
                    <td>{{ $congregacion->provincia }}</td>
                    <td class="text-center">{{ $congregacion->users_count }}</td>
                    <td class="text-center">{{ $congregacion->publicadors_count }}</td>
                    <td class="text-center">{{ $congregacion->registros_count }}</td>
                    <td class="text-center">
                        @if($congregacion->activa)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-secondary">Inactiva</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('congregaciones.edit', $congregacion->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-edit"></i> Editar
                        </a>

                        <form action="{{ route('congregaciones.destroy', $congregacion->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Eliminar esta congregación y todos sus datos relacionados?')">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        No hay congregaciones cargadas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</div>
@endsection