@extends('layouts.app')

@section('content')
@php
    $roles = [
        'superadmin' => ['label' => 'Superadmin', 'desc' => 'Administra todas las congregaciones.'],
        'secretario' => ['label' => 'Secretario', 'desc' => 'Acceso completo. Administra usuarios y puede eliminar toda la base de datos de la congregacion.'],
        'colaborador' => ['label' => 'Colaborador', 'desc' => 'Mismo trabajo operativo que secretario, sin administracion de usuarios ni eliminacion total de datos.'],
        'tablero' => ['label' => 'Tablero', 'desc' => 'Gestiona el tablero. En Vida y Ministerio solo ve e imprime.'],
        'disabled' => ['label' => 'Desactivado', 'desc' => 'Usuario suspendido. No puede ingresar hasta ser habilitado.'],
    ];
@endphp

<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">Usuarios</h3>
            <p class="text-muted mb-0">
                Alta, baja y permisos de acceso de esta congregacion.
            </p>
        </div>

        <a class="btn btn-primary btn-sm" href="{{ route('usuarios.create') }}">
            <i class="fas fa-plus"></i> Nuevo usuario
        </a>
    </div>

    <div class="alert alert-info border small mb-3">
        <strong>Importante:</strong> solo <strong class="text-primary">Secretario</strong> administra usuarios y puede eliminar definitivamente todos los datos de la congregacion.
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Congregacion</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($usuariosOrdenados as $usuario)
                        @php
                            $rol = $roles[$usuario->role] ?? ['label' => $usuario->role, 'desc' => ''];
                        @endphp

                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $usuario->name }}</div>
                                <div class="text-muted small">Creado {{ $usuario->created_at?->format('d/m/Y') }}</div>
                            </td>

                            <td>{{ $usuario->email }}</td>

                            <td style="min-width: 230px;">
                                <span class="badge role-{{ $usuario->role }}">{{ $rol['label'] }}</span>
                                <div class="text-muted small mt-1">{{ $rol['desc'] }}</div>
                            </td>

                            <td>
                                {{ $usuario->congregacion?->nombre ?? 'No asignada' }}
                            </td>

                            <td class="text-end text-nowrap">
                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>

                                <button class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $usuario->id }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                                <form id="delete-form-{{ $usuario->id }}" action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No hay usuarios cargados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('¿Eliminar este usuario?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endsection
