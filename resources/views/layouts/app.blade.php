<!DOCTYPE html>

<html lang="es">
<!--NavBar de la pagina publica -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Tickets')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo_SGI.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        html, body {
            height: 100%;
            overflow-x: hidden;
        }

        .bg-body {
            background: #f3f4f6;
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Animación suave para el menú móvil */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        
        .mobile-menu.active {
            max-height: 500px;
        }

        /* Contenedor principal que ocupa el espacio restante */
        .main-content-wrapper {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
    </style>
</head>
<body class="bg-body">   
    <!-- Header Minimalista -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Desktop & Mobile Header -->
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="https://www.munisanluis.gob.pe/portal/" target="_blank" class="flex-shrink-0">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="h-16 hover:scale-105 transition-transform">
                </a>

                <!-- Desktop: Centro con icono + título -->
                <div class="hidden lg:flex flex-1 items-center justify-center mx-8">
                    <div class="flex items-center space-x-3">
                        <!-- Icono de la pantalla -->
                        <div class="flex-shrink-0 w-10 h-10 bg-sky-600 rounded-lg shadow-sm flex items-center justify-center">
                            @yield('icon')
                        </div>
                        
                        <!-- Título y descripción -->
                        <div class="text-left">
                            <h1 class="text-lg font-semibold text-gray-800">@yield('page-title')</h1>
                            <p class="text-gray-600 text-sm">@yield('page-description')</p>
                        </div>
                    </div>
                </div>

                <!-- Desktop: Botones de acción -->
                <div class="hidden md:flex items-center space-x-2">
                    @if(!Request::is('crear-ticket'))
                        <a href="{{ route('public.ticket') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg transition font-medium text-sm shadow">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="hidden lg:inline">Crear Ticket</span>
                            <span class="lg:hidden">Crear</span>
                        </a>
                    @endif
                    
                    @if(!Request::is('reservarequipo'))
                        <a href="{{ route('public.reservarequipo') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg transition font-medium text-sm shadow">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="hidden lg:inline">Reservar Equipo</span>
                            <span class="lg:hidden">Reservar</span>
                        </a>
                    @endif
                    
                    @if(!Request::is('seguimiento'))
                        <a href="{{ route('public.ticket-seguimiento') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg transition font-medium text-sm shadow">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="hidden lg:inline">Ver Seguimiento</span>
                            <span class="lg:hidden">Seguimiento</span>
                        </a>
                    @endif
                </div>

                <!-- Mobile: Botón de menú hamburguesa -->
                <button id="mobile-menu-button" class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile: Título debajo del header principal (solo en móvil) -->
            <div class="lg:hidden pb-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-sky-600 rounded-lg shadow-sm flex items-center justify-center">
                        @yield('icon')
                    </div>
                    <div>
                        <h1 class="text-base font-semibold text-gray-800">@yield('page-title')</h1>
                        <p class="text-gray-600 text-xs">@yield('page-description')</p>
                    </div>
                </div>
            </div>

            <!-- Mobile: Menú desplegable -->
            <div id="mobile-menu" class="mobile-menu md:hidden border-t border-gray-100">
                <div class="py-3 space-y-1">
                    @if(!Request::is('crear-ticket'))
                        <a href="{{ route('public.ticket') }}" class="flex items-center px-4 py-3 text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition-colors font-medium">
                            <svg class="w-5 h-5 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Crear Ticket
                        </a>
                    @endif
                    
                    @if(!Request::is('reservarequipo'))
                        <a href="{{ route('public.reservarequipo') }}" class="flex items-center px-4 py-3 text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition-colors font-medium">
                            <svg class="w-5 h-5 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Reservar Equipo
                        </a>
                    @endif
                    
                    @if(!Request::is('seguimiento'))
                        <a href="{{ route('public.ticket-seguimiento') }}" class="flex items-center px-4 py-3 text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition-colors font-medium">
                            <svg class="w-5 h-5 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Ver Seguimiento
                        </a>
                    @endif

                    
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="main-content-wrapper">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="text-center text-sm text-gray-600">
                © Copyright 2023 | Municipalidad de San Luis. ¡Haremos Historia!
            </div>
        </div>
    </footer>
    
    @livewireScripts
    
    <script>
        // Toggle menú móvil
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('active');
            
            // Cambiar icono del botón
            const icon = this.querySelector('svg path');
            if (menu.classList.contains('active')) {
                this.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            } else {
                this.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
            }
        });
    </script>
</body>
</html>