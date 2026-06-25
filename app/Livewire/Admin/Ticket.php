<?php

namespace App\Livewire\Admin;

use App\Events\TicketCreated;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ticket as TicketModel;
use App\Models\TipoEstado;
use App\Models\User;
use App\Models\CategoriaIncidencia;
use App\Models\SubCategoriaIncidencia;
use App\Models\Area;
use App\Models\TicketAuditoria;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;

class Ticket extends Component
{
    use WithPagination;


    public $newTicketNotification = false;
    public $lastTicketCreated = null;



    // Propiedades de búsqueda y filtros
    public $search = '';
    public $filterStatus = '';
    public $filterArea = '';
    public $filterCategoria = '';
    public $perPage = 10;

    // Propiedades del modal de eliminación
    public $showDeleteModal = false;
    public $ticketToDelete = null;

    // Propiedades del modal de edición
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
    public $selectedAnexo = null; // Nueva propiedad para el modal

    // Propiedades del modal de ver detalles
    public $showDetailModal = false;
    public $ticketDetail = null;


    public $notificationData = [];
    public $usuarios = [];


    // HISTORIAl
    public $showHistorialModal = false;
    public $historialTicket = [];
    public $ticketHistorialId = null;
    public $selectedAuditoriaIndex = 0; // Añade esta propiedad

    // Método 1: Escuchar con Echo + Livewire
    // Método 1: Escuchar con Echo + Livewire
    #[On('echo:tickets,ticket.created')]
    public function handleNewTicket($event)
    {

        $this->processNewTicket($event);
    }

