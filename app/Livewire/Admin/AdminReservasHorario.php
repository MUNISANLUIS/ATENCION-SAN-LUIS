<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ReservaEquipo;
use Carbon\Carbon;

class AdminReservasHorario extends Component
{
    public $selectedDate = null;
    public $selectedMonth;
    public $selectedYear;
    public $selectedDayReservas = [];
    public $currentReservaIndex = 0;
    
    // Propiedades para alertas
    public $showAlert = false;
    public $alertMessage = '';
    public $alertType = '';
    public $upcomingReservas = [];
    public $alertChecked = false;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
        $this->checkUpcomingReservas();
    }

    /**
     * Verifica si una reserva es urgente (menos de 2 horas)
     */
    public function isReservaUrgent($reserva)
    {
        if (!$reserva) return false;
        
        $now = Carbon::now();
        $fechaInicio = Carbon::parse($reserva->fecha_inicio);
        $diffInHours = $now->diffInHours($fechaInicio, false);
        
        return $diffInHours <= 2 && $diffInHours > 0;
    }

    /**
     * Verifica reservas próximas y genera alertas
     */
    public function checkUpcomingReservas()
    {
        $now = Carbon::now();
        $alerts = [];
        
        // Obtener reservas activas y pendientes
        $reservas = ReservaEquipo::with(['equipo', 'solicitante'])
            ->whereIn('estado', ['APROBADA', 'PENDIENTE'])
            ->where('fecha_inicio', '>', $now)
            ->orderBy('fecha_inicio', 'asc')
            ->get();
        
        foreach ($reservas as $reserva) {
            $fechaInicio = Carbon::parse($reserva->fecha_inicio);
            $diffInHours = $now->diffInHours($fechaInicio, false);
            $diffInMinutes = $now->diffInMinutes($fechaInicio, false);
            
            // Evitar duplicados - verificar si ya se mostró esta alerta
            $alertKey = $reserva->id . '_' . $this->getAlertTypeByTime($diffInHours, $diffInMinutes);
            
            // Solo mostrar si no se ha mostrado antes (usando sesión)
            $shownAlerts = session()->get('shown_alerts', []);
            
            // Verificar alertas según el tiempo restante
            if (!$this->alertChecked && !in_array($alertKey, $shownAlerts)) {
                if ($diffInHours <= 2 && $diffInHours > 1.5 && $diffInMinutes > 0) {
                    // 2 horas antes
                    $alerts[] = [
                        'reserva' => $reserva,
                        'time' => '2 horas',
                        'type' => 'warning',
                        'key' => $alertKey
                    ];
                } elseif ($diffInHours <= 1 && $diffInHours > 0.5 && $diffInMinutes > 0) {
                    // 1 hora antes
                    $alerts[] = [
                        'reserva' => $reserva,
                        'time' => '1 hora',
                        'type' => 'danger',
                        'key' => $alertKey
                    ];
                } elseif ($diffInMinutes <= 30 && $diffInMinutes > 0 && $diffInMinutes <= 30) {
                    // 30 minutos antes
                    $alerts[] = [
                        'reserva' => $reserva,
                        'time' => '30 minutos',
                        'type' => 'critical',
                        'key' => $alertKey
                    ];
                }
            }
        }
        
        // Mostrar la alerta más próxima (la más urgente)
        if (!empty($alerts) && !$this->alertChecked) {
            $this->showUrgentAlert($alerts);
        }
        
        $this->alertChecked = true;
    }
    
    /**
     * Determina el tipo de alerta según el tiempo
     */
    private function getAlertTypeByTime($diffInHours, $diffInMinutes)
    {
        if ($diffInHours <= 2 && $diffInHours > 1.5) {
            return '2h';
        } elseif ($diffInHours <= 1 && $diffInHours > 0.5) {
            return '1h';
        } elseif ($diffInMinutes <= 30 && $diffInMinutes > 0) {
            return '30m';
        }
        return 'other';
    }
    
    /**
     * Muestra la alerta más urgente
     */
    private function showUrgentAlert($alerts)
    {
        // Ordenar por urgencia: critical > danger > warning
        $priority = ['critical' => 0, 'danger' => 1, 'warning' => 2];
        usort($alerts, function($a, $b) use ($priority) {
            return $priority[$a['type']] <=> $priority[$b['type']];
        });
        
        $alert = $alerts[0];
        $reserva = $alert['reserva'];
        
        $this->alertType = $alert['type'];
        $this->alertMessage = $this->buildAlertMessage($reserva, $alert['time']);
        $this->showAlert = true;
        
        // Guardar que esta alerta ya se mostró
        $shownAlerts = session()->get('shown_alerts', []);
        $shownAlerts[] = $alert['key'];
        session()->put('shown_alerts', $shownAlerts);
        
        // Limpiar alertas viejas de la sesión (más de 24 horas)
        $this->cleanOldAlerts();
    }
    
    /**
     * Construye el mensaje de alerta
     */
    private function buildAlertMessage($reserva, $time)
    {
        $icon = $time == '30 minutos' ? '🔴' : ($time == '1 hora' ? '🟠' : '🟡');
        $solicitante = $reserva->solicitante ? $reserva->solicitante->nombres : 'Usuario';
        $equipo = $reserva->equipo ? $reserva->equipo->codigo : 'Equipo';
        $ubicacion = $reserva->ubicacion ? " en {$reserva->ubicacion}" : '';
        
        return "{$icon} ¡ALERTA! Reserva en {$time}: {$reserva->motivo} - {$solicitante} - {$equipo}{$ubicacion} a las " . Carbon::parse($reserva->fecha_inicio)->format('H:i');
    }
    
    /**
     * Limpia alertas viejas de la sesión
     */
    private function cleanOldAlerts()
    {
        $shownAlerts = session()->get('shown_alerts', []);
        
        if (count($shownAlerts) > 100) {
            session()->put('shown_alerts', array_slice($shownAlerts, -50));
        }
    }
    
    /**
     * Cierra la alerta actual
     */
    public function dismissAlert()
    {
        $this->showAlert = false;
        $this->alertMessage = '';
        $this->alertType = '';
    }
    
    /**
     * Verifica automáticamente cada minuto (usando polling)
     */
    public function pollAlerts()
    {
        $this->alertChecked = false;
        $this->checkUpcomingReservas();
    }

    public function getCalendarDays()
    {
        $days = [];
        $firstDay = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $lastDay = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        // Espacios vacíos antes del primer día
        $startDayOfWeek = $firstDay->dayOfWeek;
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $days[] = [
                'isEmpty' => true,
                'day' => null,
                'isToday' => false,
                'reservas' => collect()
            ];
        }
        
        // Días del mes
        for ($day = 1; $day <= $lastDay->day; $day++) {
            $currentDate = Carbon::create($this->selectedYear, $this->selectedMonth, $day);
            $dayReservas = ReservaEquipo::with(['equipo', 'solicitante'])
                ->whereDate('fecha_inicio', $currentDate)
                ->orderBy('fecha_inicio', 'asc')
                ->get();
            
            $days[] = [
                'isEmpty' => false,
                'day' => $day,
                'isToday' => $currentDate->isToday(),
                'reservas' => $dayReservas
            ];
        }
        
        return $days;
    }

    public function selectDay($day)
    {
        $this->selectedDate = Carbon::create($this->selectedYear, $this->selectedMonth, $day);
        
        $this->selectedDayReservas = ReservaEquipo::with(['equipo', 'solicitante'])
            ->whereDate('fecha_inicio', $this->selectedDate)
            ->orderBy('fecha_inicio', 'asc')
            ->get();
        
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

    public function previousMonth()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->selectedDate = null;
        $this->selectedDayReservas = [];
        $this->currentReservaIndex = 0;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->selectedDate = null;
        $this->selectedDayReservas = [];
        $this->currentReservaIndex = 0;
    }

    public function getEquiposTypes($equiposIds)
    {
        if (!$equiposIds) {
            return null;
        }
        
        if (is_array($equiposIds)) {
            $ids = $equiposIds;
        } elseif (is_string($equiposIds) && str_contains($equiposIds, '[')) {
            $ids = json_decode($equiposIds, true);
        } else {
            $ids = explode(',', $equiposIds);
        }
        
        $equiposCatalogo = \App\Models\Equipo::pluck('tipo', 'id')->toArray();
        $types = [];
        
        foreach ($ids as $id) {
            $id = trim($id);
            if (isset($equiposCatalogo[$id])) {
                $types[] = $equiposCatalogo[$id];
            }
        }
        
        return empty($types) ? null : implode(', ', $types);
    }

    public function render()
    {
        $currentReserva = null;
        if ($this->selectedDayReservas && $this->selectedDayReservas->count() > 0) {
            if ($this->currentReservaIndex >= $this->selectedDayReservas->count()) {
                $this->currentReservaIndex = $this->selectedDayReservas->count() - 1;
            }
            if ($this->currentReservaIndex < 0) {
                $this->currentReservaIndex = 0;
            }
            $currentReserva = $this->selectedDayReservas->slice($this->currentReservaIndex, 1)->first();
        }
        
        return view('livewire.admin.admin-reservas-horario', [
            'calendarDays' => $this->getCalendarDays(),
            'selectedDayReservas' => $this->selectedDayReservas,
            'currentReserva' => $currentReserva,
            'currentReservaIndex' => $this->currentReservaIndex,
            'selectedDate' => $this->selectedDate
        ]);
    }
}