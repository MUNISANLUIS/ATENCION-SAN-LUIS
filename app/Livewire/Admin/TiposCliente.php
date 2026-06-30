<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TipoCliente;
use Illuminate\Support\Facades\Auth;

class TiposCliente extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $nombre = '';
    public $peso = '';
    public $estado = 1;
    
    // Propiedades de control
    public $tipoClienteId;
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
        'peso' => 'required|integer|min:1|max:999',
        'estado' => 'required|boolean',
    ];
    
    protected $messages = [
        'nombre.required' => 'El nombre del tipo de cliente es obligatorio',
        'nombre.max' => 'El nombre no puede superar los 50 caracteres',
        'peso.required' => 'El peso es obligatorio',
        'peso.integer' => 'El peso debe ser un número entero',
        'peso.min' => 'El peso debe ser mayor a 0',
        'peso.max' => 'El peso no puede superar 999',
        'estado.required' => 'El estado es obligatorio',
    ];
    
    public function render()
    {
        $tiposCliente = TipoCliente::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('peso', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->orderBy('peso', 'asc')
            ->paginate($this->perPage);
            
        return view('livewire.admin.tipos-cliente', [
            'tiposCliente' => $tiposCliente
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
            TipoCliente::create([
                'nombre' => $this->nombre,
                'peso' => $this->peso,
                'estado' => $this->estado,
                'id_usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
                'estacion_creacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Tipo de cliente creado exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el tipo de cliente: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($id);
            
            $this->tipoClienteId = $tipoCliente->id;
            $this->nombre = $tipoCliente->nombre;
            $this->peso = $tipoCliente->peso;
            $this->estado = $tipoCliente->estado;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el tipo de cliente');
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
            $tipoCliente = TipoCliente::findOrFail($this->tipoClienteId);
            
            $tipoCliente->update([
                'nombre' => $this->nombre,
                'peso' => $this->peso,
                'estado' => $this->estado,
                'id_usuario_modificacion' => Auth::id(),
                'fecha_modificacion' => now(),
                'estacion_modificacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Tipo de cliente actualizado exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el tipo de cliente: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->tipoClienteId = $id;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->tipoClienteId = null;
    }
    
    public function deleteTipoCliente()
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($this->tipoClienteId);
            
            // Verificar si tiene registros asociados en tcliente_tatencion_tubicacion
            if ($tipoCliente->clienteAtencionUbicacion()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el tipo de cliente porque tiene configuraciones asociadas');
                $this->cancelDelete();
                return;
            }
            
            $tipoCliente->delete();
            
            session()->flash('message', 'Tipo de cliente eliminado exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el tipo de cliente: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }
    
    private function resetForm()
    {
        $this->reset(['nombre', 'peso', 'estado', 'tipoClienteId']);
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