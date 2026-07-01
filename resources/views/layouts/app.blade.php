<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <!--  jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- CSS datatables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
<!-- JS datatables -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>

{{-- font awesome --}}
<!-- Enlace a Font Awesome versión gratuita (Free) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">


{{-- Estilos personalizados --}}
<style>
    .btn-success, .bg-success, .alert-success, .badge.bg-success {
        background-color: #35dda2 !important;
        color: #ffffff !important;
        border-color: #35dda2 !important;
    }
    
    .btn-danger, .bg-danger, .alert-danger, .badge.bg-danger {
        background-color: #ff4d4f !important;  /* Color rojo para 'danger' */
        color: #ffffff !important;             /* Texto en blanco para contraste */
        border-color: #ff4d4f !important;      /* Borde del mismo color */
    }
</style>


<style>
/* ======= ESTILO EXCLUSIVO PARA TABLERO ======= */

/* -------- ENCABEZADO DE CADA PROGRAMA -------- */
.banner-programa {
    border: 3px solid #5c498b;
    padding: 10px 6px;
    text-align: center;
    border-radius: 6px;
    margin-bottom: 10px;
}
.banner-programa .titulo {
    margin: 0;
    font-weight: 700;
    color: #41345a;
    letter-spacing: 1px;
}
.banner-programa .subtitulo {
    margin: 0;
    color: #3d3154;
    font-weight: 600;
}

/* -------- TABLA DEL PROGRAMA -------- */
.tabla-programa {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.tabla-programa thead th {
    background: #6b5b95;
    color: #fff;
    border: 1px solid #3e3351;
    font-weight: 600;
    font-size: 12px;
}
.tabla-programa tbody td {
    border: 1px solid #d8d1e6;
    color: #333;
    padding: 2px 3px;
}
.tabla-programa tbody tr:nth-child(even) {
    background: #f5f0fb !important;
}
.tabla-programa tbody tr:nth-child(odd) {
    background: #ffffff;
}

/* Para SALIDAS a ministerio FILAS */
.fila-violeta {
    background-color: #f5f0fb !important; /* violeta claro */
}
.fila-blanca {
    background-color: #ffffff !important; /* blanco */
}


/* -------- IMPRESIÓN -------- */
@media print {
    nav.navbar,
    .no-print {
        display: none !important;
    }
    * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
    table {
        font-size: 11px !important;
    }
    body {
        margin: 0;
    }
}
</style>


<style>
.role-superadmin {
    background-color: #6f42c1 !important;
    color: #fff !important;
}

.role-secretario {
    background-color: #0d6efd !important;
    color: #fff !important;
}

.role-colaborador {
    background-color: #20c997 !important;
    color: #fff !important;
}

.role-tablero {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.role-disabled {
    background-color: #dc3545 !important;
    color: #fff !important;
}

.congregacion-dot {
    width: 9px;
    height: 9px;
    display: inline-block;
    border-radius: 50%;
    background: #ffffff;
    opacity: .9;
    box-shadow: 0 0 0 4px rgba(255, 255, 255, .12);
}

.navbar .container {
    max-width: 1320px;
}

.congregacion-brand {
    max-width: 190px;
    min-width: 0;
    font-size: .95rem;
    flex-shrink: 1;
}

.congregacion-brand-text {
    display: inline-block;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.main-navbar {
    gap: .35rem !important;
}

.main-navbar .nav-link {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    white-space: nowrap;
    font-size: .9rem;
    padding: .45rem .55rem;
}

.main-navbar .nav-link i {
    font-size: .9rem;
}

@media (min-width: 768px) {
    .navbar-collapse {
        min-width: 0;
    }

    .main-navbar {
        flex-wrap: nowrap;
        min-width: 0;
    }
}

@media (min-width: 768px) and (max-width: 1199.98px) {
    .congregacion-brand {
        max-width: 130px;
        font-size: .88rem;
    }

    .main-navbar .nav-link {
        font-size: .84rem;
        padding-left: .42rem;
        padding-right: .42rem;
    }
}

@media (max-width: 767.98px) {
    .congregacion-brand {
        max-width: calc(100vw - 92px);
    }

    .main-navbar .nav-link {
        width: 100%;
    }
}

</style>

</head>
<body>
    <div id="app">
       <nav class="navbar navbar-expand-md navbar-light" style="background: linear-gradient(135deg, #1a1a1a 0%, #878787 100%);">
            <div class="container">
              
             @if(Auth::check() && Auth::user()->congregacion)
                <span class="navbar-brand text-white d-flex align-items-center gap-2 mb-0 congregacion-brand" title="{{ Auth::user()->congregacion->nombre }}">
                    <span class="congregacion-dot"></span>
                    <span class="congregacion-brand-text">{{ Auth::user()->congregacion->nombre }}</span>
                </span>
            @elseif(session('free_access') && session('free_congregacion_nombre'))
                <span class="navbar-brand text-white d-flex align-items-center gap-2 mb-0 congregacion-brand" title="{{ session('free_congregacion_nombre') }}">
                    <span class="congregacion-dot"></span>
                    <span class="congregacion-brand-text">{{ session('free_congregacion_nombre') }}</span>
                </span>
            @endif

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    @auth
                        @php
                            $rol = Auth::user()->role;

                           $puedeGestionarDatos = in_array($rol, ['secretario', 'colaborador']);
                            $puedeVerTablero = in_array($rol, ['secretario', 'colaborador', 'tablero']);
                            $puedeGestionarUsuarios = in_array($rol, ['secretario', 'superadmin']);
                            $puedeGestionarCongregaciones = $rol === 'superadmin';
                            $puedeEliminarDatosCongregacion = $rol === 'secretario';
                        @endphp

                        <ul class="navbar-nav me-auto main-navbar">

                            @if($puedeGestionarDatos)
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('pub') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('pub.index') }}">
                                        <i class="fa-solid fa-file-circle-plus"></i> Informes
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('pub/listado') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('pub.listado') }}">
                                        <i class="fa-solid fa-users"></i> Publicadores
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('asistencia') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('asist.index') }}">
                                        <i class="fa-solid fa-calendar-check"></i> Asistencia
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('reg/enviar-informes') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('reg.enviar-informes') }}">
                                        <i class="fa-solid fa-paper-plane"></i> Enviar
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('vida-ministerio*') ? 'bg-light bg-opacity-25' : '' }} rounded-4"
                                    href="{{ route('vida-ministerio.index') }}">
                                        <i class="fa-solid fa-list-check"></i> V. Ministerio
                                    </a>
                                </li>
                            @endif

                            @if($puedeVerTablero)
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('tablero') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('tablero.index') }}">
                                        <i class="fa-solid fa-table-columns"></i> Tablero
                                    </a>
                                </li>
                            @endif

                            @if($puedeGestionarUsuarios)
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('usuarios') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('usuarios.index') }}">
                                        <i class="fa-solid fa-user-gear"></i> Usuarios
                                    </a>
                                </li>
                            @endif

                            @if($puedeEliminarDatosCongregacion)
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('mi-congregacion/datos') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('congregacion.datos') }}">
                                        <i class="fa-solid fa-database"></i> Datos
                                    </a>
                                </li>
                            @endif

                            @if($puedeGestionarCongregaciones)
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('congregaciones*') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('congregaciones.index') }}">
                                        <i class="fa-solid fa-building"></i> Congregaciones
                                    </a>
                                </li>
                            @endif

                        </ul>
                    @endauth

                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link text-white rounded-2" href="{{ route('login') }}">
                                        <i class="fa-solid fa-right-from-bracket"></i> {{ __('Iniciar sesión') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown"
                                class="nav-link dropdown-toggle text-white rounded-2 d-flex align-items-center gap-2"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                    <i class="fa-solid fa-circle-user fa-lg"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>

                </div>
            </div>
        </nav>
        
        

        <main class="py-4">
            @yield('content')
        </main>
    </div>
<script>
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert-success, .alert-danger").fadeOut("slow");
        }, 3000); // 5000 milisegundos = 5 segundos
    });
