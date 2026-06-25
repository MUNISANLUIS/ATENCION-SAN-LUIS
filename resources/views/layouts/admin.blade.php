<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Contenido principal --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header --}}
            <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">@yield('header', 'Dashboard')</h1>
                        <p class="text-sm text-gray-500 mt-1">@yield('subtitle', 'Bienvenido al panel de administración')</p>
                    </div>

                    <div class="flex items-center space-x-4">
                        {{-- Botón de Control de Sonido --}}
                        <button
                            id="soundToggleBtn"
                            class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition group"
                            title="Control de sonido">
                            {{-- Icono de sonido activado --}}
                            <svg id="soundOnIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z">
                                </path>
                            </svg>
                            {{-- Icono de sonido desactivado --}}
                            <svg id="soundOffIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2">
                                </path>
                            </svg>
                            {{-- Indicador de estado --}}
                            <span id="soundIndicator" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        {{-- Notificaciones --}}
                        <button
                            class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        {{-- Usuario --}}
                        <div class="flex items-center space-x-3 border-l pl-4">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'Admin' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ Auth::user()->tipoUsuarioRol->nombre ?? 'Sin rol asignado' }}
                                </p>
                            </div>
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- ✅ ALERTA GLOBAL DE RESERVAS PRÓXIMAS --}}
            <livewire:global-reserva-alert />

            {{-- Contenido con scroll --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="bg-white border-t border-gray-200 px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <p>&copy; {{ date('Y') }} Tickets MDSL. Todos los derechos reservados.</p>
                    <p>Versión 1.0.0</p>
                </div>
            </footer>
        </div>
    </div>

    @livewireScripts

    {{-- Script de Control de Sonido --}}
    <script>
        // ── Sonidos ──────────────────────────────────────────────────────────
        const notificationSound   = new Audio('/sounds/notification.mp3');          // ticket normal
        const notificationSistema = new Audio('/sounds/notificacion_sistemas.mp3'); // ticket de sistema
        notificationSound.volume   = 0.7;
        notificationSistema.volume = 0.9;

        // Categoría que dispara el sonido crítico de sistemas
        const ID_TIPO_SISTEMA = 14;

        // ── Estado ───────────────────────────────────────────────────────────
        let soundEnabled = false;

        // ── Elementos del DOM ────────────────────────────────────────────────
        const soundToggleBtn = document.getElementById('soundToggleBtn');
        const soundOnIcon    = document.getElementById('soundOnIcon');
        const soundOffIcon   = document.getElementById('soundOffIcon');
        const soundIndicator = document.getElementById('soundIndicator');

        // ── UI del botón ─────────────────────────────────────────────────────
        function updateSoundUI() {
            if (soundEnabled) {
                soundOnIcon.classList.remove('hidden');
                soundOffIcon.classList.add('hidden');
                soundIndicator.classList.remove('bg-red-500');
                soundIndicator.classList.add('bg-green-500');
                soundToggleBtn.title = 'Sonido activado';
            } else {
                soundOnIcon.classList.add('hidden');
                soundOffIcon.classList.remove('hidden');
                soundIndicator.classList.remove('bg-green-500');
                soundIndicator.classList.add('bg-red-500');
                soundToggleBtn.title = 'Sonido desactivado';
            }
            localStorage.setItem('notificationSoundEnabled', soundEnabled);
        }

        function toggleSound() {
            soundEnabled = !soundEnabled;
            updateSoundUI();
            console.log(soundEnabled ? '🔊 Sonido habilitado' : '🔇 Sonido deshabilitado');
        }

        soundToggleBtn.addEventListener('click', toggleSound);

        // ── Inicialización ───────────────────────────────────────────────────
        window.addEventListener('DOMContentLoaded', () => {
            const savedPreference = localStorage.getItem('notificationSoundEnabled');

            if (savedPreference !== null) {
                soundEnabled = savedPreference === 'true';
                updateSoundUI();
            } else {
                setTimeout(() => {
                    if (confirm('¿Deseas habilitar las notificaciones con sonido para nuevos tickets?')) {
                        soundEnabled = true;
                        console.log('✅ Notificaciones sonoras habilitadas');
                    } else {
                        soundEnabled = false;
                        console.log('❌ Notificaciones sonoras deshabilitadas');
                    }
                    updateSoundUI();
                }, 1000);
            }
        });

        // ── Listener del evento Livewire ─────────────────────────────────────
        Livewire.on('play-notification-sound', (data) => {
            console.log("🔔 Evento de notificación recibido", data);

            if (!soundEnabled) {
                console.log("🔇 Sonido desactivado por el usuario");
                return;
            }

            // Livewire 3 envuelve los params en array → data[0]
            const idTipo    = data?.[0]?.id_tipo_incidencia;
            const esSistema = idTipo == ID_TIPO_SISTEMA;

            if (esSistema) {
                console.log("🚨 Reproduciendo sonido de SISTEMA (categoría:", idTipo, ")");
                notificationSistema.currentTime = 0;
                notificationSistema.play().catch(err =>
                    console.error('❌ Error sonido sistema:', err)
                );
            } else {
                console.log("🔊 Reproduciendo sonido normal (categoría:", idTipo, ")");
                notificationSound.currentTime = 0;
                notificationSound.play().catch(err =>
                    console.error('❌ Error sonido normal:', err)
                );
            }
        });
    </script>
</body>

</html>