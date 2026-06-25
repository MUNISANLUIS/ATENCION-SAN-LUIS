<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 py-8">
    {{-- Elementos decorativos de fondo --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Círculos decorativos -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-100/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-sky-100/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-50/10 rounded-full blur-3xl"></div>
        
        <!-- Líneas decorativas -->
        <div class="absolute top-20 left-10 w-32 h-32 border border-blue-100/30 rounded-full"></div>
        <div class="absolute bottom-20 right-10 w-24 h-24 border border-sky-100/20 rounded-full"></div>
    </div>

    <div class="relative w-full max-w-5xl mx-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 bg-white/80 backdrop-blur-sm shadow-2xl rounded-3xl overflow-hidden border border-gray-100/60 min-h-[600px] lg:min-h-[650px]">
            
            {{-- Columna izquierda - Información --}}
            <div class="bg-gradient-to-br from-blue-600 via-sky-600 to-cyan-600 p-10 lg:p-12 flex flex-col justify-between relative overflow-hidden">
                <!-- Elementos decorativos SVG -->
                <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 100 0 L 200 100 L 100 200 L 0 100 Z" fill="white"/>
                        <circle cx="100" cy="100" r="60" fill="none" stroke="white" stroke-width="2"/>
                        <circle cx="100" cy="100" r="40" fill="none" stroke="white" stroke-width="2"/>
                        <circle cx="100" cy="100" r="20" fill="white"/>
                    </svg>
                </div>
                <div class="absolute bottom-0 left-0 w-48 h-48 opacity-10">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <rect x="10" y="10" width="80" height="80" fill="none" stroke="white" stroke-width="2" rx="10"/>
                        <rect x="25" y="25" width="50" height="50" fill="white" rx="5"/>
                    </svg>
                </div>

                <div class="relative z-10 flex-1 flex flex-col justify-center">
                    <!-- Logo y título -->
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6zm1 2h6v2H7V6zm0 4h6v2H7v-2zm0 4h4v2H7v-2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-white text-2xl font-bold tracking-tight">Municipalidad San Luis</h1>
                        </div>
                    </div>

                    <!-- Mensaje principal -->
                    <div class="space-y-4">
                        <h2 class="text-white text-3xl font-bold leading-tight">
                            ¡Bienvenido al<br>
                            <span class="text-blue-200">Sistema Atención San Luis</span>
                        </h2>
                        <p class="text-blue-100 text-sm leading-relaxed opacity-90 max-w-sm">
                            Administrador de tickets, videos, llamado de ventanilla con nuestra plataforma integrada. Accede de forma segura y eficiente.
                        </p>
                    </div>
                </div>

                <!-- Características -->
                <div class="relative z-10 mt-6">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white text-xs font-medium">Seguridad</span>
                            </div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                                </svg>
                                <span class="text-white text-xs font-medium">Eficiencia</span>
                            </div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white text-xs font-medium">Trámites</span>
                            </div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2h14a1 1 0 100-2H3zm0 4a1 1 0 000 2h14a1 1 0 100-2H3zm0 4a1 1 0 100 2h14a1 1 0 100-2H3zm0 4a1 1 0 100 2h14a1 1 0 100-2H3z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white text-xs font-medium">24/7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna derecha - Formulario --}}
            <div class="p-8 lg:p-12 bg-white flex items-center">
                <div class="max-w-sm mx-auto w-full">
                    <!-- Encabezado del formulario -->
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-800">Iniciar Sesión</h3>
                    </div>

                    <!-- Mensajes de éxito o error -->
                    @if (session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form wire:submit.prevent="login" class="space-y-5">
                        <!-- Campo de usuario -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Usuario</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <input type="text" id="username" wire:model="username"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                                    placeholder="Ingresa tu usuario" required>
                            </div>
                            @error('username')
                                <span class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Campo de contraseña -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contraseña</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <input type="password" id="password" wire:model="password"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                                    placeholder="Ingresa tu contraseña" required>
                            </div>
                            @error('password')
                                <span class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Opciones adicionales -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                            </label>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">¿Olvidaste tu contraseña?</a>
                        </div>

                        <!-- Botón de login -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-sky-600 hover:from-blue-700 hover:to-sky-700 text-white font-semibold py-3.5 rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                            <span>Iniciar Sesión</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Footer -->
                    <p class="text-center text-xs text-gray-400 mt-6">
                        © 2025 Municipalidad San Luis. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>