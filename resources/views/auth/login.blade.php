@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center mt-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-lg rounded-0 py-4">
            <div class="card-header text-center border-0 bg-white">
                <h4 class="fw-bold mb-0 py-2">Iniciar sesión</h4>
            </div>

            <div class="card-body px-4 py-4">
                @if(session('message'))
                    <div class="alert alert-danger text-center mb-3 py-2">
                        {{ session('message') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Usuario</label>
                        <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="invalid-feedback d-block small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror" name="password" required>
                            <button type="button" class="btn btn-outline-secondary " onclick="togglePassword()" tabindex="-1">
                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark ">Iniciar sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endsection
