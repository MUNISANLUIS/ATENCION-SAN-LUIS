{{-- UN SOLO ELEMENTO RAÍZ - div principal --}}
<div>
    {{-- Polling para verificar alertas cada 30 segundos --}}
    <div wire:poll.30s="pollAlerts"></div>
    
    {{-- Alerta flotante para reservas próximas --}}
    @if($showAlert)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 10000)" x-show="show" 
             class="fixed top-4 right-4 z-50 max-w-md animate-slide-in">
            <div class="rounded-lg shadow-lg overflow-hidden 
                @if($alertType == 'critical') bg-red-50 border-l-4 border-red-500
                @elseif($alertType == 'danger') bg-orange-50 border-l-4 border-orange-500
                @else bg-yellow-50 border-l-4 border-yellow-500 @endif">
                
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($alertType == 'critical')
                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    @elseif($alertType == 'danger')
                                        <svg class="h-5 w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium 
                                        @if($alertType == 'critical') text-red-800
                                        @elseif($alertType == 'danger') text-orange-800
                                        @else text-yellow-800 @endif">
                                        {{ $alertMessage }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button wire:click="dismissAlert" class="inline-flex text-gray-400 hover:text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                {{-- Barra de progreso animada --}}
                <div class="h-1 bg-gray-200">
                    <div class="h-full 
                        @if($alertType == 'critical') bg-red-500
                        @elseif($alertType == 'danger') bg-orange-500
                        @else bg-yellow-500 @endif
                        animate-progress"></div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white border border-gray-300">
        {{-- Header institucional minimalista --}}
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center justify-between">
                <button wire:click="previousMonth" class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                    {{ Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->format('F Y') }}
                </h2>
                
                <button wire:click="nextMonth" class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Calendario minimalista --}}
        <div class="p-4">
            {{-- Días de la semana --}}
            <div class="grid grid-cols-7 border-b border-gray-200">
                @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $weekday)
                    <div class="py-2 text-center text-xs font-medium text-gray-600 border-r border-gray-200 last:border-r-0">
                        {{ $weekday }}
                    </div>
                @endforeach
            </div>
            
            {{-- Cuadrícula de días --}}
            <div class="grid grid-cols-7">
                @foreach($calendarDays as $calendarDay)
                    @if($calendarDay['isEmpty'])
                        <div class="h-14 border-r border-b border-gray-100 bg-gray-50"></div>
                    @else
                        <button 
                            wire:click="selectDay({{ $calendarDay['day'] }})" 
                            class="h-14 border-r border-b border-gray-100 
                                @if($calendarDay['isToday']) bg-blue-50 @else bg-white @endif
                                hover:bg-gray-50 transition-colors relative
                                @if($selectedDate && $selectedDate->day == $calendarDay['day']) bg-blue-100 @endif">
                        
                            {{-- Número del día --}}
                            <div class="absolute top-1 left-2">
                                <span class="text-sm font-medium 
                                    @if($calendarDay['isToday']) text-blue-700 font-semibold @else text-gray-700 @endif">
                                    {{ $calendarDay['day'] }}
                                </span>
                            </div>
                            
                            {{-- Indicadores de reservas con tooltip --}}
                            @if($calendarDay['reservas']->count() > 0)
                                <div class="absolute bottom-1 left-1 right-1 flex justify-center">
                                    @if($calendarDay['reservas']->count() == 1)
                                        @php
                                            $reserva = $calendarDay['reservas']->first();
                                        @endphp
                                        <div class="w-1.5 h-1.5 rounded-full 
                                            @if($this->isReservaUrgent($reserva)) bg-red-500 animate-pulse
                                            @else bg-blue-600 @endif" 
                                             title="{{ $reserva->motivo }} - {{ $reserva->solicitante->nombres ?? 'N/A' }} - Ubicación: {{ $reserva->ubicacion ?? 'N/E' }}">
                                        </div>
                                    @else
                                        <span class="text-xs bg-blue-600 text-white px-1.5 py-0.5 rounded-full text-[10px] font-medium"
                                              title="{{ $calendarDay['reservas']->count() }} reservas">
                                            {{ $calendarDay['reservas']->count() }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Detalle de reservas --}}
        @if($selectedDate)
            <div class="border-t border-gray-200 bg-gray-50">
                <div class="px-4 py-3 border-b border-gray-200 bg-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800">
                            {{ $selectedDate->format('d/m/Y') }}
                        </h3>
                        <button wire:click="$set('selectedDate', null)" 
                                class="text-xs text-gray-500 hover:text-gray-700">
                            Cerrar
                        </button>
                    </div>
                </div>
                
                @if($selectedDayReservas && $selectedDayReservas->count() > 0)
                    {{-- Navegación --}}
                    <div class="px-4 py-2 border-b border-gray-200 bg-white">
                        <div class="flex items-center justify-between">
                            <button 
                                wire:click="previousReserva" 
                                @if($currentReservaIndex <= 0) disabled @endif
                                class="p-1 text-gray-400 hover:text-gray-600 disabled:text-gray-300 disabled:cursor-not-allowed rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            
                            <span class="text-xs text-gray-600 font-medium">
                                {{ $currentReservaIndex + 1 }} de {{ $selectedDayReservas->count() }}
                            </span>
                            
                            <button 
                                wire:click="nextReserva" 
                                @if($currentReservaIndex >= $selectedDayReservas->count() - 1) disabled @endif
                                class="p-1 text-gray-400 hover:text-gray-600 disabled:text-gray-300 disabled:cursor-not-allowed rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    @if($currentReserva)
                        @php
                            $isUrgent = $this->isReservaUrgent($currentReserva);
                        @endphp
                        <div class="px-4 py-3 bg-white">
                            <div class="space-y-2">
                                {{-- Horario --}}
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-800">
                                        {{ Carbon\Carbon::parse($currentReserva->fecha_inicio)->format('H:i') }} - 
                                        {{ Carbon\Carbon::parse($currentReserva->fecha_fin)->format('H:i') }}
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        @if($currentReserva->estado === 'APROBADA') 
                                            bg-green-100 text-green-800 
                                        @elseif($currentReserva->estado === 'RECHAZADA')
                                            bg-red-100 text-red-800
                                        @else 
                                            bg-yellow-100 text-yellow-800 
                                        @endif">
                                        {{ $currentReserva->estado }}
                                    </span>
                                    
                                    {{-- Badge de urgencia --}}
                                    @if($isUrgent)
                                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800 animate-pulse">
                                            ⚡ Próxima
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Motivo --}}
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800">{{ $currentReserva->motivo }}</h4>
                                </div>
                                
                                {{-- Cuenta regresiva para reservas próximas --}}
                                @if($isUrgent)
                                    @php
                                        $fechaInicio = Carbon\Carbon::parse($currentReserva->fecha_inicio);
                                        $now = Carbon\Carbon::now();
                                        $diffInMinutes = $now->diffInMinutes($fechaInicio, false);
                                        $hours = floor($diffInMinutes / 60);
                                        $minutes = $diffInMinutes % 60;
                                    @endphp
                                    <div class="flex items-center gap-2 text-sm bg-red-50 p-2 rounded-lg">
                                        <svg class="w-4 h-4 text-red-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-red-700 font-medium">
                                            @if($hours > 0)
                                                Comienza en {{ $hours }} hora(s) y {{ $minutes }} minuto(s)
                                            @else
                                                Comienza en {{ $minutes }} minuto(s)
                                            @endif
                                        </span>
                                    </div>
                                @endif
                                
                                {{-- UBICACIÓN --}}
                                @if($currentReserva->ubicacion)
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-gray-700">{{ $currentReserva->ubicacion }}</span>
                                    </div>
                                @endif
                                
                                {{-- Solicitante --}}
                                @if($currentReserva->solicitante)
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>{{ $currentReserva->solicitante->nombres }}</span>
                                        @if($currentReserva->solicitante->area)
                                            <span class="text-gray-400">• {{ $currentReserva->solicitante->area->nombre }}</span>
                                        @endif
                                    </div>
                                @endif
                                
                                {{-- Equipos --}}
                                @if($currentReserva->equipos_ids)
                                    @php
                                        $equiposCatalogo = \App\Models\Equipo::pluck('tipo', 'id')->toArray();
                                        $ids = is_array($currentReserva->equipos_ids) 
                                            ? $currentReserva->equipos_ids 
                                            : (str_contains($currentReserva->equipos_ids, '[') 
                                                ? json_decode($currentReserva->equipos_ids, true) 
                                                : explode(',', $currentReserva->equipos_ids));
                                        $equiposTypes = [];
                                        if (is_array($ids)) {
                                            foreach ($ids as $id) {
                                                $id = trim($id);
                                                if (isset($equiposCatalogo[$id])) {
                                                    $equiposTypes[] = $equiposCatalogo[$id];
                                                }
                                            }
                                        }
                                        $equiposTypesStr = implode(', ', $equiposTypes);
                                    @endphp
                                    @if($equiposTypesStr)
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>{{ $equiposTypesStr }}</span>
                                        </div>
                                    @endif
                                @endif
                                
                                {{-- Personal Técnico --}}
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span>
                                        @if($currentReserva->requiere_personal)
                                            <span class="text-orange-600 font-medium">Requiere personal técnico</span>
                                        @else
                                            <span class="text-gray-500">No requiere personal técnico</span>
                                        @endif
                                    </span>
                                </div>
                                
                                {{-- Fechas de creación --}}
                                <div class="pt-2 text-xs text-gray-400 border-t border-gray-100">
                                    <div>Creado: {{ Carbon\Carbon::parse($currentReserva->fecha_creacion)->format('d/m/Y H:i') }}</div>
                                    @if($currentReserva->fecha_aprobacion)
                                        <div>Aprobado: {{ Carbon\Carbon::parse($currentReserva->fecha_aprobacion)->format('d/m/Y H:i') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="px-4 py-8 text-center bg-white">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">No hay reservas programadas</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Estilos CSS adicionales --}}
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes progress {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        .animate-progress {
            animation: progress 10s linear forwards;
        }
        
        .animate-pulse {
            animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .animate-bounce {
            animation: bounce 1s infinite;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-25%);
            }
        }
    </style>
</div>