</script>

<script>
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('loadingOverlay');
    
        // Función para mostrar el overlay
        function showOverlay() {
            overlay.style.display = 'block';
        }
    
        // Función para ocultar el overlay
        function hideOverlay() {
            overlay.style.display = 'none';
        }
    
        // Ocultar overlay al finalizar la carga de la página
        window.addEventListener('load', hideOverlay);
    
        // Asegurarse de que el overlay se oculta al navegar (ir hacia atrás/adelante)
        window.addEventListener('popstate', hideOverlay);
        window.addEventListener('pageshow', hideOverlay); // Este evento se dispara al navegar a la caché
    
        // Manejar clics en enlaces para mostrar el overlay
        document.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Verificar que no sea un enlace de anclaje o un desplegable
                if (this.getAttribute('href') !== '#' && !this.classList.contains('dropdown-toggle') && this.target !== '_blank') {
                    showOverlay();
                }
            });
        });
    
        // Mostrar overlay al enviar formularios
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', showOverlay);
        });
    
        // Truco para manejar recargas de página y navegación directa (opcional)
        // Utiliza sessionStorage para saber cuándo ocultar el overlay después de una recarga
        if (sessionStorage.getItem('overlayVisible') === 'true') {
            showOverlay();
        }
        sessionStorage.setItem('overlayVisible', 'true');
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('overlayVisible', 'false');
        });
    });
    
    </script>
    
{{-- <!-- Overlay de Carga -->
<div id="loadingOverlay"></div> --}}

  
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function () {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Procesando...';  // Cambiar texto si lo deseas
            }
        });
    });
});

</script>

{{-- EVITAR EL ERROR 419 PAGINA EXPIRADA --}}
@if (!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('welcome') && !request()->routeIs('contacto.enviar'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Detener el envío del formulario hasta que se verifique la sesión

                // Verificar la sesión antes de proceder
                fetch('{{ route("check.session") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        window.location.href = '{{ route("login") }}'; // Redirigir al login si la sesión ha expirado
                    } else {
                        form.submit(); // Si la sesión está activa, proceder con el envío
                    }
                })
                .catch(error => {
                    console.error('Error verificando la sesión:', error);
                    form.submit(); // En caso de error, proceder con el envío del formulario
                });
            });
        });
    });
    </script>
@endif



</body>
</html>
