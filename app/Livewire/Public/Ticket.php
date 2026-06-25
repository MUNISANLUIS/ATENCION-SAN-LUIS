<?php

namespace App\Livewire\Public;

use Livewire\Component;

use App\Events\TicketCreated;

use Livewire\WithFileUploads;
use App\Models\Empleado;
use App\Models\CategoriaIncidencia;
use App\Models\SubCategoriaIncidencia;
use App\Models\Ticket as TicketModel;
use App\Models\Anexos;
use App\Models\TicketAuditoria;
use Illuminate\Support\Facades\Storage;

class Ticket extends Component
{
    use WithFileUploads;

    // Propiedades del formulario
    //public $id_area = '';
    public $nombres = '';
    public $id_tipo_incidencia = '';
    public $id_sub_incidencia = '';
    public $descripcion = '';
    public $anexos = [];
    public $uploadingFiles = false;
    public $subCategoriaSeleccionada = null;
    public $busquedaEmpleado = '';
    public $empleadoSeleccionado = null;
    public $empleadosEncontrados = [];
    public $showEmpleadosModal = false;
    public $empleadoNoEncontrado = false;

    // Propiedades de estado
    public $showSuccessModal = false;
    public $ticketCorrelativo = '';
    public $showHelpModal = false;

    // Datos para los combos
    public $areas = [];
    public $tiposIncidencia = [];
    public $subTiposIncidencia = [];

    // Reglas de validación
    protected $rules = [
        // 'id_area' => 'required|exists:areas,id', // ELIMINAR
        // 'nombres' => 'required|string|max:255', // ELIMINAR
        'empleadoSeleccionado' => 'required', // AGREGAR
        'id_tipo_incidencia' => 'required|exists:categoria_incidencia,id',
        'id_sub_incidencia' => 'required|exists:sub_categoria_incidencia,id',
        'descripcion' => 'required|string',
        'anexos.*' => 'nullable|file|max:51200',
    ];

    protected $messages = [
        // 'id_area.required' => 'Debe seleccionar un área', // ELIMINAR
        // 'nombres.required' => 'El nombre es obligatorio', // ELIMINAR // antonio no mevas y la kagues
        'empleadoSeleccionado.required' => 'Debe seleccionar un empleado', // AGREGAR
        'id_tipo_incidencia.required' => 'Debe seleccionar un tipo de incidencia',
        'id_sub_incidencia.required' => 'Debe seleccionar un subtipo de incidencia',
        'descripcion.required' => 'La descripción es obligatoria',
    ];

    public function mount()
    {
        //$this->cargarAreas();
        $this->cargarTiposIncidencia();
    }

    public function updatedAnexos()
    {
        $this->resetErrorBag('anexos');
        $this->resetErrorBag('anexos.*');
    }

    //public function cargarAreas()
    //{
    //    $this->areas = Area::where('estado', 1)->orderBy('nombre')->get();
    //}

    public function buscarEmpleado()
    {
        $value = $this->busquedaEmpleado;
        $this->empleadoSeleccionado = null;
        $this->empleadoNoEncontrado = false; // Resetear el flag

        if (strlen($value) >= 3) {
            $this->empleadosEncontrados = Empleado::where('estado', 1)
                ->where('nombres', 'like', '%' . $value . '%')
                ->with('area')
                ->get();

            if ($this->empleadosEncontrados->count() === 1) {
                $this->seleccionarEmpleado($this->empleadosEncontrados->first()->id);
            } elseif ($this->empleadosEncontrados->count() > 1) {
                $this->showEmpleadosModal = true;
            } else {
                // Marcar como no encontrado
                $this->empleadoNoEncontrado = true;
                $this->empleadosEncontrados = [];
                $this->showEmpleadosModal = false;
            }
        } else {
            $this->empleadosEncontrados = [];
            $this->showEmpleadosModal = false;
        }
    }

    public function seleccionarEmpleado($empleadoId)
    {
        $this->empleadoSeleccionado = Empleado::with('area')->find($empleadoId);
        $this->busquedaEmpleado = $this->empleadoSeleccionado->nombres;
        $this->showEmpleadosModal = false;
        $this->empleadosEncontrados = [];
    }

    public function cerrarModalEmpleados()
    {
        $this->showEmpleadosModal = false;
    }

    public function toggleHelpModal()
    {
        $this->showHelpModal = !$this->showHelpModal;
    }

