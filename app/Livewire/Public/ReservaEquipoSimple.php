<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\ReservaEquipo;
use App\Models\Equipo;
use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservaEquipoSimple extends Component
{
    public $equipos;
    public $selectedEquipos = [];
    public $id_equipo, $fecha_inicio, $fecha_fin, $motivo;
    public $fecha_reserva, $hora_inicio, $hora_fin;
    public $search = '';
    public $selectedEquipo = null;
    public $calendarDays = [];
    public $firstDayOfMonth = 0;
    public $existingReservas = [];
    
    // Propiedades para empleado
    public $busquedaEmpleado = '';
    public $empleadoSeleccionado = null;
    public $empleadoNoEncontrado = false;
    public $empleadosEncontrados = [];
    
    // Propiedades para personal técnico
    public $requierePersonal = false;
    
    // Propiedad para ubicación
    public $ubicacion = '';
    
    // Propiedades para panel de información
    public $showInfo = false;
    public $todayReservas;
    public $selectedDate = null;
    public $selectedDayReservas = [];
    public $equiposCatalogo = [];
    public $currentReservaIndex = 0;

    // Propiedades para notificación centrada
    public $showNotification = false;
    public $notificationMessage = '';

    // ⭐ NUEVA PROPIEDAD PARA ERROR DE HORARIO ⭐
    public $errorHorario = '';

    protected $rules = [
        'selectedEquipos' => 'required|array|min:1',
        'empleadoSeleccionado' => 'required',
        'requierePersonal' => 'required|boolean',
        'fecha_reserva' => 'required|date|after_or_equal:today',
        'hora_inicio' => 'required|string',
        'hora_fin' => 'required|string',
        'motivo' => 'required|string|max:500',
        'ubicacion' => 'nullable|string|max:255'
    ];

    protected $messages = [
        'fecha_reserva.after_or_equal' => 'La fecha de reserva debe ser hoy o una fecha futura.',
        'hora_fin.different' => 'La hora de fin debe ser diferente a la hora de inicio.',
        'selectedEquipos.required' => 'Debes seleccionar al menos un equipo.',
        'empleadoSeleccionado.required' => 'Debes seleccionar un empleado.',
        'requierePersonal.required' => 'Debes indicar si se requiere personal técnico.',
        'ubicacion.max' => 'La ubicación no puede tener más de 255 caracteres.'
    ];

    public function mount()
    {
        $this->fecha_reserva = Carbon::now()->format('Y-m-d');
        $this->hora_inicio = '08:00';
        $this->hora_fin = '10:00';
        $this->loadEquipos();
        $this->loadExistingReservas();
        $this->loadEquiposCatalogo();
    }

    public function loadExistingReservas()
    {
        // Cargar todas las reservas existentes para mostrar en la lista
        $this->existingReservas = ReservaEquipo::with(['equipo'])
            ->whereIn('estado', ['APROBADA', 'PENDIENTE'])
            ->orderBy('fecha_inicio', 'desc')
            ->limit(50)
            ->get();
    }

    public function loadEquiposCatalogo()
    {
        // Cargar catálogo de equipos para obtener tipos
        $this->equiposCatalogo = Equipo::pluck('tipo', 'id')->toArray();
    }

    public function getEquiposTypes($equiposIds)
    {
        if (!$equiposIds) {
            return null;
        }
        
        // Si es un array o string JSON
        if (is_array($equiposIds)) {
            $ids = $equiposIds;
        } elseif (is_string($equiposIds) && str_contains($equiposIds, '[')) {
            $ids = json_decode($equiposIds, true);
        } else {
            $ids = explode(',', $equiposIds);
        }
        
        $types = [];
        
        foreach ($ids as $id) {
            $id = trim($id);
            if (isset($this->equiposCatalogo[$id])) {
                $types[] = $this->equiposCatalogo[$id];
            }
        }
        
        return empty($types) ? null : implode(', ', $types);
    }

    public function loadEquipos()
    {
        $this->equipos = Equipo::where('estado', '1')->get();
    }

    public function selectEquipo($equipoId)
    {
        $equipo = Equipo::find($equipoId);
        
        // Toggle selección del equipo
        if (isset($this->selectedEquipos[$equipoId])) {
            unset($this->selectedEquipos[$equipoId]);
        } else {
            $this->selectedEquipos[$equipoId] = $equipo;
        }
        
        // Para compatibilidad con el calendario (muestra el primero)
        $this->selectedEquipo = count($this->selectedEquipos) > 0 ? reset($this->selectedEquipos) : null;
        
        if ($this->selectedEquipo) {
            $this->generateCalendar();
        }
    }

    public function selectAll()
    {
        $this->selectedEquipos = [];
        foreach ($this->equipos as $equipo) {
            $this->selectedEquipos[$equipo->id] = $equipo;
        }
        
        $this->selectedEquipo = count($this->selectedEquipos) > 0 ? reset($this->selectedEquipos) : null;
        
        if ($this->selectedEquipo) {
            $this->generateCalendar();
        }
    }

    public function buscarEmpleado()
    {
        if (strlen($this->busquedaEmpleado) < 2) {
            return;
        }

        $empleados = Empleado::where(function($query) {
            $query->where('nombres', 'like', '%' . $this->busquedaEmpleado . '%')
                  ->orWhere('dni', 'like', '%' . $this->busquedaEmpleado . '%');
        })
        ->where('estado', '1')
        ->with('area')
        ->limit(10)
        ->get();

        if ($empleados->count() == 1) {
            $this->empleadoSeleccionado = $empleados->first();
            $this->empleadoNoEncontrado = false;
            $this->empleadosEncontrados = [];
            $this->busquedaEmpleado = $this->empleadoSeleccionado->nombres;
        } elseif ($empleados->count() > 1) {
            $this->empleadoSeleccionado = null;
            $this->empleadoNoEncontrado = false;
            $this->empleadosEncontrados = $empleados;
        } else {
            $this->empleadoSeleccionado = null;
            $this->empleadoNoEncontrado = true;
            $this->empleadosEncontrados = [];
        }
    }

    public function limpiarEmpleado()
    {
        $this->empleadoSeleccionado = null;
        $this->busquedaEmpleado = '';
        $this->empleadoNoEncontrado = false;
        $this->empleadosEncontrados = [];
    }

    public function seleccionarEmpleado($empleadoId)
    {
        $this->empleadoSeleccionado = Empleado::with('area')->find($empleadoId);
        $this->busquedaEmpleado = $this->empleadoSeleccionado->nombres;
        $this->empleadoNoEncontrado = false;
        $this->empleadosEncontrados = [];
    }

    public function clearSelection()
    {
        $this->selectedEquipos = [];
        $this->selectedEquipo = null;
        $this->calendarDays = [];
        $this->existingReservas = [];
    }

    public function generateCalendar()
    {
        if (!$this->selectedEquipo) {
            return;
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $daysInMonth = Carbon::now()->daysInMonth;
        $this->firstDayOfMonth = Carbon::now()->startOfMonth()->dayOfWeek;
        
        // Obtener todas las reservas existentes del equipo
        $this->existingReservas = $this->selectedEquipo->reservas()
            ->whereIn('estado', ['APROBADA', 'PENDIENTE'])
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        // Obtener reservas del calendario (mes actual)
        $reservas = $this->selectedEquipo->reservas()
            ->whereIn('estado', ['APROBADA', 'PENDIENTE'])
            ->whereMonth('fecha_inicio', $currentMonth)
            ->whereYear('fecha_inicio', $currentYear)
            ->get();

        $this->calendarDays = [];

        // Generar días del mes
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::now()->setDay($day);
            $isPast = $date->isPast();
            
            $hasReserva = $reservas->contains(function($reserva) use ($date) {
                return $date->between(Carbon::parse($reserva->fecha_inicio), Carbon::parse($reserva->fecha_fin));
            });
            
            $isSelected = $this->fecha_inicio && $date->isSameDay(Carbon::parse($this->fecha_inicio));

            $this->calendarDays[] = [
                'day' => $day,
                'date' => $date,
                'isPast' => $isPast,
                'hasReserva' => $hasReserva,
                'isSelected' => $isSelected
            ];
        }
    }

    public function getAllReservasCalendar()
    {
        // Obtener todas las reservas para el mes actual
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $reservas = ReservaEquipo::with(['equipo'])
            ->whereIn('estado', ['APROBADA', 'PENDIENTE'])
            ->whereMonth('fecha_inicio', $currentMonth)
            ->whereYear('fecha_inicio', $currentYear)
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        $calendarData = [];

        foreach ($reservas as $reserva) {
            $startDate = Carbon::parse($reserva->fecha_inicio);
            $endDate = Carbon::parse($reserva->fecha_fin);
            
            $calendarData[] = [
                'id' => $reserva->id,
                'title' => $reserva->motivo . ' - ' . ($reserva->equipo->codigo ?? 'N/A'),
                'start' => $startDate->format('Y-m-d H:i:s'),
                'end' => $endDate->format('Y-m-d H:i:s'),
                'backgroundColor' => $reserva->estado === 'APROBADA' ? '#10b981' : '#f59e0b',
                'borderColor' => $reserva->estado === 'APROBADA' ? '#059669' : '#d97706',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'usuario' => $reserva->usuario_creacion,
                    'equipo' => $reserva->equipo->codigo ?? 'N/A',
                    'equipos' => $reserva->equipos_ids,
                    'ubicacion' => $reserva->ubicacion,
                    'requiere_personal' => $reserva->requiere_personal,
                    'estado' => $reserva->estado
                ]
            ];
        }

        return $calendarData;
    }

    public function getCalendarDays()
    {
        $days = [];
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $firstDay = Carbon::create($currentYear, $currentMonth, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startPadding = $firstDay->dayOfWeek;
        
        // Agregar espacios vacíos antes del primer día
        for ($i = 0; $i < $startPadding; $i++) {
            $days[] = ['isEmpty' => true];
        }
        
        // Agregar días del mes
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($currentYear, $currentMonth, $day);
            $isSunday = $currentDate->isSunday();
            
            // Obtener reservas del día actual desde la colección existingReservas
            $dayReservas = collect();
            if ($this->existingReservas) {
                $dayReservas = $this->existingReservas->filter(function($reserva) use ($currentDate) {
                    $reservaDate = Carbon::parse($reserva->fecha_inicio);
                    return $reservaDate->format('Y-m-d') === $currentDate->format('Y-m-d');
                });
            }
            
            $days[] = [
                'isEmpty' => false,
                'day' => $day,
                'isToday' => $currentDate->isToday(),
                'isSunday' => $isSunday,
                'disabled' => $isSunday,
                'reservas' => $dayReservas,
                'date' => $currentDate
            ];
        }
        
        return $days;
    }

    public function selectDay($day)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $selectedDate = Carbon::create($currentYear, $currentMonth, $day);
        
        // Validar que no sea domingo
        if ($selectedDate->isSunday()) {
            session()->flash('error', '❌ No se pueden seleccionar días domingo para ver reservas.');
            $this->selectedDate = null;
            $this->selectedDayReservas = [];
            $this->currentReservaIndex = 0;
            return;
        }
        
        $this->selectedDate = $selectedDate;
        
        // Filtrar reservas para el día seleccionado desde existingReservas
        $this->selectedDayReservas = $this->existingReservas->filter(function($reserva) use ($day, $currentMonth, $currentYear) {
            $reservaDate = Carbon::parse($reserva->fecha_inicio);
            return $reservaDate->day == $day && 
                   $reservaDate->month == $currentMonth && 
                   $reservaDate->year == $currentYear;
        })->values();
        
        // Reiniciar índice al seleccionar un nuevo día
        $this->currentReservaIndex = 0;
    }

    public function previousReserva()
    {
        if ($this->currentReservaIndex > 0) {
            $this->currentReservaIndex--;
        }
    }

    public function nextReserva()
    {
        if ($this->currentReservaIndex < $this->selectedDayReservas->count() - 1) {
            $this->currentReservaIndex++;
        }
    }

    public function refreshCalendar()
    {
        $this->dispatch('refreshCalendar');
    }

    public function toggleInfo()
    {
        $this->showInfo = !$this->showInfo;
        
        if ($this->showInfo) {
            // Primero intentar cargar reservas del día actual
            $today = Carbon::now()->format('Y-m-d');
            $this->todayReservas = ReservaEquipo::with(['equipo'])
                ->whereIn('estado', ['APROBADA', 'PENDIENTE'])
                ->whereDate('fecha_inicio', $today)
                ->orderBy('fecha_inicio', 'asc')
                ->get();
            
            // Si no hay reservas hoy, cargar las próximas reservas
            if ($this->todayReservas->isEmpty()) {
                $this->todayReservas = ReservaEquipo::with(['equipo'])
                    ->whereIn('estado', ['APROBADA', 'PENDIENTE'])
                    ->where('fecha_inicio', '>', Carbon::now())
                    ->orderBy('fecha_inicio', 'asc')
                    ->limit(10)
                    ->get();
            }
            
            // Disparar evento para inicializar el calendario del modal
            $this->dispatch('showModalCalendar', todayReservas: $this->todayReservas);
        }
    }

    public function limpiarFormulario()
    {
        $this->selectedEquipos = [];
        $this->selectedEquipo = null;
        $this->limpiarEmpleado();
        $this->motivo = '';
        $this->requierePersonal = false;
        $this->ubicacion = '';
        $this->fecha_reserva = Carbon::now()->format('Y-m-d');
        $this->hora_inicio = '08:00';
        $this->hora_fin = '10:00';
        $this->errorHorario = '';
        $this->calendarDays = [];
        $this->existingReservas = [];
    }

    /**
     * ⭐ MÉTODO PARA VALIDAR HORARIO EN TIEMPO REAL ⭐
     */
    public function updatedFechaReserva()
    {
        $this->validarHoraInicio();
    }

    public function updatedHoraInicio()
    {
        $this->validarHoraInicio();
    }

    /**
     * ⭐ VALIDACIÓN DE HORA DE INICIO (NO ANTERIOR A LA HORA ACTUAL) ⭐
     */
    public function validarHoraInicio()
    {
        $this->errorHorario = '';
        
        if (!$this->fecha_reserva || !$this->hora_inicio) {
            return;
        }
        
        $fechaSeleccionada = Carbon::parse($this->fecha_reserva);
        $hoy = Carbon::today();
        
        // Solo validar si la fecha seleccionada es hoy
        if ($fechaSeleccionada->isToday()) {
            $ahora = Carbon::now();
            $horaInicioSeleccionada = Carbon::parse($this->fecha_reserva . ' ' . $this->hora_inicio);
            
            if ($horaInicioSeleccionada->lt($ahora)) {
                $this->errorHorario = '❌ No puedes seleccionar una hora anterior a las ' . $ahora->format('H:i');
                // Opcional: resetear la hora_inicio si es inválida
                // $this->hora_inicio = '';
            }
        }
    }

    /**
     * ⭐ MÉTODO PARA CERRAR LA NOTIFICACIÓN ⭐
     */
    public function closeNotification()
    {
        $this->showNotification = false;
        $this->notificationMessage = '';
    }

    /**
     * ⭐ VALIDACIÓN DE HORARIO PARA EL BACKEND ⭐
     */
    public function validateHorario()
    {
        $fechaSeleccionada = Carbon::parse($this->fecha_reserva);
        
        // Si la fecha seleccionada es hoy
        if ($fechaSeleccionada->isToday()) {
            $ahora = Carbon::now();
            $horaInicioSeleccionada = Carbon::parse($this->fecha_reserva . ' ' . $this->hora_inicio);
            
            // Si la hora de inicio es anterior a la hora actual
            if ($horaInicioSeleccionada->lt($ahora)) {
                session()->flash('error', '❌ No puedes seleccionar un horario anterior a la hora actual (' . $ahora->format('H:i') . '). Por favor, elige una hora posterior.');
                return false;
            }
        }
        
        return true;
    }

    /**
     * ⭐ MÉTODO STORE MODIFICADO CON VALIDACIÓN DE HORARIO ⭐
     */
    public function store()
    {
        // Validar que se haya seleccionado al menos un equipo
        if (empty($this->selectedEquipos) || count($this->selectedEquipos) === 0) {
            session()->flash('error', 'No se seleccionó ningún equipo a reservar. Debes seleccionar al menos un equipo para continuar.');
            return;
        }

        // Validar que se haya seleccionado un empleado
        if (!$this->empleadoSeleccionado) {
            session()->flash('error', 'Debes seleccionar un empleado para realizar la reserva.');
            return;
        }

        // Validar que la fecha no sea domingo
        $fecha = Carbon::parse($this->fecha_reserva);
        if ($fecha->isSunday()) {
            session()->flash('error', '❌ No se permiten reservas los días domingo. Por favor, seleccione otra fecha.');
            return;
        }

        // ⭐ VALIDACIÓN DE HORARIO (NO ANTERIOR A LA HORA ACTUAL) ⭐
        if (!$this->validateHorario()) {
            return;
        }

        $this->validate();

        // Combinar fecha y horas
        $this->fecha_inicio = $this->fecha_reserva . ' ' . $this->hora_inicio;
        $this->fecha_fin = $this->fecha_reserva . ' ' . $this->hora_fin;

        // Validar que la hora de fin sea posterior a la hora de inicio
        if (strtotime($this->fecha_fin) <= strtotime($this->fecha_inicio)) {
            session()->flash('error', 'La hora de fin debe ser posterior a la hora de inicio.');
            return;
        }

        // Validar que no existan reservas en el mismo horario para ningún equipo seleccionado
        $hasConflict = false;
        $conflictingEquipo = null;
        
        foreach ($this->selectedEquipos as $equipo) {
            $conflictingReserva = $equipo->reservas()
                ->whereIn('estado', ['APROBADA', 'PENDIENTE'])
                ->where(function($query) {
                    return $query->where(function($q) {
                        $q->where('fecha_inicio', '<=', $this->fecha_inicio)
                          ->where('fecha_fin', '>', $this->fecha_inicio);
                    })->orWhere(function($q) {
                        $q->where('fecha_inicio', '<', $this->fecha_fin)
                          ->where('fecha_fin', '>=', $this->fecha_fin);
                    })->orWhere(function($q) {
                        $q->where('fecha_inicio', '>=', $this->fecha_inicio)
                          ->where('fecha_fin', '<=', $this->fecha_fin);
                    });
                })
                ->first();

            if ($conflictingReserva) {
                $hasConflict = true;
                $conflictingEquipo = $equipo;
                break;
            }
        }

        if ($hasConflict) {
            $fechaInicio = Carbon::parse($this->fecha_inicio)->format('d/m/Y H:i');
            $fechaFin = Carbon::parse($this->fecha_fin)->format('d/m/Y H:i');
            session()->flash('error', "No se puede hacer la reserva. El equipo '{$conflictingEquipo->codigo}' ya tiene una reserva en el horario seleccionado ({$fechaInicio} - {$fechaFin}). Por favor, elige otro horario o fecha disponible.");
            return;
        }

        try {
            DB::beginTransaction();
            
            // Obtener IDs de los equipos seleccionados
            $equiposIds = array_keys($this->selectedEquipos);
            
            // Crear una sola reserva con múltiples equipos
            $reserva = ReservaEquipo::create([
                'id_equipo' => $equiposIds[0], // Primer equipo como principal
                'equipos_ids' => json_encode($equiposIds), // Todos los equipos seleccionados como JSON
                'id_usuario' => auth()->id() ?? 1,
                'id_usuario_solicitante' => $this->empleadoSeleccionado->id ?? null,
                'requiere_personal' => $this->requierePersonal,
                'ubicacion' => $this->ubicacion,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'motivo' => $this->motivo,
                'estado' => 'APROBADA',
                'usuario_creacion' => $this->empleadoSeleccionado->nombres ?? auth()->user()->name ?? 'publico',
                'fecha_creacion' => now(),
                'usuario_aprobacion' => auth()->user()->name ?? 'sistema',
                'fecha_aprobacion' => now(),
            ]);

            DB::commit();

            $count = count($this->selectedEquipos);
            $message = $count == 1 
                ? '¡Reserva confirmada automáticamente!' 
                : "¡Reserva para {$count} equipos confirmada automáticamente!";

            if ($this->ubicacion) {
                $message .= " Ubicación: {$this->ubicacion}";
            }

            $this->notificationMessage = $message;
            $this->showNotification = true;
            
            // Guardar fecha seleccionada antes de resetear
            $selectedDateBeforeReset = $this->selectedDate;
            
            // Resetear formulario
            $this->limpiarFormulario();
            
            // Recargar datos
            $this->loadEquipos();
            $this->loadExistingReservas();
            
            // Actualizar calendario automáticamente
            $this->dispatch('refreshCalendar');
            
            // Si había un día seleccionado, actualizar las reservas de ese día
            if ($selectedDateBeforeReset) {
                $this->selectedDate = $selectedDateBeforeReset;
                $this->selectDay($selectedDateBeforeReset->day);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar la reserva: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->limpiarFormulario();
    }

    public function render()
    {
        return view('livewire.public.reserva-equipo-simple');
    }
}