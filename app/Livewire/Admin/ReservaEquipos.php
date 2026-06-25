<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ReservaEquipo;
use App\Models\Equipo;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservaEquipos extends Component
{
    public $reservas, $equipos, $usuarios;
    public $id_equipo, $id_usuario, $fecha_inicio, $fecha_fin, $motivo;
    public $reserva_id, $estado;
    public $search = '';
    public $filtro_estado = '';

    protected $rules = [
        'id_equipo' => 'required|exists:equipo,id',
        'id_usuario' => 'required|exists:users,id',
        'fecha_inicio' => 'required|date|after:now',
        'fecha_fin' => 'required|date|after:fecha_inicio',
        'motivo' => 'nullable|string|max:500'
    ];

    protected $messages = [
        'fecha_inicio.after' => 'La fecha de inicio debe ser posterior a la fecha actual.',
        'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $query = ReservaEquipo::with(['equipo', 'usuario'])
            ->when($this->search, function($query) {
                $query->whereHas('equipo', function($q) {
                    $q->where('codigo', 'like', '%' . $this->search . '%')
                      ->orWhere('marca', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('usuario', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filtro_estado, function($query) {
                $query->where('estado', $this->filtro_estado);
            })
            ->orderBy('fecha_inicio', 'desc');

        $this->reservas = $query->get();
        $this->equipos = Equipo::where('estado', '1')->get();
        $this->usuarios = User::where('estado', '1')->get();
    }

    public function render()
    {
        return view('livewire.admin.reserva-equipos');
    }

    public function store()
    {
        Log::info('=== CREAR RESERVA ===');
        
        $this->validate();

        try {
            // Verificar disponibilidad del equipo
            if (!$this->verificarDisponibilidad($this->id_equipo, $this->fecha_inicio, $this->fecha_fin)) {
                session()->flash('error', 'El equipo no está disponible en el horario seleccionado.');
                return;
            }

            ReservaEquipo::create([
                'id_equipo' => $this->id_equipo,
                'id_usuario' => $this->id_usuario,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'motivo' => $this->motivo,
                'estado' => 'PENDIENTE',
                'usuario_creacion' => auth()->user()->name ?? 'sistema',
                'fecha_creacion' => now(),
            ]);

            session()->flash('success', 'Reserva solicitada correctamente. Esperando aprobación.');
            $this->reset(['id_equipo', 'id_usuario', 'fecha_inicio', 'fecha_fin', 'motivo']);
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al crear reserva: ' . $e->getMessage());
            session()->flash('error', 'Error al registrar la reserva.');
        }
    }

    public function edit($id)
    {
        $reserva = ReservaEquipo::findOrFail($id);
        
        $this->reserva_id = $reserva->id;
        $this->id_equipo = $reserva->id_equipo;
        $this->id_usuario = $reserva->id_usuario;
        $this->fecha_inicio = $reserva->fecha_inicio->format('Y-m-d\TH:i');
        $this->fecha_fin = $reserva->fecha_fin->format('Y-m-d\TH:i');
        $this->motivo = $reserva->motivo;
        $this->estado = $reserva->estado;
    }

    public function update()
    {
        $this->validate();

        try {
            $reserva = ReservaEquipo::findOrFail($this->reserva_id);
            
            // Solo permitir editar si está pendiente
            if ($reserva->estado !== 'PENDIENTE') {
                session()->flash('error', 'Solo se pueden editar reservas pendientes.');
                return;
            }

            // Verificar disponibilidad si cambian fechas o equipo
            if ($reserva->id_equipo != $this->id_equipo || 
                $reserva->fecha_inicio->format('Y-m-d\TH:i') != $this->fecha_inicio ||
                $reserva->fecha_fin->format('Y-m-d\TH:i') != $this->fecha_fin) {
                
                if (!$this->verificarDisponibilidad($this->id_equipo, $this->fecha_inicio, $this->fecha_fin, $this->reserva_id)) {
                    session()->flash('error', 'El equipo no está disponible en el horario seleccionado.');
                    return;
                }
            }

            $reserva->update([
                'id_equipo' => $this->id_equipo,
                'id_usuario' => $this->id_usuario,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'motivo' => $this->motivo,
            ]);

            session()->flash('success', 'Reserva actualizada correctamente.');
            $this->reset(['id_equipo', 'id_usuario', 'fecha_inicio', 'fecha_fin', 'motivo', 'reserva_id']);
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al actualizar reserva: ' . $e->getMessage());
            session()->flash('error', 'Error al actualizar la reserva.');
        }
    }

    public function aprobar($id)
    {
        try {
            $reserva = ReservaEquipo::findOrFail($id);
            
            if ($reserva->estado !== 'PENDIENTE') {
                session()->flash('error', 'Solo se pueden aprobar reservas pendientes.');
                return;
            }

            // Verificar disponibilidad final
            if (!$this->verificarDisponibilidad($reserva->id_equipo, $reserva->fecha_inicio, $reserva->fecha_fin, $id)) {
                session()->flash('error', 'El equipo ya no está disponible en ese horario.');
                return;
            }

            $reserva->update([
                'estado' => 'APROBADA',
                'usuario_aprobacion' => auth()->user()->name,
                'fecha_aprobacion' => now(),
            ]);

            session()->flash('success', 'Reserva aprobada correctamente.');
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al aprobar reserva: ' . $e->getMessage());
            session()->flash('error', 'Error al aprobar la reserva.');
        }
    }

    public function rechazar($id)
    {
        try {
            $reserva = ReservaEquipo::findOrFail($id);
            
            if ($reserva->estado !== 'PENDIENTE') {
                session()->flash('error', 'Solo se pueden rechazar reservas pendientes.');
                return;
            }

            $reserva->update([
                'estado' => 'RECHAZADA',
                'usuario_aprobacion' => auth()->user()->name,
                'fecha_aprobacion' => now(),
            ]);

            session()->flash('success', 'Reserva rechazada correctamente.');
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al rechazar reserva: ' . $e->getMessage());
            session()->flash('error', 'Error al rechazar la reserva.');
        }
    }

    public function cancelar($id)
    {
        try {
            $reserva = ReservaEquipo::findOrFail($id);
            
            if (!in_array($reserva->estado, ['PENDIENTE', 'APROBADA'])) {
                session()->flash('error', 'Solo se pueden cancelar reservas pendientes o aprobadas.');
                return;
            }

            if ($reserva->fecha_inicio < now()) {
                session()->flash('error', 'No se pueden cancelar reservas que ya han comenzado.');
                return;
            }

            $reserva->update([
                'estado' => 'CANCELADA',
                'usuario_aprobacion' => auth()->user()->name,
                'fecha_aprobacion' => now(),
            ]);

            session()->flash('success', 'Reserva cancelada correctamente.');
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al cancelar reserva: ' . $e->getMessage());
            session()->flash('error', 'Error al cancelar la reserva.');
        }
    }

    public function delete($id)
    {
        try {
            $reserva = ReservaEquipo::findOrFail($id);
            
            if ($reserva->estado === 'APROBADA' && $reserva->fecha_inicio <= now()) {
                session()->flash('error', 'No se pueden eliminar reservas aprobadas que ya han comenzado.');
                return;
            }

            $reserva->delete();

            session()->flash('success', 'Reserva eliminada correctamente.');
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al eliminar reserva: ' . $e->getMessage());
            session()->flash('error', 'Error al eliminar la reserva.');
        }
    }

    private function verificarDisponibilidad($idEquipo, $fechaInicio, $fechaFin, $excluirId = null)
    {
        $query = ReservaEquipo::where('id_equipo', $idEquipo)
            ->where('estado', 'APROBADA')
            ->where(function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                      ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                      ->orWhere(function($q) use ($fechaInicio, $fechaFin) {
                          $q->where('fecha_inicio', '<=', $fechaInicio)
                            ->where('fecha_fin', '>=', $fechaFin);
                      });
            });

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        return !$query->exists();
    }

    public function cancel()
    {
        $this->reset(['id_equipo', 'id_usuario', 'fecha_inicio', 'fecha_fin', 'motivo', 'reserva_id']);
    }

    public function updatingSearch()
    {
        $this->loadData();
    }

    public function updatingFiltroEstado()
    {
        $this->loadData();
    }
}
