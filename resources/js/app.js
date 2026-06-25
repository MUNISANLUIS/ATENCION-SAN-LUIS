import './bootstrap';
import Echo from 'laravel-echo';

// Verificar que Echo esté disponible
if (window.Echo) {

    // Configurar Livewire para usar Echo
    if (window.Livewire) {

        // Exponer Echo a Livewire
        window.Livewire.hook('component.init', ({ component }) => {
            console.log('🔌 [LIVEWIRE] Componente inicializado:', component.name);
        });
    }
}
