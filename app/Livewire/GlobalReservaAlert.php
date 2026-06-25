<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ReservaEquipo;
use Carbon\Carbon;

class GlobalReservaAlert extends Component
{
    public $showAlert = false;
    public $alertMessage = '';
    public $alertType = '';
    public $alertChecked = false;

    public function mount()
    {
        $this->checkUpcomingReservas();
    }

    public function isReservaUrgent($reserva)
    {
        if (!$reserva) return false;
        
        $now = Carbon::now();
        $fechaInicio = Carbon::parse($reserva->fecha_inicio);
        $diffInHours = $now->diffInHours($fechaInicio, false);
        
        return $diffInHours <= 2 && $diffInHours > 0;
    }

    public function checkUpcomingReservas()
    {
        $now = Carbon::now();
        $alerts = [];
        
        // Obtener reservas próximas (próximas 2 horas)
        $reservas = ReservaEquipo::with(['solicitante', 'equipo'])
            ->whereIn('estado', ['APROBADA', 'PENDIENTE'])
            ->where('fecha_inicio', '>', $now)
            ->where('fecha_inicio', '<', $now->copy()->addHours(2))
            ->orderBy('fecha_inicio', 'asc')
            ->get();
        
        foreach ($reservas as $reserva) {
            $fechaInicio = Carbon::parse($reserva->fecha_inicio);
            $diffInHours = $now->diffInHours($fechaInicio, false);
            $diffInMinutes = $now->diffInMinutes($fechaInicio, false);
            
            $alertKey = 'global_' . $reserva->id . '_' . session()->getId();
            $shownAlerts = session()->get('global_reserva_alerts', []);
            
            if (!in_array($alertKey, $shownAlerts) && !$this->alertChecked) {
                if ($diffInHours <= 2 && $diffInHours > 1.5 && $diffInMinutes > 0) {
                    $alerts[] = ['reserva' => $reserva, 'time' => '2 horas', 'type' => 'warning'];
                } elseif ($diffInHours <= 1 && $diffInHours > 0.5 && $diffInMinutes > 0) {
                    $alerts[] = ['reserva' => $reserva, 'time' => '1 hora', 'type' => 'danger'];
                } elseif ($diffInMinutes <= 30 && $diffInMinutes > 0) {
                    $alerts[] = ['reserva' => $reserva, 'time' => '30 minutos', 'type' => 'critical'];
                }
            }
        }
        
        if (!empty($alerts) && !$this->alertChecked) {
            // Mostrar la alerta más urgente
            $priority = ['critical' => 0, 'danger' => 1, 'warning' => 2];
            usort($alerts, function($a, $b) use ($priority) {
                return $priority[$a['type']] <=> $priority[$b['type']];
            });
            
            $alert = $alerts[0];
            $reserva = $alert['reserva'];
            
            $this->alertType = $alert['type'];
            $this->alertMessage = $this->buildAlertMessage($reserva, $alert['time']);
            $this->showAlert = true;
            
            // Marcar como mostrada
            $shownAlerts = session()->get('global_reserva_alerts', []);
            $shownAlerts[] = 'global_' . $reserva->id . '_' . session()->getId();
            session()->put('global_reserva_alerts', $shownAlerts);
        }
        
        $this->alertChecked = true;
    }

    private function buildAlertMessage($reserva, $time)
    {
        $icon = $time == '30 minutos' ? '🔴' : ($time == '1 hora' ? '🟠' : '🟡');
        $solicitante = $reserva->solicitante ? $reserva->solicitante->nombres : 'Usuario';
        $equipo = $reserva->equipo ? $reserva->equipo->codigo : 'Equipo';
        $ubicacion = $reserva->ubicacion ? " en {$reserva->ubicacion}" : '';
        
        return "{$icon} ¡ALERTA! Reserva en {$time}: {$reserva->motivo} - {$solicitante} - {$equipo}{$ubicacion} a las " . Carbon::parse($reserva->fecha_inicio)->format('H:i');
    }

    public function pollAlerts()
    {
        $this->alertChecked = false;
        $this->checkUpcomingReservas();
    }

    public function dismissAlert()
    {
        $this->showAlert = false;
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function render()
    {
        return view('livewire.global-reserva-alert');
    }
}