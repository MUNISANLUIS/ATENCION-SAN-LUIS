<!-- resources/views/components/sidebar.blade.php -->
@php
    $rolUsuario = Auth::user()->id_tipo_usuario_rol ?? null;
@endphp

<aside class="w-72 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white flex flex-col shadow-2xl">
    {{-- Logo --}}
    <div class="p-6 border-b border-gray-700/50">
    <a href="/listar-tickets" class="flex items-center space-x-3 group">
        <div
            class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
            </svg>
        </div>
        <div class="flex-1">
            <h1 class="text-xl font-bold tracking-tight">TICKETS MDSL</h1>
            <p class="text-xs text-gray-400">Panel de Control</p>
        </div>
        <div class="text-right" x-data="{
            currentTime: '',
            updateTime() {
                const now = new Date();
                const day = String(now.getDate()).padStart(2, '0');
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const year = now.getFullYear();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                this.currentTime = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
            }
        }" x-init="updateTime(); setInterval(() => updateTime(), 1000)">
            <p class="text-xs text-gray-400">Hora del Servidor</p>
            <p class="text-sm font-semibold text-indigo-400" x-text="currentTime"></p>
            <p class="text-xs text-gray-500">{{ now()->timezone }}</p>
        </div>
    </a>
</div>

    {{-- Navegación --}}
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <div class="mb-4">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Principal</p>
        </div>

        {{-- Inicio --}}

        @if (in_array($rolUsuario, [1, 2]))
            <a href="/listar-tickets"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.ticket') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.ticket') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="font-medium">
                    @if ($rolUsuario == 1)
                        Tickets
                    @else
                        {{ Auth::user()->name ?? 'Mis Tickets' }}
                    @endif
                </span>
            </a>
            <a href="/tablero-control"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.tablero') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.tablero') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="font-medium">
                        Tablero Control
                </span>
            </a>
            <a href="/monitoreo"
            class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.monitoreo') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.monitoreo') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            <span class="font-medium">Monitoreo</span>
            </a>
        @endif


        @if (in_array($rolUsuario, [1]))
            <div class="my-4 border-t border-gray-700/50"></div>

            <div class="mb-4">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestión</p>
            </div>

            {{-- Usuarios --}}
            <a href="/usuarios"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.usuario') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.usuario') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <span class="font-medium">Usuarios</span>
            </a>

            {{-- Áreas --}}
            <a href="/area"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.area') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.area') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                <span class="font-medium">Áreas</span>
            </a>

            {{-- Categorías --}}
            <a href="/categoria"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.categoria') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.categoria') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                    </path>
                </svg>
                <span class="font-medium">Categorías</span>
            </a>

            {{-- Subcategorías --}}
            <a href="/subcategoria"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.subcategoria') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.subcategoria') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                    </path>
                </svg>
                <span class="font-medium">Subcategorías</span>
            </a>

            {{-- Empleados --}}
            <a href="/empleado"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.empleados') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.empleados') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                    </path>
                </svg>
                <span class="font-medium">Empleados</span>
            </a>

            {{-- Equipos --}}
            <a href="/equipo"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.equipo') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.equipo') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                <span class="font-medium">Equipos</span>
            </a>

            {{-- Reservas en Horario --}}
            <a href="/reservas-horario"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.reservas-horario') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.reservas-horario') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                <span class="font-medium">Reservas Horario</span>
            </a>


                        {{-- Empleados --}}
                        <a href="/sistemas"
                        class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.empleados') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.empleados') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        <span class="font-medium">Sistemas</span>
                    </a>
        
        
            <div class="my-4 border-t border-gray-700/50"></div>

            <div class="mb-4">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Análisis</p>
            </div>

            {{-- Reporte --}}
            <a href="/reporte"
                class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.reporte') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/50' : 'hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.reporte') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="font-medium">Reportes</span>
            </a>
        @endif
    </nav>

    {{-- Usuario y logout --}}
    <div class="border-t border-gray-700/50 p-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            {{-- Logout --}}
            <button type="submit"
                class="w-full flex items-center justify-center space-x-2 px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 transition-all duration-200 shadow-lg hover:shadow-red-500/50 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                <span>Cerrar sesión</span>
            </button>
        </form>

    </div>
</aside>
