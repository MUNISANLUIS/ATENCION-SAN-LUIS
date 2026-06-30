<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\Auth;

class Ubicaciones extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $nombre = '';
    public $estado = 1;
    
    // Propiedades de control
    public $ubicacionId;
    public $search = '';
    public $filterEstado = '';
    public $perPage = 10;
    
    // Modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    protected $paginationTheme = 'tailwind';
    
    protected $rules = [
        'nombre' => 'required|string|max:50',
        'estado' => 'required|boolean',
    ];
    
    protected $messages = [
        'nombre.required' => 'El nombre de la ubicación es obligatorio',
        'nombre.max' => 'El nombre no puede superar los 50 caracteres',
        'estado.required' => 'El estado es obligatorio',
    ];
    
    public function render()
    {
        $ubicaciones = Ubicacion::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
            
        return view('livewire.admin.ubicaciones', [
            'ubicaciones' => $ubicaciones
        ]);
    }
    
    public function clearFilters()
    {
        $this->reset(['search', 'filterEstado']);
        $this->resetPage();
    }
    
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }
    
    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }
    
    public function store()
    {
        $this->validate();
        
        try {
            Ubicacion::create([
                'nombre' => $this->nombre,
                'estado' => $this->estado,
                'id_usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
                'estacion_creacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Ubicación creada exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la ubicación: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        try {
            $ubicacion = Ubicacion::findOrFail($id);
            
            $this->ubicacionId = $ubicacion->id;
            $this->nombre = $ubicacion->nombre;
            $this->estado = $ubicacion->estado;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar la ubicación');
        }
    }
    
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }
    
    public function update()
    {
        $this->validate();
        
        try {
            $ubicacion = Ubicacion::findOrFail($this->ubicacionId);
            
            $ubicacion->update([
                'nombre' => $this->nombre,
                'estado' => $this->estado,
                'id_usuario_modificacion' => Auth::id(),
                'fecha_modificacion' => now(),
                'estacion_modificacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Ubicación actualizada exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la ubicación: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->ubicacionId = $id;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->ubicacionId = null;
    }
    
    public function deleteUbicacion()
    {
        try {
            $ubicacion = Ubicacion::findOrFail($this->ubicacionId);
            
            // Verificar si tiene registros asociados
            if ($ubicacion->atencionVentanilla()->count() > 0) {
                session()->flash('error', 'No se puede eliminar la ubicación porque tiene configuraciones de atención asociadas');
                $this->cancelDelete();
                return;
            }
            
            if ($ubicacion->clienteAtencionUbicacion()->count() > 0) {
                session()->flash('error', 'No se puede eliminar la ubicación porque tiene configuraciones de cliente asociadas');
                $this->cancelDelete();
                return;
            }
            
            $ubicacion->delete();
            
            session()->flash('message', 'Ubicación eliminada exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la ubicación: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }
    
    private function resetForm()
    {
        $this->reset(['nombre', 'estado', 'ubicacionId']);
        $this->estado = 1;
        $this->resetValidation();
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterEstado()
    {
        $this->resetPage();
    }
}