<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Protegido</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container" style="max-width:420px; margin-top:40px;">

    <div class="card shadow-sm border-0">
        <div class="card-body p-3">

            <h6 class="mb-3 text-center fw-bold">
                <i class="fa-solid fa-shield-halved"></i> Acceso Protegido
            </h6>

            <p class="text-center small text-muted">
                Este link requiere contraseña. Es un acceso temporal privado.
            </p>

            <form action="{{ route('acceso.verify', $token) }}" method="POST">
                @csrf

                <input type="password" name="password" class="form-control form-control-sm mb-2"
                       placeholder="Contraseña" required>

                @error('password')
                <div class="text-danger small">{{ $message }}</div>
                @enderror

                <button class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-lock-open"></i> Ingresar
                </button>
            </form>

        </div>
    </div>

</div>

</body>
</html>

