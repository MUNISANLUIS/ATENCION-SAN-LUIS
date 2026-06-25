<div class="min-h-screen bg-gradient-to-br from-slate-100 via-slate-50 to-slate-200">
    <div class="w-full px-2 sm:px-4 lg:px-6 py-8">
        
        <!-- Card principal con diseño minimalista -->
        <div class="rounded-lg border border-gray-400 bg-white shadow-sm p-4 w-full">
            <!-- Header minimalista -->
            <div class="border-b border-gray-400 pb-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <div class="bg-sky-600 p-2 rounded-lg mr-3 shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        Seguimiento de Tickets
                    </h2>
                    
                    <span class="text-sky-600 text-sm font-medium bg-sky-50 px-3 py-1 rounded-lg border border-sky-200">
                        Municipalidad Distrital de San Luis
                    </span>

                </div>
                <p class="text-gray-700 mt-2 ml-12 text-sm">
                    Consulte el estado de sus solicitudes de soporte técnico
                </p>
            </div>

            <div class="p-4">
                {{-- Mensajes de alerta --}}
                @if (session()->has('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-600 p-3 rounded-r">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session()->has('info'))
                    <div class="mb-4 bg-blue-50 border-l-4 border-blue-600 p-3 rounded-r">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Formulario de búsqueda --}}
                <form wire:submit.prevent="buscar" class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        <!-- Tipo de búsqueda -->
                        <div class="lg:col-span-3">
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Buscar por
                                </span>
                            </label>
                            <select wire:model.live="tipoBusqueda" 
                                class="w-full border border-gray-400 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-sky-600 focus:border-sky-600 bg-white">
                                <option value="correlativo">Número de Ticket</option>
                                <option value="nombre">Nombre del Solicitante</option>
                           
                            </select>
                        </div>

                        <!-- Campo de búsqueda -->
                        <div class="lg:col-span-7">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    @if($tipoBusqueda === 'correlativo')
                                        <svg class="w-4 h-4 mr-1 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                        Número de Ticket
                                    @elseif($tipoBusqueda === 'nombre')
                                        <svg class="w-4 h-4 mr-1 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Nombre del Solicitante
                                    @else
                                        <svg class="w-4 h-4 mr-1 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Área Municipal
                                    @endif
                                </span>
                            </label>

                            @if($tipoBusqueda === 'area')
                                <select wire:model="busqueda" 
                                    class="w-full border border-gray-400 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-sky-600 focus:border-sky-600 bg-white">
                                    <option value="">Seleccione un área</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                            @elseif($tipoBusqueda === 'nombre')
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        wire:model="busquedaEmpleado"
                                        wire:keydown.enter.prevent="buscarEmpleado"
                                        class="w-full border border-gray-400 rounded-lg pl-4 pr-24 py-2 text-sm focus:ring-2 focus:ring-sky-600 focus:border-sky-600 bg-white @error('busqueda') border-red-500 @enderror"
                                        placeholder="Ingrese al menos 3 caracteres..."
                                        autocomplete="off">
                                    
                                    @if($empleadoSeleccionado)
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center space-x-2 bg-green-100 px-3 py-1 rounded border border-green-300">
                                            <span class="text-xs font-medium text-green-800">
                                                {{ $empleadoSeleccionado->nombres }}
                                            </span>
                                            <button 
                                                type="button" 
                                                wire:click="limpiarEmpleado"
                                                class="text-green-700 hover:text-green-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @error('busqueda')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            @else
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm font-mono">TK-</span>
                                    </div>
                                    <input 
                                        type="text" 
                                        wire:model="busqueda" 
                                        class="w-full border border-gray-400 rounded-lg pl-12 pr-4 py-2 text-sm focus:ring-2 focus:ring-sky-600 focus:border-sky-600 bg-white font-mono @error('busqueda') border-red-500 @enderror"
                                        placeholder="000000">
                                </div>
                                @error('busqueda')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <!-- Botón de búsqueda -->
                        <div class="lg:col-span-2 flex items-end">
                            <button 
                                type="submit" 
                                @if($tipoBusqueda === 'nombre' && !$empleadoSeleccionado) disabled @endif
                                class="w-full bg-sky-600 hover:bg-sky-700 text-white font-medium px-4 py-2 rounded-lg transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed text-sm border border-sky-700 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Consultar
                            </button>
                        </div>
                    </div>

                    @if(!empty($tickets) && count($tickets) > 0)
                        <div class="flex justify-start pt-2 border-t border-gray-300">
                            <button type="button" wire:click="limpiarBusqueda" 
                                class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Nueva consulta
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Resultados de búsqueda --}}
        @if(!empty($tickets) && count($tickets) > 0)
            <div class="mt-8">
                <!-- Header de resultados -->
                <div class="bg-sky-50 rounded-xl border border-sky-200 p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-sky-500 p-3 rounded-xl mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Resultados de la búsqueda</h3>
                                <p class="text-sm text-gray-600 mt-1">Tickets encontrados según los criterios de búsqueda</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="bg-white rounded-lg px-4 py-2 border border-sky-300 shadow-sm">
                                <div class="text-2xl font-bold text-sky-600">{{ count($tickets) }}</div>
                                <div class="text-xs text-gray-600">{{ count($tickets) === 1 ? 'registro encontrado' : 'registros encontrados' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de tarjetas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tickets as $ticket)
                        <div class="rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <!-- Header de la tarjeta -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="bg-sky-500 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-mono font-medium text-sky-600">TK-{{ str_pad($ticket->correlativo, 6, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($ticket->fecha_creacion)->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border
                                    @if($this->getEstadoColor($ticket->estado) === 'success') bg-green-100 text-green-800 border-green-300
                                    @elseif($this->getEstadoColor($ticket->estado) === 'warning') bg-yellow-100 text-yellow-800 border-yellow-300
                                    @elseif($this->getEstadoColor($ticket->estado) === 'danger') bg-red-100 text-red-800 border-red-300
                                    @elseif($this->getEstadoColor($ticket->estado) === 'info') bg-blue-100 text-blue-800 border-blue-300
                                    @else bg-gray-100 text-gray-800 border-gray-300
                                    @endif">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                        @if($this->getEstadoColor($ticket->estado) === 'success') bg-green-500
                                        @elseif($this->getEstadoColor($ticket->estado) === 'warning') bg-yellow-500
                                        @elseif($this->getEstadoColor($ticket->estado) === 'danger') bg-red-500
                                        @elseif($this->getEstadoColor($ticket->estado) === 'info') bg-blue-500
                                        @else bg-gray-500
                                        @endif"></span>
                                    {{ $this->getEstadoNombre($ticket->estado) }}
                                </span>
                            </div>

                            <!-- Información principal -->
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-500">Solicitante</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $ticket->nombres }}</p>
                                    <p class="text-xs text-gray-500">Creado por: {{ $ticket->usuario_creacion }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Área</p>
                                    @php
                                        $area = \App\Models\Area::find($ticket->id_area);
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-300">
                                        {{ $area->abreviatura ?? 'N/A' }}
                                    </span>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Categoría</p>
                                    @php
                                        $categoria = \App\Models\CategoriaIncidencia::find($ticket->id_tipo_incidencia);
                                    @endphp
                                    <p class="text-sm text-gray-900">{{ $categoria->nombre ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Técnico asignado</p>
                                    @if($ticket->usuario)
                                        <p class="text-sm text-gray-900">{{ $ticket->usuario->name }}</p>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Pendiente</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                                <button 
                                    wire:click="verDetalle({{ $ticket->id }})" 
                                    class="inline-flex items-center px-4 py-2 bg-white border border-sky-500 text-sky-600 hover:bg-sky-50 text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver detalle
                                </button>
                                @if($ticket->respuesta)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-teal-100 text-teal-800 border border-teal-300">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Respondido
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($totalTickets > 3)
                    <div class="mt-8 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Mostrando {{ $tickets->count() }} de {{ $totalTickets }} resultados
                        </div>
                        <div class="flex items-center space-x-2">
                            <!-- Botón Anterior -->
                            @if($currentPage > 1)
                                <button 
                                    wire:click="previousPage"
                                    class="inline-flex items-center px-3 py-2 bg-white border border-sky-600 text-sky-600 hover:bg-sky-50 text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Anterior
                                </button>
                            @else
                                <button 
                                    disabled
                                    class="inline-flex items-center px-3 py-2 bg-gray-100 border border-gray-200 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Anterior
                                </button>
                            @endif

                            <!-- Números de página -->
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= $lastPage; $i++)
                                    @if($i == $currentPage)
                                        <button 
                                            disabled
                                            class="inline-flex items-center justify-center w-8 h-8 bg-sky-600 text-white text-sm font-medium rounded-lg">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button 
                                            wire:click="goToPage({{ $i }})"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                            {{ $i }}
                                        </button>
                                    @endif
                                @endfor
                            </div>

                            <!-- Botón Siguiente -->
                            @if($currentPage < $lastPage)
                                <button 
                                    wire:click="nextPage"
                                    class="inline-flex items-center px-3 py-2 bg-white border border-sky-600 text-sky-600 hover:bg-sky-50 text-sm font-medium rounded-lg transition-colors">
                                    Siguiente
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @else
                                <button 
                                    disabled
                                    class="inline-flex items-center px-3 py-2 bg-gray-100 border border-gray-200 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">
                                    Siguiente
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Modal de detalle del ticket con efecto blur --}}
    @if($mostrarDetalle && $ticketSeleccionado)
    <div class="fixed inset-0 flex items-center justify-center z-50 p-4 overflow-y-auto" 
         style="background-color: rgba(0, 0, 0, 0.25); backdrop-filter: blur(4px);" 
         wire:click.self="cerrarDetalle">
        <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full my-8 animate-scale-in border border-gray-200">
            <!-- Header del modal -->
            <div class="bg-sky-50 px-8 py-6 rounded-t-lg border-b border-sky-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-sky-500 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Ticket TK-{{ str_pad($ticketSeleccionado->correlativo, 6, '0', STR_PAD_LEFT) }}</h2>
                            <p class="text-gray-600 text-sm mt-1">Detalle completo de la solicitud</p>
                        </div>
                    </div>
                    <button 
                        wire:click="cerrarDetalle" 
                        class="text-gray-600 hover:text-gray-800 bg-white hover:bg-gray-50 rounded-lg p-2 transition-colors border border-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-8 max-h-[calc(100vh-200px)] overflow-y-auto">
                {{-- Estado actual --}}
                <div class="mb-8">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-4">
                                <div class="w-14 h-14 rounded-full flex items-center justify-center
                                    @if($this->getEstadoColor($ticketSeleccionado->estado) === 'success') bg-green-500
                                    @elseif($this->getEstadoColor($ticketSeleccionado->estado) === 'warning') bg-yellow-500
                                    @elseif($this->getEstadoColor($ticketSeleccionado->estado) === 'danger') bg-red-500
                                    @elseif($this->getEstadoColor($ticketSeleccionado->estado) === 'info') bg-blue-500
                                    @else bg-gray-500
                                    @endif">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Estado actual</p>
                                <h3 class="text-2xl font-bold 
                                    @if($this->getEstadoColor($ticketSeleccionado->estado) === 'success') text-green-700
                                    @elseif($this->getEstadoColor($ticketSeleccionado->estado) === 'warning') text-yellow-700
                                    @elseif($this->getEstadoColor($ticketSeleccionado->estado) === 'danger') text-red-700
                                    @elseif($this->getEstadoColor($ticketSeleccionado->estado) === 'info') text-blue-700
                                    @else text-gray-700
                                    @endif">
                                    {{ $this->getEstadoNombre($ticketSeleccionado->estado) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Información del solicitante --}}
                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Solicitante
                            </h4>
                        </div>
                        <div class="p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Nombre completo</p>
                                <p class="text-sm font-medium text-gray-900">{{ $ticketSeleccionado->nombres }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Área</p>
                                <p class="text-sm text-gray-900">{{ $ticketSeleccionado->area->nombre ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Registrado por</p>
                                <p class="text-sm text-gray-900">{{ $ticketSeleccionado->usuario_creacion }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Fecha de creación</p>
                                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($ticketSeleccionado->fecha_creacion)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Técnico asignado --}}
                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Técnico asignado
                            </h4>
                        </div>
                        <div class="p-4">
                            @if($ticketSeleccionado->usuario)
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Nombre</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $ticketSeleccionado->usuario->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Correo electrónico</p>
                                        <p class="text-sm text-gray-900 break-all">{{ $ticketSeleccionado->usuario->email }}</p>
                                    </div>
                                    @if($ticketSeleccionado->usuario->tipoUsuarioRol)
                                    <div>
                                        <p class="text-xs text-gray-500">Rol</p>
                                        <span class="inline-block mt-1 px-3 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-300">
                                            {{ $ticketSeleccionado->usuario->tipoUsuarioRol->nombre }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <p class="text-sm text-gray-500">Sin asignar</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Categorización --}}
                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Categorización
                            </h4>
                        </div>
                        <div class="p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Categoría</p>
                                <p class="text-sm text-gray-900">{{ $ticketSeleccionado->categoria->nombre ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Subcategoría</p>
                                <p class="text-sm text-gray-900">{{ $ticketSeleccionado->subcategoria->nombre ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Número de ticket</p>
                                <span class="inline-flex items-center mt-1 px-4 py-2 rounded text-sm font-mono font-bold bg-gray-900 text-white">
                                    TK-{{ str_pad($ticketSeleccionado->correlativo, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Descripción completa --}}
                <div class="mt-6 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Descripción de la incidencia
                        </h4>
                    </div>
                    <div class="p-6">
                        <div class="bg-white rounded p-4 border border-gray-200 min-h-[100px]">
                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $ticketSeleccionado->descripcion }}</p>
                        </div>
                    </div>
                </div>

                {{-- Respuesta del ticket --}}
                @if($ticketSeleccionado->respuesta)
                    <div class="mt-6 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                Respuesta del soporte
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="bg-teal-50 rounded p-4 border border-teal-200 min-h-[100px]">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-3">
                                        <div class="w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap flex-1">{{ $ticketSeleccionado->respuesta }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Respuesta del soporte
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="text-center py-6 bg-white rounded border border-gray-200">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-gray-500">Sin respuesta aún</p>
                                <p class="text-xs text-gray-400 mt-1">El equipo está trabajando en su solicitud</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Anexos --}}
                @if($ticketSeleccionado->anexos && count($ticketSeleccionado->anexos) > 0)
                    <div class="mt-6 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Archivos adjuntos
                                <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs">
                                    {{ count($ticketSeleccionado->anexos) }}
                                </span>
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($ticketSeleccionado->anexos as $index => $anexo)
                                    <a 
                                        href="{{ Storage::url($anexo->ruta) }}" 
                                        target="_blank"
                                        class="group block">
                                        <div class="bg-white border border-gray-200 rounded p-4 hover:border-blue-400 hover:shadow transition-all">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-10 h-10 bg-blue-800 rounded flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">Archivo {{ $index + 1 }}</p>
                                                    <p class="text-xs text-gray-500 truncate">{{ basename($anexo->ruta) }}</p>
                                                </div>
                                                <div class="ml-3">
                                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer del modal -->
            <div class="bg-gray-50 border-t border-gray-200 px-8 py-4 rounded-b-lg flex justify-end">
                <button 
                    type="button" 
                    wire:click="cerrarDetalle" 
                    class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-medium rounded transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de Selección de Empleados con efecto blur -->
    @if($showEmpleadosModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 p-4" 
             style="background-color: rgba(0, 0, 0, 0.25); backdrop-filter: blur(4px);" 
             wire:click.self="cerrarModalEmpleados">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden border border-gray-200 animate-scale-in">
                <!-- Header -->
                <div class="bg-blue-900 px-6 py-4 flex justify-between items-center border-b border-blue-800">
                    <div>
                        <h3 class="text-lg font-bold text-white">Seleccionar empleado</h3>
                        <p class="text-blue-200 text-sm">{{ $empleadosEncontrados->count() }} resultados encontrados</p>
                    </div>
                    <button 
                        wire:click="cerrarModalEmpleados" 
                        class="text-blue-200 hover:text-white bg-blue-800 hover:bg-blue-700 rounded p-2 transition-colors border border-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Lista de empleados -->
                <div class="p-4 overflow-y-auto max-h-96 space-y-2">
                    @foreach($empleadosEncontrados as $empleado)
                        <button 
                            type="button"
                            wire:click="seleccionarEmpleadoParaBusqueda({{ $empleado->id }})"
                            class="w-full text-left p-4 bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-400 rounded transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 group-hover:text-blue-800">{{ $empleado->nombres }}</p>
                                    <p class="text-sm text-gray-500">{{ $empleado->area->nombre ?? 'Sin área asignada' }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes scale-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }

        .animate-scale-in {
            animation: scale-in 0.2s ease-out;
        }

        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }

        /* Scrollbar personalizada */
        .overflow-y-auto {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }

        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.7);
        }
    </style>
</div>