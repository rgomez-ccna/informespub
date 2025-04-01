@extends('layouts.app')

@section('content')

<style>
.role-vendedor { background-color: lightblue; }
.role-admin { background-color: lightgreen; }
.role-disabled { background-color: lightcoral; }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-5">
            <h4>{{ isset($usuario) ? 'Editar Usuario' : 'Crear Usuario' }}</h4>

            <form action="{{ isset($usuario) ? route('usuarios.update', $usuario->id) : route('usuarios.store') }}" method="post" autocomplete="off">
                @csrf
                @if(isset($usuario))
                    @method('PUT')
                @endif
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control form-control-sm" id="name" name="name" value="{{ old('name', $usuario->name ?? '') }}" required autocomplete="off">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control form-control-sm" id="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" required autocomplete="off">
                </div>

                <div class="mb-3">
                    <select class="form-select form-select-sm role-selector" id="role" name="role">
                        <option value="vendedor" {{ (isset($usuario) && $usuario->role == 'vendedor') ? 'selected' : '' }}>Vendedor</option>
                        <option value="admin" {{ (isset($usuario) && $usuario->role == 'admin') ? 'selected' : '' }}>Admin</option>
                        <option value="disabled" {{ (isset($usuario) && $usuario->role == 'disabled') ? 'selected' : '' }}>Desactivado</option>
                    </select>
                </div>
                
                

                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-sm" id="password" name="password" autocomplete="new-password">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('password', 'passwordIcon')">
                            <i class="fas fa-eye-slash" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('password_confirmation', 'confirmPasswordIcon')">
                            <i class="fas fa-eye-slash" id="confirmPasswordIcon"></i>
                        </button>
                    </div>
                </div>

                @if ($errors->has('password'))
                <div class="alert alert-danger">
                    {{ $errors->first('password') }}
                </div>
               @endif
               <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-sm mt-2">
                <i class="fas fa-reply"></i> Atrás </a>
               <button type="submit" class="btn btn-primary btn-sm mt-2">
                <i class="fas fa-check"></i> {{ isset($usuario) ? 'Actualizar' : 'Crear ' }}
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

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelector = document.querySelector('.role-selector');
    
        function updateRoleColor() {
            roleSelector.className = 'form-select form-select-sm role-selector';
            switch(roleSelector.value) {
                case 'vendedor':
                    roleSelector.classList.add('role-vendedor');
                    break;
                case 'admin':
                    roleSelector.classList.add('role-admin');
                    break;
                case 'disabled':
                    roleSelector.classList.add('role-disabled');
                    break;
            }
        }
    
        roleSelector.addEventListener('change', updateRoleColor);
        updateRoleColor(); // Actualizar al cargar la página
    });
    </script>
    
@endsection