    public function limpiarEmpleado()
    {
        $this->empleadoSeleccionado = null;
        $this->busquedaEmpleado = '';
        $this->empleadosEncontrados = [];
        $this->empleadoNoEncontrado = false;
    }

    public function cargarTiposIncidencia()
    {
        $this->tiposIncidencia = CategoriaIncidencia::where('estado', 1)->orderBy('orden')->get();
    }

    public function updatedIdTipoIncidencia($value)
    {
        $this->id_sub_incidencia = '';
        $this->subCategoriaSeleccionada = null; // Agregar esta línea
        if ($value) {
            $this->subTiposIncidencia = SubCategoriaIncidencia::where('id_categoria_padre', $value)
                ->where('estado', 1)
                ->orderBy('orden')
                ->get();
        } else {
            $this->subTiposIncidencia = [];
        }
    }

    public function updatedIdSubIncidencia($value)
    {
        if ($value) {
            $this->subCategoriaSeleccionada = SubCategoriaIncidencia::find($value);
        } else {
            $this->subCategoriaSeleccionada = null;
        }
    }

    public function descargarFormato()
    {
        if ($this->subCategoriaSeleccionada && $this->subCategoriaSeleccionada->formato_path) {
            return Storage::disk('public')->download($this->subCategoriaSeleccionada->formato_path);
        }

        session()->flash('error', 'No hay formato disponible para esta subcategoría');
    }

    public function removeAnexo($index)
    {
        array_splice($this->anexos, $index, 1);
    }

    private function obtenerEstacionCreacion()
    {
        $ip = request()->ip() ?: 'SIN IP';

        return $ip;
    }

    public function submit()
    {
        $this->validate();

        try {
            // Generar correlativo
            $ultimoTicket = TicketModel::orderBy('id', 'desc')->first();
            $numeroCorrelativo = $ultimoTicket ? (intval($ultimoTicket->correlativo) + 1) : 1;
            $correlativo = str_pad($numeroCorrelativo, 6, '0', STR_PAD_LEFT);

            // Crear ticket
            $ticket = TicketModel::create([
                'correlativo' => $correlativo,
                'id_area' => $this->empleadoSeleccionado->id_area,
                'nombres' => $this->empleadoSeleccionado->nombres,
                'id_tipo_incidencia' => $this->id_tipo_incidencia,
                'id_sub_incidencia' => $this->id_sub_incidencia,
                'descripcion' => $this->descripcion,
                'estado' => '3',
                'usuario_creacion' => $this->empleadoSeleccionado->id,
                'fecha_creacion' => now(),
                'estacion_creacion' => $this->obtenerEstacionCreacion(),
            ]);

            // Guardar anexos
            if (!empty($this->anexos)) {
                foreach ($this->anexos as $anexo) {
                    // Generar nombre personalizado
                    $timestamp = time();
                    $nombreOriginal = pathinfo($anexo->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $anexo->getClientOriginalExtension();
                    $nombrePersonalizado = $timestamp . '_' . $nombreOriginal . '.' . $extension;

                    // Guardar con nombre personalizado
                    $path = $anexo->storeAs('tickets', $nombrePersonalizado, 'public');

                    Anexos::create([
                        'id_ticket' => $ticket->id,
                        'ruta' => $path,
                        'estado' => 1,
                        'usuario_creacion' => auth()->user()->usuario ?? 'Sistema',
                        'fecha_creacion' => now(),
                    ]);
                }
            }



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
                    'accion' => 'CREATE',
                    'fecha_auditoria' => now(),
                    'id_usuario_auditoria' => auth()->id(),
                ]);

            // 🔥 DISPARAR EVENTO
            event(new TicketCreated($ticket));

            // Mostrar modal de éxito
            $this->ticketCorrelativo = $correlativo;
            $this->showSuccessModal = true;

            // Limpiar formulario
            $this->reset(['busquedaEmpleado', 'empleadoSeleccionado', 'id_tipo_incidencia', 'id_sub_incidencia', 'descripcion', 'anexos']);
            $this->subTiposIncidencia = [];

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el ticket: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showSuccessModal = false;
    }

    public function irASeguimiento()
    {
        return redirect()->route('public.ticket-seguimiento', ['buscar' => $this->ticketCorrelativo]);
    }

    public function render()
    {
        return view('livewire.public.ticket');
    }
}