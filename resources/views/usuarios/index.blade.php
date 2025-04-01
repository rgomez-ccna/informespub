@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Bloque de mensajes de succes o error  --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
        <a class="btn btn-primary btn-sm" href="{{ route('usuarios.create') }}"><i class="fas fa-plus"></i> Nuevo </a>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th> <!-- Columna para acciones -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuariosOrdenados as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @switch($usuario->role)
                                    @case('vendedor')
                                        <span class="badge bg-primary">{{ $usuario->role }}</span>
                                        @break
                                    @case('admin')
                                        <span class="badge bg-success">{{ $usuario->role }}</span>
                                        @break
                                    @case('disabled')
                                        <span class="badge bg-secondary">{{ $usuario->role }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-warning">{{ $usuario->role }}</span>
                                @endswitch
                            </td>
                            <td>{{ $usuario->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>

                                <button class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $usuario->id }}')"><i class="fas fa-trash-alt"></i></button>

                            <form id="delete-form-{{ $usuario->id }}" action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if(confirm('¿Estás seguro de que deseas eliminar este Usuario?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
    
@endsection

