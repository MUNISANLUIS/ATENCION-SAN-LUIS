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

        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- HEADER — fuera de Livewire, nunca se destruye --}}
            <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0" style="">
                <div class="flex items-center justify-between px-6 py-3">
                    <div>
                       <a href="{{ url('/listar-tickets') }}">
    <h1 class="text-lg font-bold text-gray-800">Ir al inicio</h1>
</a>
<p class="text-xs text-gray-400">@yield('subtitle', '')</p>
                    </div>
                    <div class="flex items-center gap-3">

                        {{-- BOTÓN SONIDO — aquí nunca es tocado por Livewire --}}
                        <button id="soundToggleBtn" title="Sonido desactivado"
                            class="flex items-center gap-2 text-xs border px-3 py-2 transition-colors"
                            style="border-color:#f87171; color:#dc2626; background:#fef2f2;">
                            <svg id="soundOnIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                            </svg>
                            <svg id="soundOffIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                            </svg>
                            <span id="soundLabel">SONIDO OFF</span>
                        </button>

                        @auth
                        <div class="flex items-center gap-3 border-l pl-3">
                            <div class="text-right">
                                <p class="text-xs font-medium text-gray-700">{{ Auth::user()->name ?? 'Admin' }}</p>
                                <p class="text-[10px] text-gray-400">{{ Auth::user()->tipoUsuarioRol->nombre ?? '' }}</p>
                            </div>
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="flex-1 @yield('main_class', 'overflow-y-auto')">
                @yield('content')
            </main>

        </div>
    </div>

    @livewireScripts

    <script>
        // ── Sonidos ──────────────────────────────────────────────────────────
        const _sound        = new Audio('/sounds/notification.mp3');
        const _soundSistema = new Audio('/sounds/notificacion_sistemas.mp3');
        _sound.volume        = 0.7;
        _soundSistema.volume = 0.9;

        // Categoría que dispara el sonido de sistemas
        const ID_TIPO_SISTEMA = 14;

        let _soundOn = localStorage.getItem('soundOn') === 'true';

        const _btn = document.getElementById('soundToggleBtn');
        const _on  = document.getElementById('soundOnIcon');
        const _off = document.getElementById('soundOffIcon');
        const _lbl = document.getElementById('soundLabel');

        function _syncSoundBtn() {
            if (_soundOn) {
                _on.classList.remove('hidden');
                _off.classList.add('hidden');
                _btn.style.borderColor     = '#10b981';
                _btn.style.color           = '#059669';
                _btn.style.backgroundColor = '#ecfdf5';
                _lbl.textContent           = 'SONIDO ON';
                _btn.title                 = 'Sonido activado';
            } else {
                _on.classList.add('hidden');
                _off.classList.remove('hidden');
                _btn.style.borderColor     = '#f87171';
                _btn.style.color           = '#dc2626';
                _btn.style.backgroundColor = '#fef2f2';
                _lbl.textContent           = 'SONIDO OFF';
                _btn.title                 = 'Sonido desactivado';
            }
        }

        _btn.addEventListener('click', () => {
            _soundOn = !_soundOn;
            localStorage.setItem('soundOn', _soundOn);
            _syncSoundBtn();
        });

        if (localStorage.getItem('soundOn') === null) {
            setTimeout(() => {
                _soundOn = confirm('¿Deseas habilitar notificaciones con sonido?');
                localStorage.setItem('soundOn', _soundOn);
                _syncSoundBtn();
            }, 1000);
        } else {
            _syncSoundBtn();
        }

        document.addEventListener('livewire:initialized', () => {

            // ── Sonido de notificación ────────────────────────────────────────
            Livewire.on('play-notification-sound', (data) => {
                console.log("🔔 Evento recibido", data);

                if (!_soundOn) {
                    console.log("🔇 Sonido desactivado");
                    return;
                }

                // Livewire 3 envuelve los params en array → data[0]
                const idTipo    = data?.[0]?.id_tipo_incidencia;
                const esSistema = idTipo == ID_TIPO_SISTEMA;

                if (esSistema) {
                    console.log("🚨 Sonido SISTEMA (categoría:", idTipo, ")");
                    _soundSistema.currentTime = 0;
                    _soundSistema.play().catch(() => {});
                } else {
                    console.log("🔊 Sonido normal (categoría:", idTipo, ")");
                    _sound.currentTime = 0;
                    _sound.play().catch(() => {});
                }
            });

            // ── Flash columna sin asignar ─────────────────────────────────────
            Livewire.on('flash-column', () => {
                const col = document.getElementById('columna-sin-asignar');
                if (!col) return;
                const colors = ['#fef3c7','#fff','#fef3c7','#fff','#fef3c7','#fff'];
                let i = 0;
                const iv = setInterval(() => {
                    col.style.backgroundColor = colors[i++];
                    if (i >= colors.length) { clearInterval(iv); col.style.backgroundColor = ''; }
                }, 350);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>