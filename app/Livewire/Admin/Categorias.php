<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CategoriaIncidencia;
use Illuminate\Support\Facades\Auth;

class Categorias extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $orden;
    public $nombre = '';
    public $estado = 1;
    
    // Propiedades de control
    public $categoriaId;
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
        'estado' => 'required|boolean',
        'orden' => 'nullable|integer|min:0',
    ];
    
    protected $messages = [
        'nombre.required' => 'El nombre de la categoría es obligatorio',
        'nombre.max' => 'El nombre no puede superar los 100 caracteres',
        'estado.required' => 'El estado es obligatorio',
    ];
    
    public function render()
    {
        $categorias = CategoriaIncidencia::query()
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
            
        return view('livewire.admin.categorias', [
            'categorias' => $categorias
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
            CategoriaIncidencia::create([
                'nombre' => $this->nombre,
                'estado' => $this->estado,
                'orden' => $this->orden ?? 0,
                'usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
            ]);
            
            session()->flash('message', 'Categoría creada exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la categoría: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        try {
            $categoria = CategoriaIncidencia::findOrFail($id);
            
            $this->categoriaId = $categoria->id;
            $this->nombre = $categoria->nombre;
            $this->estado = $categoria->estado;
            $this->orden = $categoria->orden;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar la categoría');
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
            $categoria = CategoriaIncidencia::findOrFail($this->categoriaId);
            
            $categoria->update([
                'nombre' => $this->nombre,
                'estado' => $this->estado,
                'orden' => $this->orden ?? 0,
                'usuario_actualizacion' => Auth::id(),
                'fecha_actualizacion' => now(),
            ]);
            
            session()->flash('message', 'Categoría actualizada exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la categoría: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->categoriaId = $id;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->categoriaId = null;
    }
    
    public function deleteCategoria()
    {
        try {
            $categoria = CategoriaIncidencia::findOrFail($this->categoriaId);
            
            // Verificar si tiene tickets asociados
            if ($categoria->tickets()->count() > 0) {
                session()->flash('error', 'No se puede eliminar la categoría porque tiene tickets asociados');
                $this->cancelDelete();
                return;
            }
            
            $categoria->delete();
            
            session()->flash('message', 'Categoría eliminada exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la categoría: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }
    
    private function resetForm()
    {
        $this->reset(['nombre', 'estado', 'orden', 'categoriaId']);
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