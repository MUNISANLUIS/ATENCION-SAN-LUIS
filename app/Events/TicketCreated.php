<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TicketCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {

        try {
            $this->ticket = $ticket->load([
                'area:id,nombre,abreviatura',
                'categoriaIncidencia:id,nombre',
                'subCategoriaIncidencia:id,nombre',
                'tipoEstado:id,nombre',
                'anexos:id,id_ticket,ruta'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ [ERROR PASO 2] Error al cargar relaciones', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function broadcastOn()
    {
        $channel = new Channel('tickets');


        return $channel;
    }

    public function broadcastAs()
    {

        return 'ticket.created';
    }

    public function broadcastWith()
    {
        try {
            $data = [
                'ticket' => [
                    'id' => $this->ticket->id,
                    'correlativo' => $this->ticket->correlativo,
                    'area' => $this->ticket->area->nombre ?? 'N/A',
                    'area_abreviatura' => $this->ticket->area->abreviatura ?? 'N/A',
                    'categoria' => $this->ticket->categoriaIncidencia->nombre ?? 'N/A',
                    'subcategoria' => $this->ticket->subCategoriaIncidencia->nombre ?? 'N/A',
                    'descripcion' => $this->ticket->descripcion,
                    'estado' => $this->ticket->tipoEstado->nombre ?? 'Pendiente',
                    'estado_id' => $this->ticket->estado,
                    'id_usuario' => $this->ticket->id_usuario,
                    'usuario_creacion' => $this->ticket->usuario_creacion,
                    'anexos_count' => $this->ticket->anexos->count(),
                ],
                'timestamp' => now()->toDateTimeString(),
            ];

            return $data;

        } catch (\Exception $e) {
            Log::error('❌ [ERROR PASO 6] Error al preparar datos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
