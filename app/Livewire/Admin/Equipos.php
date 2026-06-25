<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Equipo;
use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class Equipos extends Component
{
    public $equipos, $areas, $usuarios;
    public $tipo, $codigo, $marca, $modelo, $id_area, $id_usuario;
    public $equipo_id, $estado;
    public $search = '';

    protected $rules = [
        'tipo' => 'required|string|max:50',
        'codigo' => 'required|string|max:50',
        'marca' => 'nullable|string|max:100',
        'modelo' => 'nullable|string|max:100',
        'id_area' => 'nullable|integer',
        'id_usuario' => 'nullable|integer',
    ];

    public function mount()
    {
        $this->loadData();
        
        // Establecer valores por defecto
        $areaTI = Area::where('nombre', 'SUBGERENCIA DE TECNOLOGÍA DE LA INFORMACIÓN')->first();
        if ($areaTI) {
            $this->id_area = $areaTI->id;
        } else {
            // Si no encuentra el área específica, usar ID 27 directamente
            $this->id_area = 27;
        }
        
        // Buscar usuario con rol TI (id_tipo_usuario_rol = 2 generalmente)
        $usuarioTI = User::where('id_tipo_usuario_rol', 2)->first();
        if ($usuarioTI) {
            $this->id_usuario = $usuarioTI->id;
        }
    }

    public function loadData()
    {
        $this->equipos = Equipo::with(['area', 'usuario'])
            ->when($this->search, function($query) {
                $query->where('codigo', 'like', '%' . $this->search . '%')
                      ->orWhere('marca', 'like', '%' . $this->search . '%')
                      ->orWhere('modelo', 'like', '%' . $this->search . '%');
            })
            ->get();

        $this->areas = Area::where('estado', '1')->get();
        $this->usuarios = User::where('estado', '1')->get();
    }

    public function render()
    {
        return view('livewire.admin.equipo');
    }

    public function store()
    {
        Log::info('=== CREAR EQUIPO ===');
        
        $this->validate();

        try {
            // Asegurar que siempre haya un área
            if (!$this->id_area) {
                $areaTI = Area::where('nombre', 'SUBGERENCIA DE TECNOLOGÍA DE LA INFORMACIÓN')->first();
                $this->id_area = $areaTI ? $areaTI->id : 27;
            }
            
            // Asegurar que siempre haya un usuario
            if (!$this->id_usuario) {
                $usuarioTI = User::where('id_tipo_usuario_rol', 2)->first();
                $this->id_usuario = $usuarioTI ? $usuarioTI->id : User::first()->id;
            }
            
            // Obtener usuario TI como default para usuario_creacion
            $usuarioTI = User::where('id_tipo_usuario_rol', 2)->first();
            $nombreUsuarioCreacion = $usuarioTI ? $usuarioTI->name : (auth()->user()->name ?? 'TI');

            Equipo::create([
                'tipo' => $this->tipo,
                'codigo' => $this->codigo,
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'estado' => '1',
                'id_area' => $this->id_area,
                'id_usuario' => $this->id_usuario,
                'usuario_creacion' => $nombreUsuarioCreacion,
                'fecha_creacion' => now(),
            ]);

            session()->flash('success', 'Equipo registrado correctamente.');
            $this->reset(['tipo', 'codigo', 'marca', 'modelo', 'id_area', 'id_usuario']);
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al crear equipo: ' . $e->getMessage());
            session()->flash('error', 'Error al registrar el equipo.');
        }
    }

    public function edit($id)
    {
        $equipo = Equipo::findOrFail($id);
        
        $this->equipo_id = $equipo->id;
        $this->tipo = $equipo->tipo;
        $this->codigo = $equipo->codigo;
        $this->marca = $equipo->marca;
        $this->modelo = $equipo->modelo;
        $this->id_area = $equipo->id_area;
        $this->id_usuario = $equipo->id_usuario;
        $this->estado = $equipo->estado;
    }

    public function update()
    {
        $this->validate();

        try {
            $equipo = Equipo::findOrFail($this->equipo_id);
            
            $equipo->update([
                'tipo' => $this->tipo,
                'codigo' => $this->codigo,
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'id_area' => $this->id_area,
                'id_usuario' => $this->id_usuario,
                'usuario_actualizacion' => auth()->user()->name ?? 'sistema',
                'fecha_actualizacion' => now(),
            ]);

            session()->flash('success', 'Equipo actualizado correctamente.');
            $this->reset(['tipo', 'codigo', 'marca', 'modelo', 'id_area', 'id_usuario', 'equipo_id']);
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al actualizar equipo: ' . $e->getMessage());
            session()->flash('error', 'Error al actualizar el equipo.');
        }
    }

    public function delete($id)
    {
        try {
            $equipo = Equipo::findOrFail($id);
            
            // Verificar si tiene reservas activas
            if ($equipo->reservas()->where('estado', 'APROBADA')->exists()) {
                session()->flash('error', 'No se puede eliminar el equipo. Tiene reservas activas.');
                return;
            }

            $equipo->update([
                'estado' => '0',
                'usuario_actualizacion' => auth()->user()->name ?? 'sistema',
                'fecha_actualizacion' => now(),
            ]);

            session()->flash('success', 'Equipo desactivado correctamente.');
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al eliminar equipo: ' . $e->getMessage());
            session()->flash('error', 'Error al desactivar el equipo.');
        }
    }

    public function cambiarEstado($id)
    {
        try {
            $equipo = Equipo::findOrFail($id);
            $nuevoEstado = $equipo->estado == '1' ? '0' : '1';
            
            $equipo->update([
                'estado' => $nuevoEstado,
                'usuario_actualizacion' => auth()->user()->name ?? 'sistema',
                'fecha_actualizacion' => now(),
            ]);

            $mensaje = $nuevoEstado == '1' ? 'activado' : 'desactivado';
            session()->flash('success', "Equipo {$mensaje} correctamente.");
            $this->loadData();

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            session()->flash('error', 'Error al cambiar estado del equipo.');
        }
    }

    public function cancel()
    {
        $this->reset(['tipo', 'codigo', 'marca', 'modelo', 'id_area', 'id_usuario', 'equipo_id']);
    }

    public function updatingSearch()
    {
        $this->loadData();
    }
}
