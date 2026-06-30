<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TipoUbicacionTicket;
use Illuminate\Support\Facades\Auth;

class TiposUbicacionTicket extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $ubicacion = '';
    public $estado = 1;
    
    // Propiedades de control
    public $tipoUbicacionTicketId;
    public $search = '';
    public $filterEstado = '';
    public $perPage = 10;
    
    // Modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    protected $paginationTheme = 'tailwind';
    
    protected $rules = [
        'ubicacion' => 'required|string|max:50',
        'estado' => 'required|boolean',
    ];
    
    protected $messages = [
        'ubicacion.required' => 'La ubicación es obligatoria',
        'ubicacion.max' => 'La ubicación no puede superar los 50 caracteres',
        'estado.required' => 'El estado es obligatorio',
    ];
    
    public function render()
    {
        $tiposUbicacionTicket = TipoUbicacionTicket::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('ubicacion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
            
        return view('livewire.admin.tipos-ubicacion-ticket', [
            'tiposUbicacionTicket' => $tiposUbicacionTicket
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
            TipoUbicacionTicket::create([
                'ubicacion' => $this->ubicacion,
                'estado' => $this->estado,
                'id_usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
                'estacion_creacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Tipo de ubicación ticket creado exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el tipo de ubicación ticket: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        try {
            $tipoUbicacionTicket = TipoUbicacionTicket::findOrFail($id);
            
            $this->tipoUbicacionTicketId = $tipoUbicacionTicket->id;
            $this->ubicacion = $tipoUbicacionTicket->ubicacion;
            $this->estado = $tipoUbicacionTicket->estado;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el tipo de ubicación ticket');
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
            $tipoUbicacionTicket = TipoUbicacionTicket::findOrFail($this->tipoUbicacionTicketId);
            
            $tipoUbicacionTicket->update([
                'ubicacion' => $this->ubicacion,
                'estado' => $this->estado,
                'id_usuario_modificacion' => Auth::id(),
                'fecha_modificacion' => now(),
                'estacion_modificacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Tipo de ubicación ticket actualizado exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el tipo de ubicación ticket: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->tipoUbicacionTicketId = $id;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->tipoUbicacionTicketId = null;
    }
    
    public function deleteTipoUbicacionTicket()
    {
        try {
            $tipoUbicacionTicket = TipoUbicacionTicket::findOrFail($this->tipoUbicacionTicketId);
            
            // Verificar si tiene registros asociados
            if ($tipoUbicacionTicket->clienteAtencionUbicacion()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el tipo de ubicación ticket porque tiene configuraciones de cliente asociadas');
                $this->cancelDelete();
                return;
            }
            
            if ($tipoUbicacionTicket->ventanillas()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el tipo de ubicación ticket porque tiene ventanillas asociadas');
                $this->cancelDelete();
                return;
            }
            
            $tipoUbicacionTicket->delete();
            
            session()->flash('message', 'Tipo de ubicación ticket eliminado exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el tipo de ubicación ticket: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }
    
    private function resetForm()
    {
        $this->reset(['ubicacion', 'estado', 'tipoUbicacionTicketId']);
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