    // Método 2: Escuchar evento personalizado desde JavaScript
    #[On('new-ticket-from-js')]
    public function handleNewTicketFromJs(...$params)
    {
        // Obtener el primer parámetro (puede venir como array u objeto)
        $data = $params[0] ?? $params;


        $this->processNewTicket($data);
    }

private function processNewTicket($data)
{
    Debugbar::info("📥 Procesando ticket");
    Debugbar::info($data);

    try {
        // Obtener datos del ticket
        $ticketData = $data['ticket'] ?? $data;

        $idUsuarioTicket = $ticketData['id_usuario'] ?? null;
        $usuarioActual = auth()->id();
        $rolUsuarioActual = auth()->user()->id_tipo_usuario_rol;

        Debugbar::info("👤 Usuario actual: {$usuarioActual}, Rol: {$rolUsuarioActual}");
        Debugbar::info("🎫 Ticket asignado a: " . ($idUsuarioTicket ?? 'NADIE (nuevo)'));

        // ============================================
        // CASO 1: TICKET NUEVO (sin asignar)
        // Solo notificar a ADMINS (rol = 1)
        // ============================================
        if (is_null($idUsuarioTicket)) {
            Debugbar::info("🆕 Ticket NUEVO - Validando si es admin");

            if ($rolUsuarioActual != 1) {
                Debugbar::info("❌ Usuario NO es admin - Ignorando notificación");
                return;
            }

            Debugbar::info("✅ Usuario es ADMIN - Mostrando notificación");
        }
        // ============================================
        // CASO 2: TICKET ASIGNADO a un técnico
        // Solo notificar al técnico asignado
        // ============================================
        else {
            Debugbar::info("👨‍💻 Ticket ASIGNADO - Validando si es el técnico correcto");

            if ($idUsuarioTicket != $usuarioActual) {
                Debugbar::info("❌ Ticket asignado a otro usuario ({$idUsuarioTicket}) - Ignorando");
                return;
            }

            Debugbar::info("✅ Ticket asignado a este usuario - Mostrando notificación");
        }

        // Si llega aquí, el usuario SÍ debe recibir la notificación
        $this->notificationData = [
            'correlativo' => $ticketData['correlativo'] ?? 'N/A',
            'area' => $ticketData['area'] ?? 'N/A',
            'categoria' => $ticketData['categoria'] ?? 'N/A',
        ];

        // Mostrar notificación
        $this->newTicketNotification = true;

        // Reproducir sonido
        $this->dispatch('play-notification-sound');

        // Recargar lista
        $this->resetPage();

    } catch (\Exception $e) {
        Log::error('❌ [ERROR] Al procesar nuevo ticket', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}


    public function hideNotification()
    {
        $this->newTicketNotification = false;
    }

    // Query strings para mantener estado en URL
    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterArea' => ['except' => ''],
        'filterCategoria' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    /**
     * Resetear paginación al buscar
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Resetear paginación al filtrar
     */
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterArea()
    {
        $this->resetPage();
    }

    public function updatingFilterCategoria()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Mostrar modal de detalles del ticket
     */
    public function showDetails($ticketId)
    {
        $this->ticketDetail = TicketModel::with([
            'area',
            'categoriaIncidencia',
            'subCategoriaIncidencia',
            'usuario',
            'tipoEstado',
            'anexos'
        ])->find($ticketId);

        $this->showDetailModal = true;
    }

    /**
     * Cerrar modal de detalles
     */
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->ticketDetail = null;
    }

    /**
     * Mostrar modal de edición
     */
    public function edit($ticketId)
    {
        $ticket = TicketModel::find($ticketId);
        $is_view = false;

        if ($ticket) {

            if ($ticket->estado == 3) {
                $ticket->estado = 6;
                $ticket->save();
            }
            if ($ticket->estado == 5) {
                $is_view = false;
            }
            Log::info("prueba");
            $this->usuarios = $this->getUsuarios($ticket->subCategoriaIncidencia->tipo_incidencia);

            $this->ticketId = $ticket->id;
            $this->correlativo = $ticket->correlativo;
            $this->id_area = $ticket->id_area;
            $this->nombres = $ticket->nombres;
            $this->id_tipo_incidencia = $ticket->id_tipo_incidencia;
            $this->id_sub_incidencia = $ticket->id_sub_incidencia;
            $this->descripcion = $ticket->descripcion;
            $this->respuesta = $ticket->respuesta;
            $this->estado = $ticket->estado;
            $this->id_usuario = $ticket->id_usuario;
            $this->estacion_creacion = $ticket->estacion_creacion;
            $this->showEditModal = true;
            $this->is_view = $is_view;

            // Forzar la actualización de la vista
            $this->dispatch('estadoChanged', $this->estado);

        }
    }


    public function verHistorial($ticketId)
    {
        $this->ticketHistorialId = $ticketId;

        // Obtener el historial de auditoría del ticket
        $this->historialTicket = TicketAuditoria::where('id_ticket', $ticketId)
            ->orderBy('fecha_auditoria', 'desc')
            ->get();

        $this->selectedAuditoriaIndex = 0; // Selecciona el primer registro por defecto
        $this->showHistorialModal = true;
    }

    // Función para cerrar el modal
    public function closeHistorialModal()
    {
        $this->showHistorialModal = false;
        $this->historialTicket = [];
        $this->ticketHistorialId = null;
    }

    /**
     * Actualizar ticket
     */
public function update()
{
    $this->validate([
        'nombres' => 'required|string|max:255',
        'id_area' => 'required|exists:areas,id',
        'id_tipo_incidencia' => 'required|exists:categoria_incidencia,id',
        'id_sub_incidencia' => 'nullable|exists:sub_categoria_incidencia,id',
        'descripcion' => 'nullable|string', // 👈 Ahora es nullable
        'respuesta' => 'nullable|string',
        'estado' => 'required|exists:tipo_estado,id',
        'id_usuario' => 'required|exists:users,id',
    ]);

    try {
        $ticket = TicketModel::find($this->ticketId);

        if ($ticket) {

            // 🔍 GUARDAR EL id_usuario ANTERIOR antes de actualizar
            $idUsuarioAnterior = $ticket->id_usuario;

            // Crear auditoría
            TicketAuditoria::create([
                'id_ticket' => $ticket->id,
                'correlativo' => $ticket->correlativo,
                'id_area' => $ticket->id_area,
                'area_nombre' => optional($ticket->area)->nombre,
                'nombres' => $ticket->nombres,
                'id_tipo_incidencia' => $ticket->id_tipo_incidencia,
                'tipo_incidencia_nombre' => optional($ticket->categoriaIncidencia)->nombre,
                'id_sub_incidencia' => $ticket->id_sub_incidencia,
                'sub_incidencia_nombre' => optional($ticket->subCategoriaIncidencia)->nombre,
                'descripcion' => $ticket->descripcion,
                'estado' => $ticket->estado,
                'estado_nombre' => optional($ticket->tipoEstado)->nombre,
                'id_usuario' => $ticket->id_usuario,
                'usuario_nombre' => optional($ticket->usuario)->name,
                'usuario_creacion' => $ticket->usuario_creacion,
                'fecha_creacion' => $ticket->fecha_creacion,
                'usuario_actualizacion' => $ticket->usuario_actualizacion,
                'fecha_actualizacion' => $ticket->fecha_actualizacion,
                'estacion_creacion' => $ticket->estacion_creacion,
                'respuesta' => $ticket->respuesta,
                'accion' => 'UPDATE',
                'fecha_auditoria' => now(),
                'id_usuario_auditoria' => auth()->id(),
            ]);

            // Calcular tiempo de respuesta
            try {
                $tiempoRespuesta = $ticket->tiempo_respuesta;

                if ($this->estado == 5) {
                    if (is_null($ticket->tiempo_respuesta) || $ticket->tiempo_respuesta === '') {
                        if ($ticket->fecha_creacion) {
                            $tiempoRespuesta = Carbon::parse($ticket->fecha_creacion)->diffInSeconds(now());
                        } else {
                            $tiempoRespuesta = null;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('ERROR en cálculo tiempo_respuesta: ' . $e->getMessage());
            }

            // Actualizar ticket
            $ticket->update([
                'nombres' => $this->nombres,
                'id_area' => $this->id_area,
                'id_tipo_incidencia' => $this->id_tipo_incidencia,
                'id_sub_incidencia' => $this->id_sub_incidencia,
                'descripcion' => $this->descripcion,
                'respuesta' => $this->respuesta,
                'estado' => $this->estado,
                'id_usuario' => $this->id_usuario,
                'usuario_actualizacion' => auth()->id(),
                'fecha_actualizacion' => now(),
                'tiempo_respuesta' => $tiempoRespuesta,
            ]);

            // ✅ SOLO DISPARAR EVENTO SI EL TÉCNICO CAMBIÓ
            if ($idUsuarioAnterior != $this->id_usuario) {
                Log::info("🔄 Técnico cambió de {$idUsuarioAnterior} a {$this->id_usuario}");
                event(new TicketCreated($ticket));
            } else {
                Log::info("⏭️ Técnico no cambió, no se dispara evento");
            }

            session()->flash('message', '✓ Ticket #' . $ticket->correlativo . ' actualizado exitosamente.');
            $this->closeEditModal();
        }
    } catch (\Exception $e) {
        session()->flash('error', 'Error al actualizar el ticket: ' . $e->getMessage());
    }
}

    /**
     * Cerrar modal de edición
     */
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset([
            'ticketId',
            'correlativo',
            'id_area',
            'nombres',
            'id_tipo_incidencia',
            'id_sub_incidencia',
            'descripcion',
            'respuesta',
            'estado',
            'id_usuario'
        ]);
    }

    /**
     * Mostrar modal de confirmación de eliminación
     */
    public function confirmDelete($ticketId)
    {
        $this->ticketToDelete = $ticketId;
        $this->showDeleteModal = true;
    }

    /**
     * Eliminar ticket
     */
    public function deleteTicket()
    {
        if ($this->ticketToDelete) {
            try {
                $ticket = TicketModel::find($this->ticketToDelete);

                if ($ticket) {
                    // Primero eliminar los anexos si existen
                    $ticket->anexos()->delete();

                    $correlativo = $ticket->correlativo;
                    $ticket->delete();

                    session()->flash('message', '✓ Ticket #' . $correlativo . ' eliminado exitosamente.');

                    // Si estamos en una página vacía después de eliminar, volver a la anterior
                    $tickets = $this->getTickets();
                    if ($tickets->count() === 0 && $tickets->currentPage() > 1) {
                        $this->setPage($tickets->currentPage() - 1);
                    }
                } else {
                    session()->flash('error', 'El ticket no existe o ya fue eliminado.');
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Error al eliminar el ticket: ' . $e->getMessage());
            }

            $this->cancelDelete();
        }
    }

    /**
     * Cancelar eliminación y cerrar modal
     */
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->ticketToDelete = null;
    }

    /**
     * Limpiar filtros
     */
    public function clearFilters()
    {
        $this->reset(['search', 'filterStatus', 'filterArea', 'filterCategoria']);
        $this->resetPage();
    }

    /**
     * Obtener tickets filtrados con todas sus relaciones
     */
    private function getTickets()
    {
        return TicketModel::with([
            'area:id,nombre,abreviatura',
            'categoriaIncidencia:id,nombre',
            'subCategoriaIncidencia:id,nombre,tipo_incidencia',
            'usuario:id,name,username',
            'tipoEstado:id,nombre',
            'anexos:id,id_ticket,ruta'
        ])
            // Excluir estado 1 (Desactivo) a menos que se filtre específicamente por él
            ->when($this->filterStatus != 1, function ($query) {
                $query->where('estado', '!=', 1);
            })
            ->when(auth()->user()->id_tipo_usuario_rol == 2, function ($query) {
                $query->where('id_usuario', auth()->id());
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('correlativo', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                        ->orWhereHas('usuario', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('area', function ($query) {
                            $query->where('nombre', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('estado', $this->filterStatus);
            })
            ->when($this->filterArea, function ($query) {
                $query->where('id_area', $this->filterArea);
            })
            ->when($this->filterCategoria, function ($query) {
                $query->where('id_tipo_incidencia', $this->filterCategoria);
            })
            // PRIMERO ordenar por prioridad de estado
            //         ->orderByRaw("CASE
            //     WHEN estado = 3 THEN 1
            //     WHEN estado = 6 THEN 2
            //     WHEN estado = 4 THEN 3
            //     WHEN estado = 5 THEN 4
            //     ELSE 5
            // END")
            // DESPUÉS ordenar por fecha de creación DESC
            ->orderBy('correlativo', 'desc')
            ->paginate($this->perPage);
    }

    /**
     * Obtener estados activos desde TipoEstado
     */
    private function getEstados()
    {
        return TipoEstado::where('estado', 1)
            ->orderBy('orden')
            ->get();
    }

    /**
     * Obtener usuarios activos
     */
    private function getUsuarios($tipoIncidenciaId = null)
    {
        $query = User::with('tipoUsuarioRol')
            ->where('estado', 1)
            ->where('id_tipo_usuario_rol', 2);

        if (!is_null($tipoIncidenciaId)) {
            // Incluir usuarios del tipo específico O los que tienen id_tipo_incidente = 3
            $query->where(function ($q) use ($tipoIncidenciaId) {
                $q->where('id_tipo_incidente', $tipoIncidenciaId)
                    ->orWhere('id_tipo_incidente', 3);
            });
        } else {
            // Si no hay filtro, solo mostrar los de tipo 3
            $query->where('id_tipo_incidente', 3);
        }

        $usuarios = $query->orderBy('name')->get();

        return $usuarios;
    }



    /**
     * Obtener áreas activas
     */
    private function getAreas()
    {
        return Area::where('estado', 1)
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Obtener categorías de incidencias activas
     */
    private function getCategorias()
    {
        return CategoriaIncidencia::where('estado', 1)
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Obtener subcategorías de incidencias activas
     */
    private function getSubCategorias()
    {
        return SubCategoriaIncidencia::where('estado', 1)
            ->when($this->id_tipo_incidencia, function ($query) {
                $query->where('id_categoria_padre', $this->id_tipo_incidencia);
            })
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        $tickets = $this->getTickets();
        Debugbar::info($tickets);
        return view('livewire.admin.ticket', [
            'tickets' => $tickets,
            'estados' => $this->getEstados(),
            // 'usuarios' => $this->getUsuarios(),
            'areas' => $this->getAreas(),
            'categorias' => $this->getCategorias(),
            'subcategorias' => $this->getSubCategorias(),
        ]);
    }
    public function downloadAnexo($anexoId)
    {
        try {
            $anexo = \App\Models\Anexos::find($anexoId);

            if ($anexo) {
                // Decodificar el archivo base64
                $fileContent = base64_decode($anexo->archivo);

                // Obtener la extensión del archivo
                $extension = pathinfo('archivo', PATHINFO_EXTENSION);

                // Crear la respuesta de descarga
                return response()->streamDownload(function () use ($fileContent) {
                    echo $fileContent;
                },  'archivo.' . $extension);
            } else {
                session()->flash('error', 'El archivo no existe.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al descargar el archivo: ' . $e->getMessage());
        }
    }
}
