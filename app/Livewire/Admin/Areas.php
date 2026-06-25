<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;

class Areas extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $nombre = '';
    public $abreviatura = '';
    public $estado = 1;
    
    // Propiedades de control
    public $areaId;
    public $search = '';
    public $filterEstado = '';
    public $perPage = 10;
    
    // Modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    protected $paginationTheme = 'tailwind';
    
    protected $rules = [
        'nombre' => 'required|string|max:100',
        'abreviatura' => 'required|string|max:10',
        'estado' => 'required|boolean',
    ];
    
    protected $messages = [
        'nombre.required' => 'El nombre del área es obligatorio',
        'nombre.max' => 'El nombre no puede superar los 100 caracteres',
        'abreviatura.required' => 'La abreviatura es obligatoria',
        'abreviatura.max' => 'La abreviatura no puede superar los 10 caracteres',
        'estado.required' => 'El estado es obligatorio',
    ];
    
    public function render()
    {
        $areas = Area::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('abreviatura', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
            
        return view('livewire.admin.area', [
            'areas' => $areas
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
            Area::create([
                'nombre' => $this->nombre,
                'abreviatura' => strtoupper($this->abreviatura),
                'estado' => $this->estado,
                'usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
            ]);
            
            session()->flash('message', 'Área creada exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el área: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        try {
            $area = Area::findOrFail($id);
            
            $this->areaId = $area->id;
            $this->nombre = $area->nombre;
            $this->abreviatura = $area->abreviatura;
            $this->estado = $area->estado;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el área');
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
            $area = Area::findOrFail($this->areaId);
            
            $area->update([
                'nombre' => $this->nombre,
                'abreviatura' => strtoupper($this->abreviatura),
                'estado' => $this->estado,
                'usuario_actualizacion' => Auth::id(),
                'fecha_actualizacion' => now(),
            ]);
            
            session()->flash('message', 'Área actualizada exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el área: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->areaId = $id;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->areaId = null;
    }
    
    public function deleteArea()
    {
        try {
            $area = Area::findOrFail($this->areaId);
            
            // Verificar si tiene tickets asociados
            if ($area->tickets()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el área porque tiene tickets asociados');
                $this->cancelDelete();
                return;
            }
            
            $area->delete();
            
            session()->flash('message', 'Área eliminada exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el área: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }
    
    private function resetForm()
    {
        $this->reset(['nombre', 'abreviatura', 'estado', 'areaId']);
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