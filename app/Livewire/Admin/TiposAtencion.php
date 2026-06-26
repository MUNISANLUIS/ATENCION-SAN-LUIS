<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TipoAtencion;
use Illuminate\Support\Facades\Auth;

class TiposAtencion extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $inicial = '';
    public $nombre = '';
    public $peso = '';
    public $estado = 1;
    
    // Propiedades de control
    public $tipoAtencionId;
    public $search = '';
    public $filterEstado = '';
    public $perPage = 10;
    
    // Modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    protected $paginationTheme = 'tailwind';
    
    protected $rules = [
        'inicial' => 'required|string|max:1',
        'nombre' => 'required|string|max:100',
        'peso' => 'required|string|max:2',
        'estado' => 'required|boolean',
    ];
    
    protected $messages = [
        'inicial.required' => 'La inicial es obligatoria',
        'inicial.max' => 'La inicial no puede superar 1 caracter',
        'nombre.required' => 'El nombre del tipo de atención es obligatorio',
        'nombre.max' => 'El nombre no puede superar los 100 caracteres',
        'peso.required' => 'El peso es obligatorio',
        'peso.max' => 'El peso no puede superar los 2 caracteres',
        'estado.required' => 'El estado es obligatorio',
    ];
    
    public function render()
    {
        $tiposAtencion = TipoAtencion::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('inicial', 'like', '%' . $this->search . '%')
                      ->orWhere('peso', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
            
        return view('livewire.admin.tipos-atencion', [
            'tiposAtencion' => $tiposAtencion
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
            TipoAtencion::create([
                'inicial' => strtoupper($this->inicial),
                'nombre' => $this->nombre,
                'peso' => $this->peso,
                'estado' => $this->estado,
                'id_usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
                'estacion_creacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Tipo de atención creado exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el tipo de atención: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        try {
            $tipoAtencion = TipoAtencion::findOrFail($id);
            
            $this->tipoAtencionId = $tipoAtencion->id;
            $this->inicial = $tipoAtencion->inicial;
            $this->nombre = $tipoAtencion->nombre;
            $this->peso = $tipoAtencion->peso;
            $this->estado = $tipoAtencion->estado;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el tipo de atención');
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
            $tipoAtencion = TipoAtencion::findOrFail($this->tipoAtencionId);
            
            $tipoAtencion->update([
                'inicial' => strtoupper($this->inicial),
                'nombre' => $this->nombre,
                'peso' => $this->peso,
                'estado' => $this->estado,
                'id_usuario_modificacion' => Auth::id(),
                'fecha_modificacion' => now(),
                'estacion_modificacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Tipo de atención actualizado exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el tipo de atención: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->tipoAtencionId = $id;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->tipoAtencionId = null;
    }
    
    public function deleteTipoAtencion()
    {
        try {
            $tipoAtencion = TipoAtencion::findOrFail($this->tipoAtencionId);
            
            // Verificar si tiene tickets asociados
            if ($tipoAtencion->tickets()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el tipo de atención porque tiene tickets asociados');
                $this->cancelDelete();
                return;
            }
            
            $tipoAtencion->delete();
            
            session()->flash('message', 'Tipo de atención eliminado exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el tipo de atención: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }
    
    private function resetForm()
    {
        $this->reset(['inicial', 'nombre', 'peso', 'estado', 'tipoAtencionId']);
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