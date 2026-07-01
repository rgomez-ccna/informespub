@extends('layouts.app')

@section('content')
<div class="container">

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Ups!</strong> Problemas con tu carga:<br><br>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-5">
        <h4>{{ isset($usuario) ? 'Editar Usuario' : 'Crear Usuario' }}</h4>

        <form action="{{ isset($usuario) ? route('usuarios.update', $usuario->id) : route('usuarios.store') }}" method="post" autocomplete="off">
            @csrf

            @if(isset($usuario))
                @method('PUT')
            @endif

            @if(auth()->user()->role === 'superadmin')
                <div class="mb-3">
                    <label for="congregacion_id" class="form-label">Congregación</label>
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
                        <option value="superadmin" {{ old('role', $usuario->role ?? '') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    @endif

                    <option value="secretario" {{ old('role', $usuario->role ?? '') == 'secretario' ? 'selected' : '' }}>Secretario - administrador principal</option>
                    <option value="colaborador" {{ old('role', $usuario->role ?? '') == 'colaborador' ? 'selected' : '' }}>Colaborador - carga y edicion de datos</option>
                    <option value="tablero" {{ old('role', $usuario->role ?? '') == 'tablero' ? 'selected' : '' }}>Tablero - solo ver e imprimir</option>
                    <option value="disabled" {{ old('role', $usuario->role ?? '') == 'disabled' ? 'selected' : '' }}>Desactivado</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    {{ isset($usuario) ? 'Nueva Contraseña (opcional)' : 'Contraseña' }}
                </label>

                <div class="input-group">
                    <input type="password"
                           class="form-control form-control-sm"
                           id="password"
                           name="password"
                           {{ isset($usuario) ? '' : 'required' }}
                           autocomplete="new-password">

                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('password', 'passwordIcon')">
                        <i class="fas fa-eye-slash" id="passwordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>

                <div class="input-group">
                    <input type="password"
                           class="form-control form-control-sm"
                           id="password_confirmation"
                           name="password_confirmation"
                           {{ isset($usuario) ? '' : 'required' }}
                           autocomplete="new-password">

                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('password_confirmation', 'confirmPasswordIcon')">
                        <i class="fas fa-eye-slash" id="confirmPasswordIcon"></i>
                    </button>
                </div>
            </div>

            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-sm mt-2">
                <i class="fa-solid fa-arrow-left"></i> Atrás
            </a>

            <button type="submit" class="btn btn-primary btn-sm mt-2">
                <i class="fas fa-check"></i> {{ isset($usuario) ? 'Actualizar' : 'Crear' }}
            </button>
        </form>
    </div>
</div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        passwordInput.type = "password";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const roleSelector = document.querySelector('.role-selector');

    if (!roleSelector) return;

    function updateRoleColor() {
        roleSelector.className = 'form-select form-select-sm role-selector';

        switch(roleSelector.value) {
            case 'superadmin':
                roleSelector.classList.add('role-superadmin');
                break;
            case 'secretario':
                roleSelector.classList.add('role-secretario');
                break;
            case 'colaborador':
                roleSelector.classList.add('role-colaborador');
                break;
            case 'tablero':
                roleSelector.classList.add('role-tablero');
                break;
            case 'disabled':
                roleSelector.classList.add('role-disabled');
                break;
        }
    }

    roleSelector.addEventListener('change', updateRoleColor);
    updateRoleColor();
});
</script>
@endsection
