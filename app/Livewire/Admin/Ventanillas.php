<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ventanilla;
use App\Models\TipoUbicacionTicket;
use Illuminate\Support\Facades\Auth;

class Ventanillas extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $numero = '';
    public $descripcion = '';
    public $id_tipo_ubicacion_ticket = '';
    public $estado = 1;
    
    // Propiedades de control
    public $ventanillaId;
    public $search = '';
    public $filterEstado = '';
    public $filterTipoUbicacion = '';
    public $perPage = 10;
    
    // Modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    protected $paginationTheme = 'tailwind';
    
    protected $rules = [
        'numero' => 'required|integer|min:1|max:999',
        'descripcion' => 'nullable|string|max:100',
        'id_tipo_ubicacion_ticket' => 'required|exists:atencion.tipo_ubicacion_ticket,id',
        'estado' => 'required|boolean',
    ];
    
    protected $messages = [
        'numero.required' => 'El número de ventanilla es obligatorio',
        'numero.integer' => 'El número debe ser un valor entero',
        'numero.min' => 'El número debe ser mayor a 0',
        'numero.max' => 'El número no puede superar 999',
        'descripcion.max' => 'La descripción no puede superar los 100 caracteres',
        'id_tipo_ubicacion_ticket.required' => 'El tipo de ubicación ticket es obligatorio',
        'id_tipo_ubicacion_ticket.exists' => 'El tipo de ubicación ticket seleccionado no es válido',
        'estado.required' => 'El estado es obligatorio',
    ];
    
    public function render()
    {
        $tiposUbicacionTicket = TipoUbicacionTicket::where('estado', 1)->orderBy('ubicacion')->get();
        
        $ventanillas = Ventanilla::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('numero', 'like', '%' . $this->search . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->when($this->filterTipoUbicacion !== '', function ($query) {
                $query->where('id_tipo_ubicacion_ticket', $this->filterTipoUbicacion);
            })
            ->with('tipoUbicacionTicket')
            ->orderBy('numero', 'asc')
            ->paginate($this->perPage);
            
        return view('livewire.admin.ventanillas', [
            'ventanillas' => $ventanillas,
            'tiposUbicacionTicket' => $tiposUbicacionTicket
        ]);
    }
    
    public function clearFilters()
    {
        $this->reset(['search', 'filterEstado', 'filterTipoUbicacion']);
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
            Ventanilla::create([
                'numero' => $this->numero,
                'descripcion' => $this->descripcion,
                'id_tipo_ubicacion_ticket' => $this->id_tipo_ubicacion_ticket,
                'estado' => $this->estado,
                'id_usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
                'estacion_creacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Ventanilla creada exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la ventanilla: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        try {
            $ventanilla = Ventanilla::findOrFail($id);
            
            $this->ventanillaId = $ventanilla->id;
            $this->numero = $ventanilla->numero;
            $this->descripcion = $ventanilla->descripcion;
            $this->id_tipo_ubicacion_ticket = $ventanilla->id_tipo_ubicacion_ticket;
            $this->estado = $ventanilla->estado;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar la ventanilla');
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
            $ventanilla = Ventanilla::findOrFail($this->ventanillaId);
            
            $ventanilla->update([
                'numero' => $this->numero,
                'descripcion' => $this->descripcion,
                'id_tipo_ubicacion_ticket' => $this->id_tipo_ubicacion_ticket,
                'estado' => $this->estado,
                'id_usuario_modificacion' => Auth::id(),
                'fecha_modificacion' => now(),
                'estacion_modificacion' => request()->ip(),
            ]);
            
            session()->flash('message', 'Ventanilla actualizada exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la ventanilla: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->ventanillaId = $id;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->ventanillaId = null;
    }
    
    public function deleteVentanilla()
    {
        try {
            $ventanilla = Ventanilla::findOrFail($this->ventanillaId);
            
            // Verificar si tiene registros asociados
            if ($ventanilla->atencionVentanilla()->count() > 0) {
                session()->flash('error', 'No se puede eliminar la ventanilla porque tiene configuraciones de atención asociadas');
                $this->cancelDelete();
                return;
            }
            
            $ventanilla->delete();
            
            session()->flash('message', 'Ventanilla eliminada exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la ventanilla: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }
    
    private function resetForm()
    {
        $this->reset(['numero', 'descripcion', 'id_tipo_ubicacion_ticket', 'estado', 'ventanillaId']);
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
    
    public function updatingFilterTipoUbicacion()
    {
        $this->resetPage();
    }
}