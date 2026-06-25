<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Area;
use App\Models\TipoEstado;
use App\Models\CategoriaIncidencia;
use App\Models\SubCategoriaIncidencia;

class TicketSeguimiento extends Component
{
    // Propiedades de búsqueda
    public $busqueda = '';
    public $mostrarBusqueda = '';
    public $tipoBusqueda = 'correlativo';
    public $busquedaEmpleado = '';
    public $empleadoSeleccionado = null;
    public $empleadosEncontrados = [];
    public $showEmpleadosModal = false;
    
    // Propiedades de resultados
    public $tickets = [];
    public $ticketSeleccionado = null;
    public $mostrarDetalle = false;
    
    // Propiedades de paginación
    public $currentPage = 1;
    public $lastPage = 1;
    public $totalTickets = 0;
    public $perPage = 3;
    
    // Propiedades para los combos de búsqueda
    public $areas = [];

    protected $rules = [
        'busqueda' => 'required|string|min:1',
    ];

    protected $messages = [
        'busqueda.required' => 'Debe ingresar un criterio de búsqueda',
        'busqueda.min' => 'Debe ingresar al menos 1 carácter',
    ];

    public function mount()
    {
        $this->cargarAreas();
        
        if (request()->has('buscar')) {
            $this->busqueda = request()->get('buscar');
            $this->tipoBusqueda = 'correlativo';
            $this->buscar(); 
        }
    }

    public function cargarAreas()
    {
        $this->areas = Area::where('estado', 1)->orderBy('nombre')->get();
    }

