<div class="space-y-6" style="zoom: 90%">
    {{-- Header --}}
    {{-- <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Gestión de Tickets</h2>
        </div>

    </div> --}}

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg animate-fade-in">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
            </div>
        </div>
    @endif
    @if ($newTicketNotification)
        <div wire:poll.5s="hideNotification"
            class="fixed top-4 right-4 z-50 max-w-md bg-white rounded-lg shadow-2xl border-l-4 border-green-500 p-4 animate-fade-in">

            <div class="flex items-start">
                {{-- Icono --}}
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                {{-- Contenido --}}
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-gray-900">
                        🎫 Nuevo Ticket Creado
                    </h3>
                    <div class="mt-2 text-sm text-gray-600">
                        <p><strong>Correlativo:</strong> {{ $notificationData['correlativo'] ?? 'N/A' }}</p>
                        <p><strong>Área:</strong> {{ $notificationData['area'] ?? 'N/A' }}</p>
                        <p><strong>Categoría:</strong> {{ $notificationData['categoria'] ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Botón cerrar --}}
                <button wire:click="hideNotification" type="button"
                    class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 rounded-lg transition-colors">
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Barra de progreso (visual) --}}
            <div class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 w-full"></div>
            </div>
        </div>

        <style>
            @keyframes fadeIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }

                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.3s ease-out;
            }
        </style>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg animate-fade-in">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Filters Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Gestión de Tickets
                </h3>

                <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
                    <span class="text-sm text-gray-600">Total tickets:</span>
                    <span class="text-lg font-bold text-indigo-600 ml-2">{{ $tickets->total() }}</span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                {{-- Search Input --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Buscar ticket
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="Nombre, correlativo, descripción...">
                    </div>
                </div>

                {{-- Area Filter --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Área
                    </label>
                    <select wire:model.live="filterArea"
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Todas las áreas</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Categoria Filter --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipo Incidencia
                    </label>
                    <select wire:model.live="filterCategoria"
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Todas las incidencias</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Estado
                    </label>
                    <select wire:model.live="filterStatus"
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Per Page --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Por página
                    </label>
                    <select wire:model.live="perPage"
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            @if ($search || $filterStatus || $filterArea || $filterCategoria)
                <div class="mt-4 flex justify-end">
                    <button wire:click="clearFilters"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-150 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Limpiar filtros
                    </button>
                </div>
            @endif
        </div>
    </div>
    {{-- Tickets Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Correlativo
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Fecha
                        </th>
                        {{-- <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Nombre
                        </th> --}}
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Nombres / Área
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Tipo Incidencia
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Sub-Incidencia
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Usuario ASignado
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            T.Respuesta
                        </th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    #{{ $ticket->correlativo }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900">
                                    {{ \Carbon\Carbon::parse($ticket->fecha_creacion)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($ticket->fecha_creacion)->format('H:i') }}
                                </div>
                            </td>
                            {{-- <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $ticket->nombres }}</div>
                            </td> --}}
                            {{-- <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $ticket->area->nombre ?? 'N/A' }}
                                </span>
                            </td> --}}
                            <td class="px-4 py-4">
                                <div class="text-xs text-gray-600">{{ $ticket->nombres }}</div>
                                <div class="mt-1">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $ticket->area->nombre ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $ticket->categoriaIncidencia->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-cyan-100 text-cyan-800">
                                    {{ $ticket->subCategoriaIncidencia->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-700">
                                            {{ strtoupper(substr($ticket->usuario->username ?? 'N/A', 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $ticket->usuario->username ?? 'N/A' }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 leading-tight">
                                            {{ $ticket->usuario->name ?? '' }}
                                        </div>

                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $ticket->tipoEstado->nombre == 'Enviado' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $ticket->tipoEstado->nombre == 'Leido' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $ticket->tipoEstado->nombre == 'En proceso' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $ticket->tipoEstado->nombre == 'Desactivo' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $ticket->tipoEstado->nombre == 'Activo' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $ticket->tipoEstado->nombre == 'Solucionado' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ $ticket->tipoEstado->nombre }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if (!empty($ticket->tiempo_respuesta))
                                    @php
                                        $segundos = $ticket->tiempo_respuesta;
                                        $horas = floor($segundos / 3600);
                                        $minutos = floor(($segundos % 3600) / 60);
                                        $segs = $segundos % 60;

                                        if ($horas > 0) {
                                            $tiempo = $horas . 'h ' . $minutos . 'm';
                                        } elseif ($minutos > 0) {
                                            $tiempo = $minutos . 'm ' . $segs . 's';
                                        } else {
                                            $tiempo = $segs . 's';
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        {{ $tiempo }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                        N/A
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Edit Button --}}
                                    <button wire:click="edit({{ $ticket->id }})"
                                        class="inline-flex items-center px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-all duration-150 transform hover:scale-105"
                                        title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="verHistorial({{ $ticket->id }})"
                                        class="inline-flex items-center px-3 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg transition-all duration-150 transform hover:scale-105"
                                        title="Historial / Auditoría">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>

                                    {{-- Delete Button --}}
                                    {{-- <button wire:click="confirmDelete({{ $ticket->id }})"
                                        class="inline-flex items-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-all duration-150 transform hover:scale-105"
                                        title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">No se encontraron tickets</p>
                                    <p class="text-gray-400 text-sm mt-1">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Mostrando
                    <span class="font-semibold text-gray-900">{{ $tickets->firstItem() ?? 0 }}</span>
                    a
                    <span class="font-semibold text-gray-900">{{ $tickets->lastItem() ?? 0 }}</span>
                    de
                    <span class="font-semibold text-gray-900">{{ $tickets->total() }}</span>
                    tickets
                </div>
                <div>
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                {{-- Overlay oscuro - Debe estar DETRÁS del contenido --}}
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity -z-10" wire:click="closeModal">
                </div>


                <div
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full max-h-[100vh] overflow-y-auto">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar Ticket #{{ $correlativo }}
                            </h3>
                            <button wire:click="closeEditModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="bg-white">
                        <div class="flex flex-col lg:flex-row">
                            {{-- Columna Izquierda: Formulario --}}
                            <div style="zoom: 95%" class="flex-1 px-6 py-6 border-r border-gray-200">
                                <div class="space-y-6">
                                    {{-- Nombre --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Nombre del Solicitante 
                                        </label>
                                        <input type="text" wire:model="nombres" readonly
                                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100 text-gray-600"
                                            placeholder="Ingrese el nombre">
                                        @error('nombres')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Área y Usuario Asignado en 1 fila --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Área --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Área *
                                            </label>
                                            <select wire:model="id_area" disabled
                                                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100 text-gray-600">
                                                <option value="">Seleccione un área</option>
                                                @foreach ($areas as $area)
                                                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_area')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Usuario Asignado --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Usuario Asignado *
                                            </label>
                                            <select wire:model="id_usuario" {{ $is_view ? 'disabled' : '' }}
                                                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $is_view ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}">
                                                <option value="">Seleccione un usuario</option>
                                                @foreach ($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->name }} |
                                                        {{ $usuario->tipoUsuarioRol->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_usuario')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Tipo Incidencia y Sub Incidencia en 1 fila --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Tipo Incidencia --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Tipo de Incidencia *
                                            </label>
                                            <select wire:model.live="id_tipo_incidencia" disabled
                                                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100 text-gray-600">
                                                <option value="">Seleccione un tipo</option>
                                                @foreach ($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_tipo_incidencia')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Sub Incidencia --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Sub-Incidencia
                                            </label>
                                            <select wire:model="id_sub_incidencia" disabled
                                                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100 text-gray-600">
                                                <option value="">Seleccione una sub-incidencia</option>
                                                @foreach ($subcategorias as $subcategoria)
                                                    <option value="{{ $subcategoria->id }}">
                                                        {{ $subcategoria->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_sub_incidencia')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- Descripción --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Descripción *
                                        </label>
                                        <textarea wire:model="descripcion" rows="4" readonly
                                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100 text-gray-600"
                                            placeholder="Describa el problema o incidencia"></textarea>
                                        @error('descripcion')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

{{-- Estado --}}
<div x-data="{ estadoSeleccionado: @entangle('estado') }">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Estado del Ticket *
            </label>
            <select wire:model.live="estado" x-model="estadoSeleccionado"
                {{ $is_view ? 'disabled' : '' }}
                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $is_view ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}">
                <option value="">Seleccione un estado</option>
                @foreach ($estados as $est)
                    <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                @endforeach
            </select>
            @error('estado')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Estación Creación
            </label>
            <input type="text" wire:model="estacion_creacion" readonly
                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                placeholder="Estación de creación">
            @error('estacion_creacion')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Respuesta - Controlado por Alpine - FUERA del grid --}}
    <div class="mt-4" x-show="estadoSeleccionado == 5" x-cloak>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Respuesta *
        </label>
        <textarea wire:model="respuesta" rows="4" {{ $is_view ? 'readonly' : '' }}
            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $is_view ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}"
            placeholder="Describa la solución o respuesta al ticket"></textarea>
        @error('respuesta')
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>


                                    {{-- Respuesta --}}
                                    {{-- <div class="mt-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Respuesta * {{ $nombres }}
                                            </label>
                                            <textarea wire:model="respuesta" rows="4"
                                                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Describa la solución o respuesta al ticket"></textarea>
                                            @error('respuesta')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div> --}}

                                    {{-- Botones de Acción --}}
                                    <div
                                        class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200">
                                        <button type="button" wire:click="closeEditModal"
                                            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                            Cancelar
                                        </button>
                                        <button type="button" wire:click="update"
                                            @if ($is_view) disabled @endif
                                            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition-all
    {{ $is_view ? 'bg-gray-400 cursor-not-allowed opacity-60' : 'bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500' }}">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Columna Derecha: Anexos --}}
                            @if ($ticketId)
                                <div class="w-full lg:w-80 bg-gray-50 px-4 py-4 overflow-y-auto">
                                    <div class="mb-3">
                                        <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Archivos Adjuntos
                                        </h4>
                                    </div>
                                    @php
                                        $anexos = \App\Models\Anexos::where('id_ticket', $ticketId)->get();
                                    @endphp
                                    @if ($anexos->count() > 0)
                                        <div class="space-y-2">
                                            @foreach ($anexos as $anexo)
                                                @php
                                                    $extension = pathinfo($anexo->ruta, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                        'gif',
                                                        'webp',
                                                        'svg',
                                                    ]);
                                                    $isPdf = strtolower($extension) === 'pdf';
                                                    $fileName = basename($anexo->ruta);
                                                @endphp
                                                <div class="flex items-center gap-3 p-2 bg-white border border-gray-200 rounded-lg hover:shadow-sm hover:border-blue-300 transition-all cursor-pointer"
                                                    wire:click="$set('selectedAnexo', {{ $anexo->id }})">

                                                    {{-- Icono del archivo --}}
                                                    <div class="flex-shrink-0">
                                                        @if ($isImage)
                                                            <div class="w-10 h-10 rounded overflow-hidden">
                                                                <img src="{{ asset('storage/' . $anexo->ruta) }}"
                                                                    alt="Anexo" class="w-full h-full object-cover">
                                                            </div>
                                                        @elseif($isPdf)
                                                            <div
                                                                class="w-10 h-10 bg-red-100 rounded flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-red-600" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" />
                                                                </svg>
                                                            </div>
                                                        @else
                                                            <div
                                                                class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-gray-600" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Información del archivo --}}
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate"
                                                            title="{{ $fileName }}">
                                                            {{ Str::limit($fileName, 25) }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ strtoupper($extension) }}
                                                        </p>
                                                    </div>

                                                    {{-- Botón descargar --}}
                                                    <button wire:click.stop="downloadAnexo({{ $anexo->id }})"
                                                        class="flex-shrink-0 p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-all"
                                                        title="Descargar">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div
                                            class="text-center py-6 bg-white rounded-lg border-2 border-dashed border-gray-300">
                                            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-2 text-xs text-gray-500">No hay archivos adjuntos</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Modal de Previsualización --}}
                                @if ($selectedAnexo)
                                    @php
                                        $anexo = \App\Models\Anexos::find($selectedAnexo);
                                        $extension = pathinfo($anexo->ruta, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), [
                                            'jpg',
                                            'jpeg',
                                            'png',
                                            'gif',
                                            'webp',
                                            'svg',
                                        ]);
                                        $isPdf = strtolower($extension) === 'pdf';
                                        $fileName = basename($anexo->ruta);
                                    @endphp
                                    <div class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4 bg-black bg-opacity-75"
                                        wire:click="$set('selectedAnexo', null)">
                                        <div class="bg-white rounded-lg shadow-2xl max-w-6xl w-full max-h-[90vh] flex flex-col"
                                            wire:click.stop>

                                            {{-- Header del Modal --}}
                                            <div
                                                class="flex items-center justify-between p-4 border-b border-gray-200">
                                                <div class="flex items-center space-x-3">
                                                    <span
                                                        class="px-3 py-1 text-sm font-semibold bg-blue-100 text-blue-800 rounded-md uppercase">
                                                        {{ $extension }}
                                                    </span>
                                                    <h3 class="text-lg font-semibold text-gray-900 truncate max-w-md"
                                                        title="{{ $fileName }}">
                                                        {{ $fileName }}
                                                    </h3>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ asset('storage/' . $anexo->ruta) }}"
                                                        download="{{ $fileName }}"
                                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center space-x-2">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                        <span>Descargar</span>
                                                    </a>
                                                    <button wire:click="$set('selectedAnexo', null)"
                                                        class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-all">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Contenido del Modal --}}
                                            <div class="flex-1 overflow-auto p-4 bg-gray-50">
                                                @if ($isImage)
                                                    <div class="flex items-center justify-center min-h-full">
                                                        <img src="{{ asset('storage/' . $anexo->ruta) }}"
                                                            alt="Vista previa"
                                                            class="max-w-full max-h-full object-contain rounded-lg shadow-lg">
                                                    </div>
                                                @elseif($isPdf)
                                                    <iframe src="{{ asset('storage/' . $anexo->ruta) }}"
                                                        class="w-full h-full min-h-[600px] rounded-lg shadow-lg"
                                                        frameborder="0">
                                                    </iframe>
                                                @else
                                                    <div
                                                        class="flex flex-col items-center justify-center min-h-[400px] text-center">
                                                        <svg class="w-24 h-24 text-gray-400 mb-4" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                        </svg>
                                                        <p class="text-lg font-semibold text-gray-700 mb-2">Vista
                                                            previa no disponible</p>
                                                        <p class="text-sm text-gray-500 mb-4">Este tipo de archivo no
                                                            puede ser visualizado en el navegador</p>
                                                        <a href="{{ asset('storage/' . $anexo->ruta) }}"
                                                            download="{{ $fileName }}"
                                                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all inline-flex items-center space-x-2">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                            <span>Descargar Archivo</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- Modal Historial/Auditoría --}}
    @if ($showHistorialModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">

                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity -z-10" aria-hidden="true"
                    wire:click="closeHistorialModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Container -->
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">

                    <!-- Header -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-amber-100">
                                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        Historial de Auditoría - Ticket #{{ $ticketHistorialId }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Selecciona un registro para ver sus detalles
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeHistorialModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content: Lista + Detalles -->
                    <div class="bg-gray-50">
                        @if ($historialTicket->count() > 0)
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0" style="height: 70vh;">

                                <!-- Lista de Registros (Izquierda) -->
                                <div class="bg-white border-r border-gray-200 overflow-y-auto">
                                    <div class="p-4">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                            Registros ({{ $historialTicket->count() }})
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach ($historialTicket as $index => $auditoria)
                                                <div wire:click="$set('selectedAuditoriaIndex', {{ $index }})"
                                                    class="p-3 rounded-lg border-l-4 cursor-pointer transition-all
                                                {{ $selectedAuditoriaIndex == $index
                                                    ? 'bg-amber-50 border-amber-500 shadow-md'
                                                    : 'bg-gray-50 border-gray-300 hover:bg-gray-100' }}
                                                {{ $auditoria->accion == 'CREATE'
                                                    ? ($selectedAuditoriaIndex == $index
                                                        ? 'border-green-500'
                                                        : 'border-l-green-300')
                                                    : ($auditoria->accion == 'UPDATE'
                                                        ? ($selectedAuditoriaIndex == $index
                                                            ? 'border-blue-500'
                                                            : 'border-l-blue-300')
                                                        : ($selectedAuditoriaIndex == $index
                                                            ? 'border-red-500'
                                                            : 'border-l-red-300')) }}">

                                                    <div class="flex items-center justify-between mb-2">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                        {{ $auditoria->accion == 'CREATE'
                                                            ? 'bg-green-100 text-green-800'
                                                            : ($auditoria->accion == 'UPDATE'
                                                                ? 'bg-blue-100 text-blue-800'
                                                                : 'bg-red-100 text-red-800') }}">
                                                            {{ $auditoria->accion }}
                                                        </span>
                                                        <span class="text-xs text-gray-500">
                                                            {{ \Carbon\Carbon::parse($auditoria->fecha_auditoria)->format('d/m/Y H:i') }}
                                                        </span>
                                                    </div>

                                                    <div class="text-sm">
                                                        <p class="font-medium text-gray-900">
                                                            Usuario:
                                                            {{ optional($auditoria->usuarioAuditoria)->name ?? 'Sistema' }}
                                                        </p>
                                                        <p class="text-gray-600 text-xs mt-1">
                                                            {{ $auditoria->estado_nombre }} •
                                                            {{ $auditoria->area_nombre }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Panel de Detalles (Derecha) -->
                                <div class="bg-gray-50 overflow-y-auto">
                                    @if (isset($selectedAuditoriaIndex) && isset($historialTicket[$selectedAuditoriaIndex]))
                                        @php
                                            $auditoria = $historialTicket[$selectedAuditoriaIndex];
                                        @endphp

                                        <div class="p-6" style="zoom: 90%">
                                            <!-- Encabezado del detalle -->
                                            <div class="mb-6 pb-4 border-b border-gray-200">
                                                <div class="flex items-center justify-between mb-3">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                    {{ $auditoria->accion == 'CREATE'
                                                        ? 'bg-green-100 text-green-800'
                                                        : ($auditoria->accion == 'UPDATE'
                                                            ? 'bg-blue-100 text-blue-800'
                                                            : 'bg-red-100 text-red-800') }}">
                                                        {{ $auditoria->accion }}
                                                    </span>
                                                    <span class="text-sm text-gray-500">
                                                        {{ \Carbon\Carbon::parse($auditoria->fecha_auditoria)->format('d/m/Y H:i:s') }}
                                                    </span>
                                                </div>
                                                <h4 class="text-lg font-semibold text-gray-900">
                                                    Usuario:
                                                    {{ optional($auditoria->usuarioAuditoria)->name ?? 'Sistema' }}
                                                </h4>
                                            </div>

                                            <!-- Datos del registro -->
                                            <div class="space-y-4">
                                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                                    <h5
                                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                                                        Información General
                                                    </h5>
                                                    <dl class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Correlativo
                                                            </dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{ $auditoria->correlativo }}</dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Área</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{ $auditoria->area_nombre }}</dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Usuario
                                                                Reporta</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{ $auditoria->nombres }}</dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                                            <dd class="mt-1">
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                {{ $auditoria->estado == 1
                                                                    ? 'bg-yellow-100 text-yellow-800'
                                                                    : ($auditoria->estado == 2
                                                                        ? 'bg-blue-100 text-blue-800'
                                                                        : ($auditoria->estado == 3
                                                                            ? 'bg-green-100 text-green-800'
                                                                            : 'bg-gray-100 text-gray-800')) }}">
                                                                    {{ $auditoria->estado_nombre }}
                                                                </span>
                                                            </dd>
                                                        </div>
                                                        <div class="col-span-2">
                                                            <dt class="text-sm font-medium text-gray-500">Tipo
                                                                Incidencia</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{ $auditoria->tipo_incidencia_nombre }}
                                                                @if ($auditoria->sub_incidencia_nombre)
                                                                    <span class="text-gray-500">/
                                                                        {{ $auditoria->sub_incidencia_nombre }}</span>
                                                                @endif
                                                            </dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Asignado a
                                                            </dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{ $auditoria->usuario_nombre }}</dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Estación</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{ $auditoria->estacion_creacion }}</dd>
                                                        </div>
                                                    </dl>
                                                </div>

                                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                                    <h5
                                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                                                        Descripción
                                                    </h5>
                                                    <p class="text-sm text-gray-700 leading-relaxed">
                                                        {{ $auditoria->descripcion }}</p>
                                                </div>

                                                @if ($auditoria->respuesta)
                                                    <div
                                                        class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-green-500">
                                                        <h5
                                                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                                                            Respuesta
                                                        </h5>
                                                        <p class="text-sm text-gray-700 leading-relaxed">
                                                            {{ $auditoria->respuesta }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center h-full p-6">
                                            <div class="text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-500">
                                                    Selecciona un registro de la lista para ver sus detalles
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No hay registros de auditoría para este ticket
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 border-t border-gray-200 flex justify-end">
                        <button type="button" wire:click="closeHistorialModal"
                            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="cancelDelete">
                </div>

                <div
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-12 sm:w-12">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Confirmar eliminación
                                </h3>
                                <div class="mt-3">
                                    <p class="text-sm text-gray-600">
                                        ¿Estás seguro de que deseas eliminar este ticket?
                                    </p>
                                    <p class="text-sm text-red-600 font-semibold mt-2">
                                        ⚠️ Esta acción no se puede deshacer y se eliminarán todos los archivos adjuntos.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" wire:click="cancelDelete"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="deleteTicket"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar ticket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- Scripts para manejar eventos --}}
    @script
        <script>
            console.log('🎬 [BLADE] Script de admin/ticket cargado');

            // Función para actualizar timestamp de forma segura
            function updateTimestamp() {
                const element = document.getElementById('last-update');
                if (element) {
                    element.textContent = new Date().toLocaleTimeString('es-PE');
                    console.log('⏰ [TIMESTAMP] Actualizado');
                } else {
                    console.warn('⚠️ [TIMESTAMP] Elemento no encontrado en DOM');
                }
            }

            // Escuchar evento personalizado de Echo
            window.addEventListener('ticket-created-from-echo', (event) => {
                console.log('🎯 [BLADE] Evento capturado desde Echo:', event.detail);

                // Enviar a Livewire con el formato correcto
                try {
                    $wire.call('handleNewTicketFromJs', event.detail);
                    console.log('📤 [BLADE] Evento enviado a Livewire vía call()');
                } catch (error) {
                    console.error('❌ [BLADE] Error al enviar a Livewire:', error);
                }

                // Actualizar timestamp
                updateTimestamp();
            });

            // Escuchar toast de Livewire
            Livewire.on('show-toast', (event) => {
                console.log('🍞 [TOAST] Evento recibido:', event);

                const data = event[0] || event;
                const message = data.message || data[0] || 'Nuevo ticket ';

                console.log('🍞 [TOAST] Mostrando:', message);

                // Crear notificación temporal
                const toast = document.createElement('div');
                toast.className =
                    'fixed bottom-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            });

            console.log('✅ [BLADE] Listeners configurados');

            // Actualizar timestamp inicial
            updateTimestamp();


            const notificationSound = new Audio('/sounds/notification.mp3'); // O usa un CDN

            // Escuchar el evento de Livewire
            Livewire.on('play-notification-sound', () => {
                console.log("EJecutando Sonido")
                notificationSound.play().catch(error => {
                    console.error('Error al reproducir sonido:', error);
                });
            });
        </script>
    @endscript
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Custom scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</div>
