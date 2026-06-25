<div>
    {{-- Polling cada 30 segundos --}}
    <div wire:poll.30s="pollAlerts"></div>
    
    {{-- Alerta flotante --}}
    @if($showAlert)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 10000)" x-show="show" 
             class="fixed top-20 right-4 z-[100] max-w-md animate-slide-in">
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
                
                {{-- Barra de progreso --}}
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
    
    {{-- ESTILOS DENTRO DEL MISMO ELEMENTO RAÍZ --}}
    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        .animate-progress {
            animation: progress 10s linear forwards;
        }
    </style>
</div>