    public function buscar()
    {
        $this->validate();
        
        // Resetear paginación
        $this->currentPage = 1;
        
        // Auto-completar con ceros si es búsqueda por correlativo
        if ($this->tipoBusqueda === 'correlativo' && is_numeric($this->busqueda)) {
            $this->busqueda = str_pad($this->busqueda, 6, '0', STR_PAD_LEFT);
        }

        $this->tickets = [];
        $this->ticketSeleccionado = null;
        $this->mostrarDetalle = false;

        try {
            $query = Ticket::query()
                ->with(['anexos'])
                ->where('estado', '!=', 0); // Excluir eliminados

            switch ($this->tipoBusqueda) {
                case 'correlativo':
                    $query->where('correlativo', 'like', '%' . $this->busqueda . '%');
                    break;
                    
                case 'nombre':
                    $query->where('nombres', 'like', '%' . $this->busqueda . '%');
                    break;
                    
                case 'area':
                    $query->where('id_area', $this->busqueda);
                    break;
            }

            // Obtener total de tickets para paginación
            $this->totalTickets = $query->count();
            $this->lastPage = ceil($this->totalTickets / $this->perPage);

            // Obtener tickets de la página actual
            $this->tickets = $query->orderBy('fecha_creacion', 'desc')
                ->skip(($this->currentPage - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();

            if ($this->tickets->isEmpty()) {
                session()->flash('info', 'No se encontraron tickets con los criterios de búsqueda.');
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error al buscar tickets: ' . $e->getMessage());
        }
    }

    public function verDetalle($ticketId)
    {
        try {
            $this->ticketSeleccionado = Ticket::with(['anexos'])
                ->find($ticketId);

            if ($this->ticketSeleccionado) {
                // Cargar datos relacionados
                $this->ticketSeleccionado->area = Area::find($this->ticketSeleccionado->id_area);
                $this->ticketSeleccionado->categoria = CategoriaIncidencia::find($this->ticketSeleccionado->id_tipo_incidencia);
                $this->ticketSeleccionado->subcategoria = SubCategoriaIncidencia::find($this->ticketSeleccionado->id_sub_incidencia);
                $this->ticketSeleccionado->tipoEstado = TipoEstado::find($this->ticketSeleccionado->estado);
                
                $this->mostrarDetalle = true;
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el detalle del ticket: ' . $e->getMessage());
        }
    }

    public function cerrarDetalle()
    {
        $this->mostrarDetalle = false;
        $this->ticketSeleccionado = null;
    }

    public function buscarEmpleado()
    {
        $value = $this->busquedaEmpleado;
        $this->empleadoSeleccionado = null;
        
        if (strlen($value) >= 3) {
            $this->empleadosEncontrados = \App\Models\Empleado::where('estado', 1)
                ->where('nombres', 'like', '%' . $value . '%')
                ->with('area')
                ->get();
            
            if ($this->empleadosEncontrados->count() === 1) {
                $this->seleccionarEmpleadoParaBusqueda($this->empleadosEncontrados->first()->id);
            } elseif ($this->empleadosEncontrados->count() > 1) {
                $this->showEmpleadosModal = true;
            } else {
                $this->empleadosEncontrados = [];
                $this->showEmpleadosModal = false;
            }
        } else {
            $this->empleadosEncontrados = [];
            $this->showEmpleadosModal = false;
        }
    }

    public function seleccionarEmpleadoParaBusqueda($empleadoId)
    {
        $this->empleadoSeleccionado = \App\Models\Empleado::with('area')->find($empleadoId);
        $this->busquedaEmpleado = $this->empleadoSeleccionado->nombres;
        $this->busqueda = $this->empleadoSeleccionado->nombres; // Para que la búsqueda funcione
        $this->showEmpleadosModal = false;
        $this->empleadosEncontrados = [];
    }

    public function cerrarModalEmpleados()
    {
        $this->showEmpleadosModal = false;
    }

    public function limpiarEmpleado()
    {
        $this->empleadoSeleccionado = null;
        $this->busquedaEmpleado = '';
        $this->busqueda = '';
        $this->empleadosEncontrados = [];
    }

    public function getTiempoTranscurrido($fecha)
    {
        $fechaCreacion = \Carbon\Carbon::parse($fecha);
        $ahora = \Carbon\Carbon::now();
        
        $diff = $fechaCreacion->diffInDays($ahora);
        
        if ($diff == 0) {
            return 'Hoy';
        } elseif ($diff == 1) {
            return 'Ayer';
        } elseif ($diff < 7) {
            return "Hace {$diff} días";
        } elseif ($diff < 30) {
            $semanas = floor($diff / 7);
            return "Hace {$semanas} " . ($semanas == 1 ? 'semana' : 'semanas');
        } else {
            $meses = floor($diff / 30);
            return "Hace {$meses} " . ($meses == 1 ? 'mes' : 'meses');
        }
    }

    public function limpiarBusqueda()
    {
        $this->reset(['busqueda', 'busquedaEmpleado', 'empleadoSeleccionado', 'tickets', 'ticketSeleccionado', 'mostrarDetalle', 'currentPage', 'lastPage', 'totalTickets']);
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->lastPage) {
            $this->currentPage++;
            $this->loadTicketsForCurrentPage();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadTicketsForCurrentPage();
        }
    }

    public function goToPage($page)
    {
        if ($page >= 1 && $page <= $this->lastPage) {
            $this->currentPage = $page;
            $this->loadTicketsForCurrentPage();
        }
    }

    private function loadTicketsForCurrentPage()
    {
        try {
            $query = Ticket::query()
                ->with(['anexos'])
                ->where('estado', '!=', 0);

            switch ($this->tipoBusqueda) {
                case 'correlativo':
                    $query->where('correlativo', 'like', '%' . $this->busqueda . '%');
                    break;
                    
                case 'nombre':
                    $query->where('nombres', 'like', '%' . $this->busqueda . '%');
                    break;
                    
                case 'area':
                    $query->where('id_area', $this->busqueda);
                    break;
            }

            $this->tickets = $query->orderBy('fecha_creacion', 'desc')
                ->skip(($this->currentPage - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar tickets: ' . $e->getMessage());
        }
    }

    public function getEstadoNombre($estadoId)
    {
        $estado = TipoEstado::find($estadoId);
        return $estado ? $estado->nombre : 'Desconocido';
    }

    public function getEstadoColor($estadoId)
    {
        $estado = TipoEstado::find($estadoId);
        
        if (!$estado) {
            return 'secondary';
        }
        
        // Mapeo de colores según el nombre del estado
        $colores = [
            'Pendiente' => 'warning',
            'En Proceso' => 'info',
            'Resuelto' => 'success',
            'Abierto' => 'primary',
            'Cerrado' => 'secondary',
        ];
        
        return $colores[$estado->nombre] ?? 'secondary';
    }

    public function render()
    {
        return view('livewire.public.ticket-seguimiento');
    }
}