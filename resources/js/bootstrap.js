import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;



try {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });


    // TEST: Listener de prueba en JavaScript puro
    window.Echo.channel('tickets')
        .listen('.ticket.created', (e) => {
            console.log('🎉 [FRONTEND JS] Evento recibido:', e);

            // DISPARAR EVENTO PERSONALIZADO PARA LIVEWIRE
            window.dispatchEvent(new CustomEvent('ticket-created-from-echo', {
                detail: e
            }));

        });


} catch (error) {
    console.error('❌ [ERROR] Al configurar Echo:', error);
}
