<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Modal de Calendario de Reservas del Día -->
        @if($showInfo)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
                    <div class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold">Calendario de Reservas</h3>
                            <p class="text-sm text-gray-300">
                                @if($todayReservas && $todayReservas->count() > 0)
                                    Reservas de Hoy ({{ now()->format('d/m/Y') }})
                                @else
                                    No hay reservas programadas
                                @endif
                            </p>
                        </div>
                        <button wire:click="toggleInfo" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <div id="modal-calendar" class="bg-gray-50 rounded-lg" style="height: 600px;"></div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ALERTA DE ÉXITO - RESERVA CONFIRMADA --}}
        @if($showNotification)
            <div class="fixed inset-0 flex items-center justify-center z-50 backdrop-blur-md bg-white/30 transition-opacity duration-300"
                 x-data="{ show: true }"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 border border-white/20"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <div class="flex justify-center mt-6">
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="text-center px-6 py-4">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            ¡Reserva Confirmada!
                        </h3>
                        <p class="text-gray-600">
                            {{ $notificationMessage }}
                        </p>
                    </div>
                    
                    <div class="flex justify-center px-6 pb-6">
                        <button wire:click="closeNotification"
                                class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 shadow-md">
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ALERTA DE ERROR FLOTANTE CENTRADA --}}
        @if (session()->has('error') && !$showNotification)
            <div class="fixed inset-0 flex items-center justify-center z-50 backdrop-blur-md bg-white/30 transition-opacity duration-300"
                 x-data="{ show: true }"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 border border-white/20"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <div class="flex justify-center mt-6">
                        <div class="rounded-full bg-red-100 p-3">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="text-center px-6 py-4">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            ¡Error en la Reserva!
                        </h3>
                        <p class="text-gray-600">
                            {{ session('error') }}
                        </p>
                    </div>
                    
                    <div class="flex justify-center px-6 pb-6">
                        <button @click="show = false; setTimeout(() => window.location.reload(), 300)"
                                class="w-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 shadow-md">
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ALERTA DE ADVERTENCIA (WARNING) --}}
        @if (session()->has('warning'))
            <div class="fixed inset-0 flex items-center justify-center z-50 backdrop-blur-md bg-white/30 transition-opacity duration-300"
                 x-data="{ show: true }"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 border border-white/20"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <div class="flex justify-center mt-6">
                        <div class="rounded-full bg-yellow-100 p-3">
                            <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="text-center px-6 py-4">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            ¡Atención!
                        </h3>
                        <p class="text-gray-600">
                            {{ session('warning') }}
                        </p>
                    </div>
                    
                    <div class="flex justify-center px-6 pb-6">
                        <button @click="show = false; $wire.dispatch('clearWarning')"
                                class="w-full bg-gradient-to-r from-yellow-600 to-yellow-500 hover:from-yellow-700 hover:to-yellow-600 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50 shadow-md">
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ALERTA DE INFORMACIÓN --}}
        @if (session()->has('info'))
            <div class="fixed inset-0 flex items-center justify-center z-50 backdrop-blur-md bg-white/30 transition-opacity duration-300"
                 x-data="{ show: true }"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 border border-white/20"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <div class="flex justify-center mt-6">
                        <div class="rounded-full bg-blue-100 p-3">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="text-center px-6 py-4">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            Información
                        </h3>
                        <p class="text-gray-600">
                            {{ session('info') }}
                        </p>
                    </div>
                    
                    <div class="flex justify-center px-6 pb-6">
                        <button @click="show = false; $wire.dispatch('clearInfo')"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 shadow-md">
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Formulario de Reserva y Equipos --}}
            <div class="lg:col-span-6 space-y-6">
                {{-- Selector de Equipo --}}
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="mb-4">
                        <h2 class="text-base font-medium text-gray-900">Equipos Disponibles</h2>
                    </div>

                    @if($equipos && $equipos->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($equipos as $equipo)
                                <div 
                                    wire:click="selectEquipo({{ $equipo->id }})"
                                    class="border rounded p-3 cursor-pointer transition-all flex-shrink-0 w-32 relative
                                        {{ isset($selectedEquipos[$equipo->id]) 
                                            ? 'border-sky-600 bg-sky-50 ring-2 ring-sky-600 ring-offset-2' 
                                            : 'border-gray-200 hover:border-sky-400 hover:bg-sky-50' }}">
                                    <div class="flex flex-col">
                                        <p class="text-sm font-medium {{ isset($selectedEquipos[$equipo->id]) ? 'text-sky-900' : 'text-gray-900' }}">
                                            {{ ucfirst($equipo->tipo) }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $equipo->codigo }}</p>
                                    </div>
                                    @if(isset($selectedEquipos[$equipo->id]))
                                        <div class="absolute top-2 right-2">
                                            <div class="w-5 h-5 bg-sky-600 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-sm text-gray-500 py-8">No hay equipos disponibles</p>
                    @endif
                </div>

                {{-- Formulario de Reserva --}}
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <h2 class="text-base font-medium text-gray-900 mb-4">Nueva Reserva</h2>

                    {{-- USUARIO SOLICITANTE--}}
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">
                            USUARIO SOLICITANTE *
                        </label>
                        <div class="flex gap-2">
                            <input 
                                type="text" 
                                wire:model.live="busquedaEmpleado"
                                wire:keydown.enter="buscarEmpleado"
                                class="flex-1 border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gray-400"
                                placeholder="Nombre del Usuario solicitante">
                            
                            <button 
                                type="button"
                                wire:click="buscarEmpleado"
                                class="px-3 py-1.5 text-xs bg-sky-600 text-white rounded hover:bg-sky-700 transition">
                                Buscar
                            </button>
                        </div>

                        @if($empleadoSeleccionado)
                            <div class="mt-2 p-2 bg-gray-50 border border-gray-200 rounded">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $empleadoSeleccionado->nombres }}</p>
                                        <p class="text-xs text-gray-600">{{ $empleadoSeleccionado->area->nombre ?? 'Sin área' }}</p>
                                    </div>
                                    <button type="button" wire:click="limpiarEmpleado" class="text-xs text-gray-500 hover:text-gray-700">
                                        Cambiar
                                    </button>
                                </div>
                            </div>
                        @endif

                        @if($empleadosEncontrados && count($empleadosEncontrados) > 0)
                            <div class="mt-2 max-h-48 overflow-y-auto border border-gray-200 rounded">
                                @foreach($empleadosEncontrados as $empleado)
                                    <button 
                                        type="button"
                                        wire:click="seleccionarEmpleado({{ $empleado->id }})"
                                        class="w-full text-left px-3 py-2 hover:bg-sky-50 border-b border-gray-100 last:border-b-0 transition-colors">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $empleado->nombres }}</p>
                                            <p class="text-xs text-gray-600">{{ $empleado->area->nombre ?? 'Sin área' }}</p>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if($empleadoNoEncontrado)
                            <p class="mt-1 text-xs text-red-600">Empleado no encontrado</p>
                        @endif
                    </div>

                    @if(count($selectedEquipos) > 0)
                        <div class="mb-4 p-2 bg-gray-50 border border-gray-200 rounded">
                            <p class="text-xs font-medium text-gray-700 mb-2">Equipos seleccionados ({{ count($selectedEquipos) }}):</p>
                            @foreach($selectedEquipos as $equipo)
                                <p class="text-xs text-gray-600">· {{ $equipo->codigo }} - {{ $equipo->tipo }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- Campo de Ubicación --}}
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">
                            Ubicación
                        </label>
                        <input 
                            type="text" 
                            wire:model="ubicacion"
                            class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gray-400"
                            placeholder="Ej:Sala de Novios, Sala de Regidores, Base Serenazgo...">
                    </div>

                    <form wire:submit.prevent="store" class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">
                                    Fecha * 
                                    <span class="text-grey-500 text-xs font-normal">(No domingos)</span>
                                </label>
                                <input 
                                    type="date" 
                                    wire:model.live="fecha_reserva" 
                                    required
                                    min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                    x-data="{}"
                                    x-init="
                                        $el.addEventListener('input', function() {
                                            const [year, month, day] = this.value.split('-');
                                            const date = new Date(year, month - 1, day);
                                            
                                            if (date.getDay() === 0) {
                                                alert('No se permiten reservas los días domingo');
                                                this.value = '';
                                                @this.set('fecha_reserva', '');
                                            }
                                        })
                                    "
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gray-400">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Inicio *</label>
                                <select wire:model.live="hora_inicio" required
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gray-400">
                                    <option value="">Hora</option>
                                    @php
                                        $ahora = \Carbon\Carbon::now();
                                        $horaActual = $ahora->format('H:i');
                                        $minutoActual = (int)$ahora->format('i');
                                        $esHoy = isset($fecha_reserva) && $fecha_reserva == $ahora->format('Y-m-d');
                                    @endphp
                                    
                                    @for($hour = 6; $hour <= 22; $hour++)
                                        @php
                                            $horaCompleta = str_pad($hour, 2, '0', STR_PAD_LEFT);
                                            $opcionHora = $horaCompleta . ':00';
                                            $opcionMedia = $horaCompleta . ':30';
                                            
                                            // Para hoy: deshabilitar horas pasadas
                                            if ($esHoy) {
                                                $disabledHora = $opcionHora < $horaActual;
                                                $disabledMedia = $opcionMedia < $horaActual;
                                            } else {
                                                $disabledHora = false;
                                                $disabledMedia = false;
                                            }
                                        @endphp
                                        
                                        <option value="{{ $opcionHora }}" {{ $disabledHora ? 'disabled' : '' }} class="{{ $disabledHora ? 'text-gray-400 bg-gray-100' : '' }}">
                                            {{ $opcionHora }} @if($esHoy && $disabledHora) (ya pasó) @endif
                                        </option>
                                        <option value="{{ $opcionMedia }}" {{ $disabledMedia ? 'disabled' : '' }} class="{{ $disabledMedia ? 'text-gray-400 bg-gray-100' : '' }}">
                                            {{ $opcionMedia }} @if($esHoy && $disabledMedia) (ya pasó) @endif
                                        </option>
                                    @endfor
                                </select>
                                @if($errorHorario)
                                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $errorHorario }}</p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Fin *</label>
                                <select wire:model.live="hora_fin" required
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gray-400">
                                    <option value="">Hora</option>
                                    @for($hour = 6; $hour <= 22; $hour++)
                                        @php
                                            $horaCompleta = str_pad($hour, 2, '0', STR_PAD_LEFT);
                                            $opcionHora = $horaCompleta . ':00';
                                            $opcionMedia = $horaCompleta . ':30';
                                            
                                            $horaInicioObj = isset($hora_inicio) ? \Carbon\Carbon::parse($hora_inicio) : null;
                                            $disabledHora = $horaInicioObj && $opcionHora <= $horaInicioObj->format('H:i');
                                            $disabledMedia = $horaInicioObj && $opcionMedia <= $horaInicioObj->format('H:i');
                                        @endphp
                                        
                                        <option value="{{ $opcionHora }}" {{ $disabledHora ? 'disabled' : '' }} class="{{ $disabledHora ? 'text-gray-400 bg-gray-100' : '' }}">
                                            {{ $opcionHora }} @if($disabledHora) (debe ser posterior al inicio) @endif
                                        </option>
                                        <option value="{{ $opcionMedia }}" {{ $disabledMedia ? 'disabled' : '' }} class="{{ $disabledMedia ? 'text-gray-400 bg-gray-100' : '' }}">
                                            {{ $opcionMedia }} @if($disabledMedia) (debe ser posterior al inicio) @endif
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Personal Técnico</label>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="requierePersonal" id="requiere_personal" class="mr-2">
                                <label for="requiere_personal" class="text-sm text-gray-700">Requiero que un técnico realice la instalación de los equipos (sin personal en sitio).</label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Motivo *</label>
                            <textarea wire:model="motivo" required rows="3"
                                class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gray-400"
                                placeholder="Describe el motivo de la reserva..."></textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" wire:click="limpiarFormulario" 
                                class="flex-1 px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded text-sm transition">
                                Limpiar
                            </button>
                            <button type="submit" 
                                class="flex-1 px-3 py-1.5 bg-sky-600 hover:bg-sky-700 disabled:bg-gray-400 text-white rounded text-sm transition">
                                Reservar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Calendario --}}
            <div class="lg:col-span-6">
                <div class="mb-8 bg-white rounded-xl shadow-lg p-6">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Calendario de Reservas</h4>
                            <div class="text-sm text-gray-600">
                                Total: {{ $existingReservas ? $existingReservas->count() : 0 }} reservas
                            </div>
                        </div>
                        
                        @if($existingReservas && $existingReservas->count() > 0)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div class="bg-sky-600 text-white px-4 py-3">
                                    <div class="flex items-center justify-center">
                                        <h5 class="font-semibold text-lg">
                                            {{ Carbon\Carbon::now()->format('F Y') }}
                                        </h5>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                                    @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $weekday)
                                        <div class="px-2 py-2 text-center text-xs font-semibold text-gray-700 border-r border-gray-200 last:border-r-0">
                                            {{ $weekday }}
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="grid grid-cols-7">
                                    @foreach($this->getCalendarDays() as $calendarDay)
                                        @if($calendarDay['isEmpty'])
                                            <div class="h-16 border-r border-b border-gray-100 bg-gray-50"></div>
                                        @else
                                            <div 
                                                @if(!$calendarDay['disabled'])
                                                    wire:click="selectDay({{ $calendarDay['day'] }})"
                                                    class="h-16 border-r border-b border-gray-100 
                                                        @if($calendarDay['isToday']) bg-blue-50 @else bg-white @endif
                                                        hover:bg-gray-50 transition-colors cursor-pointer relative overflow-hidden
                                                        @if($selectedDate && $selectedDate->day == $calendarDay['day']) ring-2 ring-blue-500 @endif"
                                                @else
                                                    class="h-16 border-r border-b border-gray-200 
                                                        bg-grey-50 opacity-60 cursor-not-allowed relative overflow-hidden"
                                                    title="Los domingos no están disponibles para reservas"
                                                @endif
                                            >
                                                <div class="absolute top-1 left-2">
                                                    <span class="text-xs font-semibold 
                                                        @if($calendarDay['isToday']) text-blue-600 
                                                        @elseif($calendarDay['disabled']) text-grey-400
                                                        @else text-gray-700 @endif">
                                                        {{ $calendarDay['day'] }}
                                                    </span>
                                                </div>
                                                
                                                @if($calendarDay['disabled'])
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <div class="text-center">
                                                            <svg class="w-4 h-4 text-grey-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="absolute bottom-1 left-1 right-1">
                                                        @if($calendarDay['reservas'] && $calendarDay['reservas']->count() > 0)
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach($calendarDay['reservas']->take(3) as $reserva)
                                                                    <div class="w-2 h-2 rounded-full 
                                                                        @if($reserva->estado === 'APROBADA') 
                                                                            bg-green-500 
                                                                        @else 
                                                                            bg-yellow-500 
                                                                        @endif"
                                                                        title="{{ $reserva->motivo }} - Ubicación: {{ $reserva->ubicacion ?? 'No especificada' }}">
                                                                    </div>
                                                                @endforeach
                                                                @if($calendarDay['reservas']->count() > 3)
                                                                    <span class="text-xs text-gray-500 font-medium">
                                                                        +{{ $calendarDay['reservas']->count() - 3 }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($calendarDay['reservas'] && $calendarDay['reservas']->count() > 0)
                                                        <div class="absolute top-1 right-2">
                                                            <span class="text-xs bg-gray-800 text-white px-1.5 py-0.5 rounded-full font-medium">
                                                                {{ $calendarDay['reservas']->count() }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                @if($selectedDate)
                                    <div class="flex items-center justify-between mb-2">
                                        <h6 class="text-sm font-semibold text-gray-700">
                                            Reservas del {{ $selectedDate->format('d/m/Y') }}
                                        </h6>
                                        <button wire:click="$set('selectedDate', null)" 
                                                class="text-xs text-gray-500 hover:text-gray-700">
                                            Limpiar selección
                                        </button>
                                    </div>
                                    
                                    @if($selectedDayReservas && $selectedDayReservas->count() > 0)
                                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                            <div class="flex items-center justify-between px-3 py-2 bg-gray-50 border-b border-gray-200">
                                                <button 
                                                    wire:click="previousReserva" 
                                                    @if($currentReservaIndex <= 0) disabled @endif
                                                    class="p-1.5 text-gray-600 hover:text-sky-600 hover:bg-white disabled:text-gray-400 disabled:cursor-not-allowed rounded-lg transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                    </svg>
                                                </button>
                                                
                                                <div class="text-center">
                                                    <div class="text-xs font-semibold text-gray-800">
                                                        Reserva {{ $currentReservaIndex + 1 }} de {{ $selectedDayReservas->count() }}
                                                    </div>
                                                </div>
                                                
                                                <button 
                                                    wire:click="nextReserva" 
                                                    @if($currentReservaIndex >= $selectedDayReservas->count() - 1) disabled @endif
                                                    class="p-1.5 text-gray-600 hover:text-sky-600 hover:bg-white disabled:text-gray-400 disabled:cursor-not-allowed rounded-lg transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            
                                            @php
                                                $currentReserva = $selectedDayReservas->slice($currentReservaIndex, 1)->first();
                                            @endphp
                                            @if($currentReserva)
                                                <div class="p-3">
                                                    <div class="flex items-start gap-3">
                                                        <div class="flex-shrink-0 w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </div>
                                                        
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-gray-900 font-bold text-sm mb-1">
                                                                {{ $currentReserva->motivo }}
                                                            </div>
                                                            
                                                            @if($currentReserva->ubicacion)
                                                                <div class="flex items-center gap-1.5 text-xs text-gray-600 mb-1">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    </svg>
                                                                    <span>{{ $currentReserva->ubicacion }}</span>
                                                                </div>
                                                            @endif
                                                            
                                                            @if($currentReserva->equipos_ids)
                                                                @php
                                                                    $equiposTypes = $this->getEquiposTypes($currentReserva->equipos_ids);
                                                                @endphp
                                                                @if($equiposTypes)
                                                                    <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                                        </svg>
                                                                        <span>{{ $equiposTypes }}</span>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>

                                                        <div class="flex-shrink-0 text-sky-600 text-xs font-semibold whitespace-nowrap">
                                                            {{ Carbon\Carbon::parse($currentReserva->fecha_inicio)->format('H:i') }} - 
                                                            {{ Carbon\Carbon::parse($currentReserva->fecha_fin)->format('H:i') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-8 bg-white rounded border border-gray-200">
                                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-gray-500">No hay reservas para este día</p>
                                        </div>
                                    @endif
                                @else
                                    <h6 class="text-sm font-semibold text-gray-700 mb-3">
                                        Haz click en un día para ver sus reservas
                                    </h6>
                                    <div class="text-center py-8 bg-white rounded border border-gray-200">
                                        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-500">Selecciona un día del calendario para ver las reservas</p>
                                        <p class="text-xs text-red-500 mt-2"> Los domingos no están disponibles para reservas</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-600 font-medium">No hay reservas este mes</p>
                                <p class="text-gray-500 text-sm mt-1">Las reservas aparecerán aquí cuando se creen</p>
                                <p class="text-xs text-red-500 mt-2">Recuerda: No se permiten reservas los días domingo</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    let modalCalendar = null;
    
    window.livewire.on('showModalCalendar', (todayReservas) => {
        const modalCalendarEl = document.getElementById('modal-calendar');
        
        if (modalCalendarEl) {
            if (modalCalendar) {
                modalCalendar.destroy();
            }
            
            const modalEvents = (todayReservas || []).map(reserva => ({
                id: reserva.id,
                title: reserva.motivo,
                start: reserva.fecha_inicio,
                end: reserva.fecha_fin,
                backgroundColor: reserva.estado === 'APROBADA' ? '#10b981' : '#f59e0b',
                borderColor: reserva.estado === 'APROBADA' ? '#059669' : '#d97706',
                textColor: '#ffffff',
                extendedProps: {
                    usuario: reserva.usuario_creacion,
                    equipo: reserva.equipo?.codigo,
                    equipos: reserva.equipos_ids,
                    ubicacion: reserva.ubicacion,
                    requiere_personal: reserva.requiere_personal,
                    estado: reserva.estado
                }
            }));
            
            let initialView = 'timeGridDay';
            let initialDate = new Date();
            
            if (todayReservas && todayReservas.length > 0) {
                const firstReserva = todayReservas[0];
                const reservaDate = new Date(firstReserva.fecha_inicio);
                initialDate = reservaDate;
                
                const uniqueDates = [...new Set(todayReservas.map(r => r.fecha_inicio.split(' ')[0]))];
                if (uniqueDates.length > 1) {
                    initialView = 'timeGridWeek';
                }
            }
            
            modalCalendar = new FullCalendar.Calendar(modalCalendarEl, {
                initialView: initialView,
                initialDate: initialDate,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridDay,timeGridWeek,dayGridMonth'
                },
                locale: 'es',
                height: 600,
                slotMinTime: '06:00:00',
                slotMaxTime: '23:00:00',
                allDaySlot: false,
                slotDuration: '00:30:00',
                businessHours: {
                    daysOfWeek: [1, 2, 3, 4, 5],
                    startTime: '08:00',
                    endTime: '20:00',
                },
                events: modalEvents,
                eventDisplay: 'block',
                eventClassNames: 'text-xs',
                eventClick: function(info) {
                    const reserva = info.event.extendedProps;
                    alert(`📋 Detalles de Reserva\n\n` +
                          `Horario: ${info.event.start.toLocaleString()} - ${info.event.end.toLocaleString()}\n` +
                          `Motivo: ${info.event.title}\n` +
                          `Ubicación: ${reserva.ubicacion || 'No especificada'}\n` +
                          `Empleado: ${reserva.usuario}\n` +
                          `Personal Técnico: ${reserva.requiere_personal ? 'Sí' : 'No'}\n` +
                          `Estado: ${reserva.estado}`);
                }
            });
            
            modalCalendar.render();
        }
    });
    
    window.livewire.on('refreshCalendar', () => {
        if (typeof calendar !== 'undefined' && calendar) {
            calendar.refetchEvents();
        }
    });
});
</script>
@endpush