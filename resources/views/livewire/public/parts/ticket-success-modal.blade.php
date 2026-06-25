@if($showSuccessModal)
<div 
    class="fixed inset-0 bg-slate-900/50 flex items-center justify-center z-50 backdrop-blur-sm p-4"
    wire:click="closeModal"  {{-- CLICK FUERA CIERRA --}}
>

    <div 
        class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-gray-300"
        wire:click.stop  {{-- EVITA QUE SE CIERRE AL HACER CLICK DENTRO --}}
    >

        <!-- Header -->
        <div class="bg-sky-50 text-center py-6 px-6 border-b border-sky-200">
            <div class="flex items-center justify-center mb-3">
                <div class="bg-sky-600 p-3 rounded-full shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">
                Municipalidad Distrital
            </h2>
            <p class="text-sm text-gray-700 mt-1">
                Constancia de Registro de Solicitud
            </p>
        </div>

        <!-- Cuerpo -->
        <div class="p-8 bg-gradient-to-b from-white to-gray-50">

            <div class="text-center mb-6">
                <p class="text-gray-600 text-xs uppercase tracking-widest">
                    Número de Ticket
                </p>

                <div class="mt-3 border-2 border-sky-200 rounded-lg py-5 bg-white shadow-inner">
                    <p class="text-3xl font-bold tracking-widest text-sky-600">
                        TK-{{ $ticketCorrelativo }}
                    </p>
                </div>

                <p class="text-xs text-gray-600 mt-3">
                    Conserve este número para el seguimiento de su trámite.
                </p>
            </div>

            <div class="border-t border-gray-300 my-6"></div>

            <div class="text-sm text-gray-800 space-y-2 mb-6">
                <div class="flex justify-between">
                    <span class="font-medium">Estado:</span>
                    <span class="text-green-700 font-semibold">Registrado</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Fecha:</span>
                    <span>{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Hora:</span>
                    <span>{{ now()->format('H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">N° de Resolución:</span>
                    <span class="text-sky-600 font-semibold">R-{{ str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT) }}-{{ date('Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Periodo:</span>
                    <span class="text-gray-700">{{ date('Y') }}</span>
                </div>
            </div>

            <div class="space-y-3">
                <button
                    wire:click="irASeguimiento"
                    class="w-full bg-sky-600 hover:bg-sky-700 text-white font-semibold py-3 rounded-lg transition shadow-sm">
                    Seguimiento de Solicitud
                </button>

                <button
                    wire:click="closeModal"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 rounded-lg transition">
                    Registrar Nueva Solicitud
                </button>
            </div>

        </div>

    </div>
</div>
@endif