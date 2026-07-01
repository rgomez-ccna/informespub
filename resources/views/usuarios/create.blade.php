@extends('layouts.app')

@section('content')
@php
    $esEdit = isset($usuario);
    $rolActual = old('role', $usuario->role ?? 'colaborador');

    $rolesInfo = [
        'superadmin' => [
            'label' => 'Superadmin',
            'desc' => 'Administra todas las congregaciones.',
        ],
        'secretario' => [
            'label' => 'Secretario',
            'desc' => '<strong>Acceso completo.</strong> Administra usuarios y puede eliminar toda la base de datos de la congregacion.',
            'alert' => 'alert-warning',
        ],
        'colaborador' => [
            'label' => 'Colaborador',
            'desc' => '<strong>Trabajo operativo completo.</strong> Puede cargar, editar y eliminar datos normales, pero no administra usuarios ni elimina toda la base de datos.',
            'alert' => 'alert-info',
        ],
        'tablero' => [
            'label' => 'Tablero',
            'desc' => '<strong>Tablero.</strong> Puede trabajar con el tablero. En Vida y Ministerio solo ve e imprime.',
            'alert' => 'alert-info',
        ],
        'disabled' => [
            'label' => 'Desactivado',
            'desc' => '<strong>Usuario suspendido.</strong> No puede ingresar hasta que se lo vuelva a habilitar.',
            'alert' => 'alert-secondary',
        ],
    ];
@endphp

<div class="container py-4" style="max-width: 640px;">

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los campos marcados.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h3 class="mb-1 fw-bold text-secondary">{{ $esEdit ? 'Editar usuario' : 'Crear usuario' }}</h3>
            <p class="text-muted mb-0">
                Datos de acceso y rol del usuario.
            </p>
        </div>

        <a href="{{ route('usuarios.index') }}" class="btn btn-light border btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ $esEdit ? route('usuarios.update', $usuario->id) : route('usuarios.store') }}" method="post" autocomplete="off">
                @csrf

                @if($esEdit)
                    @method('PUT')
                @endif

                @if(auth()->user()->role === 'superadmin')
                    <div class="mb-3">
                        <label for="congregacion_id" class="form-label">Congregacion</label>
                        <select class="form-select form-select-sm" id="congregacion_id" name="congregacion_id" required>
                            <option value="">Seleccionar</option>
                            @foreach($congregaciones as $congregacion)
                                <option value="{{ $congregacion->id }}"
                                    {{ old('congregacion_id', $usuario->congregacion_id ?? '') == $congregacion->id ? 'selected' : '' }}>
                                    {{ $congregacion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text"
                           class="form-control form-control-sm"
                           id="name"
                           name="name"
                           value="{{ old('name', $usuario->name ?? '') }}"
                           required
                           autocomplete="off">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                           class="form-control form-control-sm"
                           id="email"
                           name="email"
                           value="{{ old('email', $usuario->email ?? '') }}"
                           required
                           autocomplete="off">
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Rol</label>
                    <select class="form-select form-select-sm role-selector" id="role" name="role" required>
                        @if(auth()->user()->role === 'superadmin')
                            <option value="superadmin" {{ $rolActual === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                        @endif

                        <option value="secretario" {{ $rolActual === 'secretario' ? 'selected' : '' }}>Secretario</option>
                        <option value="colaborador" {{ $rolActual === 'colaborador' ? 'selected' : '' }}>Colaborador</option>
                        <option value="tablero" {{ $rolActual === 'tablero' ? 'selected' : '' }}>Tablero</option>
                        <option value="disabled" {{ $rolActual === 'disabled' ? 'selected' : '' }}>Desactivado</option>
                    </select>

                    <div class="alert alert-info border small mt-2 mb-0 py-2" id="roleHelp"></div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        {{ $esEdit ? 'Nueva contrasena (opcional)' : 'Contrasena' }}
                    </label>

                    <div class="input-group">
                        <input type="password"
                               class="form-control form-control-sm"
                               id="password"
                               name="password"
                               {{ $esEdit ? '' : 'required' }}
                               autocomplete="new-password">

                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('password', 'passwordIcon')">
                            <i class="fas fa-eye-slash" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar contrasena</label>

                    <div class="input-group">
                        <input type="password"
                               class="form-control form-control-sm"
                               id="password_confirmation"
                               name="password_confirmation"
                               {{ $esEdit ? '' : 'required' }}
                               autocomplete="new-password">

                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('password_confirmation', 'confirmPasswordIcon')">
                            <i class="fas fa-eye-slash" id="confirmPasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-light border btn-sm">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-check"></i> {{ $esEdit ? 'Guardar cambios' : 'Crear usuario' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const rolesInfo = @json($rolesInfo);

function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const roleSelector = document.querySelector('.role-selector');
    const roleHelp = document.getElementById('roleHelp');

    if (!roleSelector) return;

    function updateRoleColor() {
        roleSelector.className = 'form-select form-select-sm role-selector';
        roleSelector.classList.add('role-' + roleSelector.value);

        if (roleHelp && rolesInfo[roleSelector.value]) {
            roleHelp.className = 'alert border small mt-2 mb-0 py-2 ' + (rolesInfo[roleSelector.value].alert || 'alert-info');
            roleHelp.innerHTML = rolesInfo[roleSelector.value].desc;
        }
    }

    roleSelector.addEventListener('change', updateRoleColor);
    updateRoleColor();
});
</script>
@endsection
