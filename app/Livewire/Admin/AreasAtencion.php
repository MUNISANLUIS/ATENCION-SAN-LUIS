<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AreasAtencion as AreasAtencionModel;
use Illuminate\Support\Facades\Auth;

class AreasAtencion extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $nombre = '';
    public $abreviatura = '';
    public $estado = 1;
    
    // Propiedades de control
    public $areaAtencionId;
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
        'abreviatura' => 'required|string|max:10',
        'estado' => 'required|boolean',
    ];
    
    protected $messages = [
        'nombre.required' => 'El nombre del área de atención es obligatorio',
        'nombre.max' => 'El nombre no puede superar los 50 caracteres',
        'abreviatura.required' => 'La abreviatura es obligatoria',
        'abreviatura.max' => 'La abreviatura no puede superar los 10 caracteres',
        'estado.required' => 'El estado es obligatorio',
    ];
    
    public function render()
    {
        $areasAtencion = AreasAtencionModel::query()
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
            
        return view('livewire.admin.areas-atencion', [
            'areasAtencion' => $areasAtencion
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
            AreasAtencionModel::create([
                'nombre' => $this->nombre,
                'abreviatura' => strtoupper($this->abreviatura),
                'estado' => $this->estado,
                'id_usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
                'estacion_creacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Área de atención creada exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el área de atención: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        try {
            $areaAtencion = AreasAtencionModel::findOrFail($id);
            
            $this->areaAtencionId = $areaAtencion->id;
            $this->nombre = $areaAtencion->nombre;
            $this->abreviatura = $areaAtencion->abreviatura;
            $this->estado = $areaAtencion->estado;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el área de atención');
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
            $areaAtencion = AreasAtencionModel::findOrFail($this->areaAtencionId);
            
            $areaAtencion->update([
                'nombre' => $this->nombre,
                'abreviatura' => strtoupper($this->abreviatura),
                'estado' => $this->estado,
                'id_usuario_modificacion' => Auth::id(),
                'fecha_modificacion' => now(),
                'estacion_modificacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Área de atención actualizada exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el área de atención: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->areaAtencionId = $id;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->areaAtencionId = null;
    }
    
    public function deleteAreaAtencion()
    {
        try {
            $areaAtencion = AreasAtencionModel::findOrFail($this->areaAtencionId);
            
           
            if ($areaAtencion->empleados()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el área de atención porque tiene empleados asociados');
                $this->cancelDelete();
                return;
            }
            
            if ($areaAtencion->tickets()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el área de atención porque tiene tickets asociados');
                $this->cancelDelete();
                return;
            }
            
            $areaAtencion->delete();
            
            session()->flash('message', 'Área de atención eliminada exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el área de atención: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }
    
    private function resetForm()
    {
        $this->reset(['nombre', 'abreviatura', 'estado', 'areaAtencionId']);
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