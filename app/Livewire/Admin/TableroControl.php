<?php

namespace App\Livewire\Admin;

use App\Events\TicketCreated;
use Livewire\Component;
use App\Models\Ticket as TicketModel;
use App\Models\TipoEstado;
use App\Models\User;
use App\Models\CategoriaIncidencia;
use App\Models\SubCategoriaIncidencia;
use App\Models\Area;
use App\Models\TicketAuditoria;
use Livewire\Attributes\On;
use Livewire\Attributes\Polling;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TableroControl extends Component
{
    // Estados válidos
    const ESTADOS_ACTIVOS = [2, 3, 4, 6];

    // Notificación
    public $newTicketNotification = false;
    public $notificationData = [];

    // ─── POLLING: guardamos el snapshot anterior para detectar cambios ───
    // Cuántos tickets sin asignar había la última vez que chequeamos
    public $snapshotSinAsignar = -1; // -1 = aún no inicializado

    // Filtros
    public $search = '';
    public $filterStatus = '';
    public $filterArea = '';
    public $filterCategoria = '';

    // Modal eliminación
    public $showDeleteModal = false;
    public $ticketToDelete = null;

    // Modal edición
    public $showEditModal = false;
    public $ticketId = null;
    public $correlativo = '';
    public $id_area = '';
    public $nombres = '';
    public $id_tipo_incidencia = '';
    public $id_sub_incidencia = '';
    public $descripcion = '';
    public $respuesta = '';
    public $estado = '';
    public $estacion_creacion = '';
    public $is_view = false;
    public $id_usuario = '';
    public $usuarios = [];

    // Modal detalle
    public $showDetailModal = false;
    public $ticketDetail = null;

    // Modal historial
    public $showHistorialModal = false;
    public $historialTicket = [];
    public $ticketHistorialId = null;
    public $selectedAuditoriaIndex = 0;

    // ─────────────────────────────────────────
    // POLLING AUTOMÁTICO CADA 10 SEGUNDOS
    // Livewire re-ejecuta render() automáticamente.
    // Aprovechamos para detectar si cambió el count.
    // ─────────────────────────────────────────

    /**
     * Este método es llamado por Livewire en cada poll.
     * Solo detecta cambios y dispara efectos — NO re-renderiza por sí solo.
     */
    public function checkForUpdates()
    {
        $rolUsuarioActual = auth()->user()->id_tipo_usuario_rol;

        // Solo admins manejan la columna sin asignar
        if ($rolUsuarioActual != 1) {
            return;
        }

        $countActual = TicketModel::whereNull('id_usuario')
            ->whereIn('estado', self::ESTADOS_ACTIVOS)
            ->count();

        // Primera vez: inicializar snapshot sin hacer nada
        if ($this->snapshotSinAsignar === -1) {
            $this->snapshotSinAsignar = $countActual;
            return;
        }

        // Aumentó: llegó un ticket nuevo sin asignar → SONIDO + FLASH
        if ($countActual > $this->snapshotSinAsignar) {
            $this->dispatch('play-notification-sound');
            $this->dispatch('flash-column'); // JS hará parpadear la columna
        }

        // Actualizamos snapshot (tanto si aumentó como si disminuyó)
        $this->snapshotSinAsignar = $countActual;
    }

    // ─────────────────────────────────────────
    // EVENTOS REALTIME (Echo/Pusher)
    // ─────────────────────────────────────────

    #[On('echo:tickets,ticket.created')]
    public function handleNewTicket($event)
    {
        $this->processNewTicket($event);
    }

    #[On('new-ticket-from-js')]
    public function handleNewTicketFromJs(...$params)
    {
        $data = $params[0] ?? $params;
        $this->processNewTicket($data);
    }

    private function processNewTicket($data)
    {
        try {
            $ticketData       = $data['ticket'] ?? $data;
            $idUsuarioTicket  = $ticketData['id_usuario'] ?? null;
            $usuarioActual    = auth()->id();
            $rolUsuarioActual = auth()->user()->id_tipo_usuario_rol;

            // Ticket sin asignar → solo admins
            if (is_null($idUsuarioTicket)) {
                if ($rolUsuarioActual != 1) return;

                // Actualizar snapshot inmediatamente
                $this->snapshotSinAsignar = TicketModel::whereNull('id_usuario')
                    ->whereIn('estado', self::ESTADOS_ACTIVOS)
                    ->count();

            } else {
                // Ticket asignado → solo el técnico asignado
                if ($idUsuarioTicket != $usuarioActual) return;
            }

            $this->notificationData = [
                'correlativo' => $ticketData['correlativo'] ?? 'N/A',
                'area'        => $ticketData['area']        ?? 'N/A',
                'categoria'   => $ticketData['categoria']   ?? 'N/A',
            ];

            $this->newTicketNotification = true;
            $this->dispatch('play-notification-sound');
            $this->dispatch('flash-column');

        } catch (\Exception $e) {
            Log::error('Error procesando nuevo ticket', ['error' => $e->getMessage()]);
        }
    }

    public function hideNotification()
    {
        $this->newTicketNotification = false;
    }

    // ─────────────────────────────────────────
    // DATOS PRINCIPALES
    // ─────────────────────────────────────────

    private function getTicketsSinAsignar()
    {
        return TicketModel::with([
                'area:id,nombre,abreviatura',
                'categoriaIncidencia:id,nombre',
                'subCategoriaIncidencia:id,nombre,tipo_incidencia',
                'tipoEstado:id,nombre',
                'anexos:id,id_ticket,ruta',
            ])
            ->whereNull('id_usuario')
            ->whereIn('estado', self::ESTADOS_ACTIVOS)
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('nombres',       'like', "%{$this->search}%")
                  ->orWhere('correlativo', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%")
                  ->orWhereHas('area', fn($q) => $q->where('nombre', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterStatus,    fn($q) => $q->where('estado',             $this->filterStatus))
            ->when($this->filterArea,      fn($q) => $q->where('id_area',            $this->filterArea))
            ->when($this->filterCategoria, fn($q) => $q->where('id_tipo_incidencia', $this->filterCategoria))
            ->orderBy('correlativo', 'desc')
            ->get();
    }

    private function getUsuariosConTickets()
    {
        $todosLosTickets = TicketModel::with([
                'area:id,nombre,abreviatura',
                'categoriaIncidencia:id,nombre',
                'subCategoriaIncidencia:id,nombre,tipo_incidencia',
                'tipoEstado:id,nombre',
                'anexos:id,id_ticket,ruta',
            ])
            ->whereNotNull('id_usuario')
            ->whereIn('estado', self::ESTADOS_ACTIVOS)
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('nombres',       'like', "%{$this->search}%")
                  ->orWhere('correlativo', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%")
                  ->orWhereHas('area', fn($q) => $q->where('nombre', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterStatus,    fn($q) => $q->where('estado',             $this->filterStatus))
            ->when($this->filterArea,      fn($q) => $q->where('id_area',            $this->filterArea))
            ->when($this->filterCategoria, fn($q) => $q->where('id_tipo_incidencia', $this->filterCategoria))
            ->orderBy('correlativo', 'desc')
            ->get()
            ->groupBy('id_usuario');

        if ($todosLosTickets->isEmpty()) {
            return collect();
        }

        $usuarios = User::whereIn('id', $todosLosTickets->keys())->get();

        return $usuarios
            ->map(function ($user) use ($todosLosTickets) {
                $user->ticketsActivos        = $todosLosTickets->get($user->id, collect());
                $user->tickets_activos_count = $user->ticketsActivos->count();
                return $user;
            })
            ->sortByDesc('tickets_activos_count')
            ->values();
    }

    // ─────────────────────────────────────────
    // MODALES
    // ─────────────────────────────────────────

    public function showDetails($ticketId)
    {
        $this->ticketDetail = TicketModel::with([
            'area', 'categoriaIncidencia', 'subCategoriaIncidencia',
            'usuario', 'tipoEstado', 'anexos'
        ])->find($ticketId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->ticketDetail   = null;
    }

    public function edit($ticketId)
    {
        $ticket  = TicketModel::find($ticketId);
        $is_view = false;

        if ($ticket) {
            if ($ticket->estado == 3) {
                $ticket->estado = 6;
                $ticket->save();
            }
            if ($ticket->estado == 5) {
                $is_view = true;
            }

            $this->usuarios           = $this->getTecnicos($ticket->subCategoriaIncidencia->tipo_incidencia ?? null);
            $this->ticketId           = $ticket->id;
            $this->correlativo        = $ticket->correlativo;
            $this->id_area            = $ticket->id_area;
            $this->nombres            = $ticket->nombres;
            $this->id_tipo_incidencia = $ticket->id_tipo_incidencia;
            $this->id_sub_incidencia  = $ticket->id_sub_incidencia;
            $this->descripcion        = $ticket->descripcion;
            $this->respuesta          = $ticket->respuesta;
            $this->estado             = $ticket->estado;
            $this->id_usuario         = $ticket->id_usuario;
            $this->estacion_creacion  = $ticket->estacion_creacion;
            $this->is_view            = $is_view;
            $this->showEditModal      = true;

            $this->dispatch('estadoChanged', $this->estado);
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset([
            'ticketId', 'correlativo', 'id_area', 'nombres',
            'id_tipo_incidencia', 'id_sub_incidencia',
            'descripcion', 'respuesta', 'estado', 'id_usuario',
        ]);
    }

    public function verHistorial($ticketId)
    {
        $this->ticketHistorialId      = $ticketId;
        $this->historialTicket        = TicketAuditoria::where('id_ticket', $ticketId)
            ->orderBy('fecha_auditoria', 'desc')
            ->get();
        $this->selectedAuditoriaIndex = 0;
        $this->showHistorialModal     = true;
    }

    public function closeHistorialModal()
    {
        $this->showHistorialModal = false;
        $this->historialTicket   = [];
        $this->ticketHistorialId = null;
    }

    // ─────────────────────────────────────────
    // UPDATE / DELETE
    // ─────────────────────────────────────────

    public function update()
    {
        $this->validate([
            'nombres'            => 'required|string|max:255',
            'id_area'            => 'required|exists:areas,id',
            'id_tipo_incidencia' => 'required|exists:categoria_incidencia,id',
            'id_sub_incidencia'  => 'nullable|exists:sub_categoria_incidencia,id',
            'descripcion'        => 'nullable|string',
            'respuesta'          => 'nullable|string',
            'estado'             => 'required|exists:tipo_estado,id',
            'id_usuario'         => 'required|exists:users,id',
        ]);

        try {
            $ticket = TicketModel::find($this->ticketId);

            if ($ticket) {
                $idUsuarioAnterior = $ticket->id_usuario;

                TicketAuditoria::create([
                    'id_ticket'              => $ticket->id,
                    'correlativo'            => $ticket->correlativo,
                    'id_area'                => $ticket->id_area,
                    'area_nombre'            => optional($ticket->area)->nombre,
                    'nombres'                => $ticket->nombres,
                    'id_tipo_incidencia'     => $ticket->id_tipo_incidencia,
                    'tipo_incidencia_nombre' => optional($ticket->categoriaIncidencia)->nombre,
                    'id_sub_incidencia'      => $ticket->id_sub_incidencia,
                    'sub_incidencia_nombre'  => optional($ticket->subCategoriaIncidencia)->nombre,
                    'descripcion'            => $ticket->descripcion,
                    'estado'                 => $ticket->estado,
                    'estado_nombre'          => optional($ticket->tipoEstado)->nombre,
                    'id_usuario'             => $ticket->id_usuario,
                    'usuario_nombre'         => optional($ticket->usuario)->name,
                    'usuario_creacion'       => $ticket->usuario_creacion,
                    'fecha_creacion'         => $ticket->fecha_creacion,
                    'usuario_actualizacion'  => $ticket->usuario_actualizacion,
                    'fecha_actualizacion'    => $ticket->fecha_actualizacion,
                    'estacion_creacion'      => $ticket->estacion_creacion,
                    'respuesta'              => $ticket->respuesta,
                    'accion'                 => 'UPDATE',
                    'fecha_auditoria'        => now(),
                    'id_usuario_auditoria'   => auth()->id(),
                ]);

                $tiempoRespuesta = $ticket->tiempo_respuesta;
                if ($this->estado == 5 && (is_null($ticket->tiempo_respuesta) || $ticket->tiempo_respuesta === '')) {
                    $tiempoRespuesta = $ticket->fecha_creacion
                        ? Carbon::parse($ticket->fecha_creacion)->diffInSeconds(now())
                        : null;
                }

                $ticket->update([
                    'nombres'               => $this->nombres,
                    'id_area'               => $this->id_area,
                    'id_tipo_incidencia'    => $this->id_tipo_incidencia,
                    'id_sub_incidencia'     => $this->id_sub_incidencia,
                    'descripcion'           => $this->descripcion,
                    'respuesta'             => $this->respuesta,
                    'estado'                => $this->estado,
                    'id_usuario'            => $this->id_usuario,
                    'usuario_actualizacion' => auth()->id(),
                    'fecha_actualizacion'   => now(),
                    'tiempo_respuesta'      => $tiempoRespuesta,
                ]);

                if ($idUsuarioAnterior != $this->id_usuario) {
                    event(new TicketCreated($ticket));
                }

                session()->flash('message', '✓ Ticket #' . $ticket->correlativo . ' actualizado exitosamente.');
                $this->closeEditModal();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el ticket: ' . $e->getMessage());
        }
    }

    public function confirmDelete($ticketId)
    {
        $this->ticketToDelete = $ticketId;
        $this->showDeleteModal = true;
    }

    public function deleteTicket()
    {
        if ($this->ticketToDelete) {
            try {
                $ticket = TicketModel::find($this->ticketToDelete);
                if ($ticket) {
                    $ticket->anexos()->delete();
                    $correlativo = $ticket->correlativo;
                    $ticket->delete();
                    session()->flash('message', '✓ Ticket #' . $correlativo . ' eliminado exitosamente.');
                } else {
                    session()->flash('error', 'El ticket no existe o ya fue eliminado.');
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Error al eliminar: ' . $e->getMessage());
            }
            $this->cancelDelete();
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->ticketToDelete  = null;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterStatus', 'filterArea', 'filterCategoria']);
    }

    public function downloadAnexo($anexoId)
    {
        try {
            $anexo = \App\Models\Anexos::find($anexoId);
            if ($anexo) {
                $fileContent = base64_decode($anexo->archivo);
                $extension   = pathinfo('archivo', PATHINFO_EXTENSION);
                return response()->streamDownload(function () use ($fileContent) {
                    echo $fileContent;
                }, 'archivo.' . $extension);
            } else {
                session()->flash('error', 'El archivo no existe.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al descargar: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────

    private function getEstados()
    {
        return TipoEstado::whereIn('id', self::ESTADOS_ACTIVOS)
            ->orderBy('orden')
            ->get();
    }

    private function getTecnicos($tipoIncidenciaId = null)
    {
        $query = User::where('estado', 1)->where('id_tipo_usuario_rol', 2);

        if (!is_null($tipoIncidenciaId)) {
            $query->where(function ($q) use ($tipoIncidenciaId) {
                $q->where('id_tipo_incidente', $tipoIncidenciaId)
                  ->orWhere('id_tipo_incidente', 3);
            });
        } else {
            $query->where('id_tipo_incidente', 3);
        }

        return $query->orderBy('name')->get();
    }

    private function getAreas()
    {
        return Area::where('estado', 1)->orderBy('nombre')->get();
    }

    private function getCategorias()
    {
        return CategoriaIncidencia::where('estado', 1)->orderBy('nombre')->get();
    }

    private function getSubCategorias()
    {
        return SubCategoriaIncidencia::where('estado', 1)
            ->when($this->id_tipo_incidencia, fn($q) => $q->where('id_categoria_padre', $this->id_tipo_incidencia))
            ->orderBy('nombre')
            ->get();
    }

    // ─────────────────────────────────────────
    // RENDER
    // ─────────────────────────────────────────

    public function render()
    {
        // Cada vez que Livewire renderiza (incluido el poll),
        // aprovechamos para verificar cambios en el count
        $this->checkForUpdates();

        return view('livewire.admin.tablerocontrol', [
            'ticketsSinAsignar'  => $this->getTicketsSinAsignar(),
            'usuariosConTickets' => $this->getUsuariosConTickets(),
            'estados'            => $this->getEstados(),
            'areas'              => $this->getAreas(),
            'categorias'         => $this->getCategorias(),
            'subcategorias'      => $this->getSubCategorias(),
        ]);
    }
}
