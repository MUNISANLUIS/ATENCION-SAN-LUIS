    <div class="space-y-6" style="zoom: 90%">

        {{-- ══════════════════════════════════════════
            MENSAJES DE SESIÓN
        ══════════════════════════════════════════ --}}
        @if (session()->has('message'))
            <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- ══════════════════════════════════════════
            CARD FILTROS
        ══════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Reporte de Tickets
                    </h3>
                    <button wire:click="exportarExcel"
                        class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Exportar Excel
                    </button>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="p-6 space-y-4">

                {{-- Fila 1: Fechas + Área + Tipo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Fecha Inicio --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha Desde</label>
                        <input type="date" wire:model.live="fechaInicio"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>

                    {{-- Fecha Fin --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha Hasta</label>
                        <input type="date" wire:model.live="fechaFin"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>

                    {{-- Área --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Área</label>
                        <select wire:model.live="idArea"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="">Todas</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tipo Incidencia --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo Incidencia</label>
                        <select wire:model.live="idTipoIncidencia"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="">Todas</option>
                            @foreach ($tiposIncidencia as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Fila 2: Estados con pills/checkboxes --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Estados</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tiposEstado as $estado)
                            @php
                                $checked = in_array((string)$estado->id, $estadosSeleccionados);
                                [$bgOn, $bgOff, $checkColor] = match($estado->nombre) {
                                    'Anulado'    => ['bg-red-600 text-white border-red-700',       'bg-white text-red-700 border-red-300',      'text-red-600'],
                                    'Activo'     => ['bg-indigo-600 text-white border-indigo-700', 'bg-white text-indigo-700 border-indigo-300','text-indigo-600'],
                                    'Sin Leer'   => ['bg-gray-600 text-white border-gray-700',     'bg-white text-gray-700 border-gray-300',    'text-gray-600'],
                                    'En proceso' => ['bg-yellow-500 text-white border-yellow-600', 'bg-white text-yellow-700 border-yellow-300','text-yellow-500'],
                                    'Atendido'   => ['bg-green-600 text-white border-green-700',   'bg-white text-green-700 border-green-300',  'text-green-600'],
                                    'Leido'      => ['bg-blue-600 text-white border-blue-700',     'bg-white text-blue-700 border-blue-300',    'text-blue-600'],
                                    default      => ['bg-gray-700 text-white border-gray-800',     'bg-white text-gray-700 border-gray-300',    'text-gray-600'],
                                };
                                $pillClass = $checked ? $bgOn : $bgOff;
                            @endphp
                            <button
                                wire:click="toggleEstado({{ $estado->id }})"
                                type="button"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border text-xs font-semibold transition-all duration-150 cursor-pointer select-none shadow-sm hover:shadow {{ $pillClass }}">

                                {{-- Checkbox visual --}}
                                <span class="inline-flex items-center justify-center w-4 h-4 rounded border-2 flex-shrink-0 bg-white
                                    {{ $checked ? 'border-white' : 'border-gray-400' }}">
                                    @if($checked)
                                        <svg class="w-3 h-3 {{ $checkColor }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </span>

                                {{ $estado->nombre }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Botón limpiar --}}
                <div class="flex justify-end">
                    <button wire:click="limpiarFiltros"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
            ESTADÍSTICAS DINÁMICAS
        ══════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

            {{-- Total siempre visible --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500">Total</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tickets->count() }}</p>
            </div>

            {{-- Una tarjeta por cada estado seleccionado --}}
            @foreach ($tiposEstado as $estado)
                @if(in_array((string)$estado->id, $estadosSeleccionados))
                    @php
                        $cnt = $tickets->filter(fn($t) => optional($t->tipoEstado)->id == $estado->id)->count();
                        [$cardBg, $numColor] = match($estado->nombre) {
                            'Anulado'    => ['bg-red-50 border-red-200',    'text-red-700'],
                            'Activo'     => ['bg-indigo-50 border-indigo-200', 'text-indigo-700'],
                            'Sin Leer'   => ['bg-gray-50 border-gray-200',  'text-gray-700'],
                            'En proceso' => ['bg-yellow-50 border-yellow-200','text-yellow-700'],
                            'Atendido'   => ['bg-green-50 border-green-200',  'text-green-700'],
                            'Leido'      => ['bg-blue-50 border-blue-200',   'text-blue-700'],
                            default      => ['bg-gray-50 border-gray-200',  'text-gray-700'],
                        };
                    @endphp
                    <div class="{{ $cardBg }} rounded-xl shadow-sm border p-4">
                        <p class="text-xs font-medium text-gray-500">{{ $estado->nombre }}</p>
                        <p class="text-2xl font-bold {{ $numColor }} mt-1">{{ $cnt }}</p>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════
            TABLA DE TICKETS
        ══════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Correlativo</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Solicitante</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Área</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Incidencia</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-indigo-600">#{{ $ticket->correlativo }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $ticket->nombres }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $ticket->area->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $ticket->categoriaIncidencia->nombre ?? 'N/A' }}</div>
                                    @if ($ticket->subCategoriaIncidencia)
                                        <div class="text-xs text-gray-500">{{ $ticket->subCategoriaIncidencia->nombre }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $estadoNombre = $ticket->tipoEstado->nombre ?? 'N/A';
                                        $estadoClass  = match($estadoNombre) {
                                            'En proceso' => 'bg-yellow-100 text-yellow-800',
                                            'Atendido'   => 'bg-green-100 text-green-800',
                                            'Leido'      => 'bg-blue-100 text-blue-800',
                                            'Sin Leer'   => 'bg-gray-100 text-gray-800',
                                            'Anulado'    => 'bg-red-100 text-red-800',
                                            'Activo'     => 'bg-indigo-100 text-indigo-800',
                                            default      => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $estadoClass }}">
                                        {{ $estadoNombre }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($ticket->fecha_creacion)->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
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
        </div>

    </div>