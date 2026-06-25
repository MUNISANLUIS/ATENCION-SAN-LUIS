<div class="min-h-screen bg-green-100 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header decorativo -->
        <div class="text-center mb-4 sm:mb-6">
            <div class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-sky-600 rounded-lg mb-3 shadow-sm">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-2">Nuevo Ticket de Incidencia</h1>
            <p class="text-gray-700 text-sm sm:text-base">
                Complete el formulario para registrar una nueva incidencia
            </p>
        </div>

        <!-- FORMULARIO -->
        <div class="rounded-lg border border-gray-300 bg-white shadow p-4 sm:p-6 lg:p-8 space-y-4">
            <!-- EMPLEADO -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-800">
                    Empleado <span class="text-sky-600">*</span>
                </label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input 
                        type="text" 
                        wire:model="busquedaEmpleado"
                        wire:keydown.enter="buscarEmpleado"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-600 focus:border-sky-600 bg-white"
                        placeholder="Ingrese el nombre del empleado (mínimo 3 caracteres)">
                    
                    <button 
                        type="button"
                        wire:click="buscarEmpleado"
                        class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm shadow whitespace-nowrap">
                        Buscar
                    </button>
                </div>

                @if($empleadoSeleccionado)
                    <div class="mt-2 p-2 bg-emerald-50 border border-emerald-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-emerald-900 text-sm">{{ $empleadoSeleccionado->nombres }}</p>
                                <p class="text-xs text-emerald-700">{{ $empleadoSeleccionado->area->nombre ?? 'Sin área asignada' }}</p>
                            </div>
                            <button wire:click="limpiarEmpleado" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">
                                Cambiar
                            </button>
                        </div>
                    </div>
                @endif

                @if($empleadoNoEncontrado)
                    <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-700 text-xs">Empleado no encontrado. Verifique el nombre e intente nuevamente.</p>
                    </div>
                @endif

                @error('empleadoSeleccionado')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- TIPO Y SUBTIPO -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

                <div>
                    <label class="block text-sm font-medium text-gray-800">
                        Tipo de Incidencia <span class="text-sky-600">*</span>
                    </label>
                    <select wire:model.live="id_tipo_incidencia"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-600 focus:border-sky-600 bg-white">
                        <option value="">Seleccionar tipo</option>
                        @foreach($tiposIncidencia as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    @error('id_tipo_incidencia')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800">
                        Subtipo de Incidencia <span class="text-sky-600">*</span>
                    </label>
                    <select wire:model="id_sub_incidencia"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-600 focus:border-sky-600 bg-white disabled:bg-gray-100"
                        @if(empty($subTiposIncidencia)) disabled @endif>
                        <option value="">Seleccionar subtipo</option>
                        @foreach($subTiposIncidencia as $subTipo)
                            <option value="{{ $subTipo->id }}">{{ $subTipo->nombre }}</option>
                        @endforeach
                    </select>
                    @error('id_sub_incidencia')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- DESCRIPCIÓN -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-800">
                    Descripción <span class="text-sky-600">*</span>
                </label>
                <textarea
                    wire:model="descripcion"
                    rows="4"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-600 focus:border-sky-600 bg-white resize-none"
                    placeholder="Describa detalladamente la incidencia presentada...">
                </textarea>
                @error('descripcion')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- ANEXOS -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-800">
                    Archivos Adjuntos (Opcional)
                </label>
                <div class="border border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-sky-400 transition-colors bg-gray-50">
                    <input type="file" wire:model="anexos" multiple class="w-full">
                    <p class="text-xs text-gray-600 mt-2">Máximo 50MB por archivo</p>
                </div>

                @if (!empty($anexos))
                    <div class="space-y-2 mt-3">
                        @foreach($anexos as $index => $anexo)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-gray-50 p-3 rounded border border-gray-300">
                                <span class="text-xs text-gray-800 mb-2 sm:mb-0 truncate">{{ $anexo->getClientOriginalName() }}</span>
                                <button wire:click="removeAnexo({{ $index }})" class="text-red-600 hover:text-red-800 text-xs font-medium whitespace-nowrap">
                                    Eliminar
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- BOTÓN DE ENVÍO -->
            <div class="pt-4 border-t border-gray-300">
                <div class="flex justify-center sm:justify-end">
                    <button
                        type="button"
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        wire:target="anexos, submit"
                        class="w-full sm:w-auto bg-sky-600 hover:bg-sky-700 text-white font-medium px-8 py-3 rounded-lg transition disabled:opacity-50 shadow">

                        <span wire:loading.remove wire:target="anexos, submit">
                            Enviar Ticket
                        </span>

                        <span wire:loading wire:target="anexos, submit">
                            Procesando...
                        </span>
                    </button>
                </div>
            </div>

        </div>

        <!-- MENSAJE DE ERROR -->
        @if (session()->has('error'))
            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-700 font-medium text-sm">{{ session('error') }}</p>
            </div>
        @endif

    </div>

    @include('livewire.public.parts.ticket-success-modal')

    <!-- Botón Flotante de Ayuda -->
    <button
        type="button"
        wire:click="toggleHelpModal"
        class="fixed bottom-6 right-6 w-14 h-14 bg-sky-600 hover:bg-sky-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-110 focus:outline-none focus:ring-4 focus:ring-sky-300 z-50">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </button>

    <!-- Modal de Ayuda -->
    @if($showHelpModal)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 border border-gray-200">
                <!-- Header Minimalista -->
                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-sky-50 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-base font-medium text-gray-900">Guía Rápida</h3>
                        </div>
                        <button wire:click="toggleHelpModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Contenido Minimalista -->
                <div class="px-6 py-5 space-y-4">
                    <div class="space-y-3">
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center text-xs font-medium">1</span>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">Seleccione el <span class="font-medium">empleado</span> que reporta la incidencia</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center text-xs font-medium">2</span>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">Elija el <span class="font-medium">tipo y subtipo</span> de incidencia</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center text-xs font-medium">3</span>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">Describa <span class="font-medium">detalladamente</span> el problema</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center text-xs font-medium">4</span>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">Adjunte <span class="font-medium">archivos</span> como evidencia (opcional)</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center text-xs font-medium">5</span>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">Haga clic en <span class="font-medium">"Enviar Ticket"</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Minimalista -->
                <div class="border-t border-gray-100 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <a href="{{ asset('Manual_Usuario_Sistemas_De_Tickets.pdf') }}" 
                           download="Manual_Usuario_Sistemas_De_Tickets.pdf"
                           class="inline-flex items-center gap-2 text-sm text-sky-600 hover:text-sky-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Descargar manual completo</span>
                        </a>
                        <button wire:click="toggleHelpModal" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de Selección de Empleados -->
    @if($showEmpleadosModal)
        <div class="fixed inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[80vh] overflow-hidden">
                <!-- Header -->
                <div class="bg-sky-600 p-4 flex justify-between items-center shadow">
                    <div class="text-white">
                        <h3 class="font-semibold">Seleccionar Empleado</h3>
                        <p class="text-sky-100 text-sm">Se encontraron {{ $empleadosEncontrados->count() }} resultados</p>
                    </div>
                    <button 
                        wire:click="cerrarModalEmpleados" 
                        class="text-white hover:bg-white/20 rounded-full p-1 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Lista de empleados -->
                <div class="p-4 overflow-y-auto max-h-96 space-y-2">
                    @foreach($empleadosEncontrados as $empleado)
                        <button 
                            type="button"
                            wire:click="seleccionarEmpleado({{ $empleado->id }})"
                            class="w-full text-left p-3 bg-gray-50 hover:bg-sky-50 border border-gray-400 hover:border-sky-400 rounded-lg transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 hover:text-sky-700">{{ $empleado->nombres }}</p>
                                    <p class="text-sm text-gray-700">{{ $empleado->area->nombre ?? 'Sin área asignada' }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</div>