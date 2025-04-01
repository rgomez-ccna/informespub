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
    /* Estilo para el overlay de carga */
    #loadingOverlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Color de fondo inicial azul */
        z-index: 1050;
        overflow: hidden; /* Para ocultar el desbordamiento de la línea */
    }

    /* Estilo para la línea animada */
    #loadingOverlay::after {
        content: "";
        position: absolute;
        width: 100%;
        height: 2px;
        top: 50%; /* Posicionamos la línea en el centro vertical */
        left: 0;
        background: linear-gradient(to right, rgb(255, 255, 255), rgb(45, 105, 255)); /* Gradiente de transición entre blanco y azul */
        animation: pasa 1s linear infinite; /* Animación de la línea con duración de 3 segundos */
    }

    /* Definición de la animación */
    @keyframes pasa {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }
</style>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light" style="background: linear-gradient(135deg, #1a1a1a 0%, #878787 100%);">
            <div class="container">
                <a class="navbar-brand text-white" href="{{ url('/') }}">
                    {{ config('app.name', 'Sis360') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
        
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @auth
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('pub/listado') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('pub.listado') }}">
                                Publicadores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('pub') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('pub.index') }}">
                                 Agregar Informes
                            </a>
                        </li>
                        @if(Auth::user()->role === 'admin')
                            {{-- <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('usuarios') ? 'bg-light bg-opacity-25' : '' }} rounded-4" href="{{ route('usuarios.index') }}">
                                     Usuarios
                                </a>
                            </li> --}}
                        @endif
                    </ul>
                                       
                    @endauth
        
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link text-white rounded-2" href="{{ route('login') }}">{{ __('Inicio') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link text-white rounded-2" href="{{ route('register') }}">{{ __('Registro') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle text-white rounded-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
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
