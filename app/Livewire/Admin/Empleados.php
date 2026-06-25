<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Empleado;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;

class Empleados extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $nombres = '';
    public $id_area;
    public $estado = 1;

    // Propiedades de control
    public $empleadoId;
    public $search = '';
    public $filterEstado = '';
    public $filterArea = '';
    public $perPage = 10;

    // Modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'nombres' => 'required|string|max:255',
        'id_area' => 'required|exists:areas,id',
        'estado' => 'required|boolean',
    ];

    protected $messages = [
        'nombres.required' => 'El nombre del empleado es obligatorio',
        'nombres.max' => 'El nombre no puede superar los 255 caracteres',
        'id_area.required' => 'El área es obligatoria',
        'id_area.exists' => 'El área seleccionada no existe',
        'estado.required' => 'El estado es obligatorio',
    ];

    public function render()
    {
        $empleados = Empleado::query()
            ->with('area') // Cargar relación
            ->when($this->search, function ($query) {
                $query->where('nombres', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->when($this->filterArea, function ($query) {
                $query->where('id_area', $this->filterArea);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
        
        $areas = Area::where('estado', 1)->orderBy('nombre')->get();
            
        return view('livewire.admin.empleados', [
            'empleados' => $empleados,
            'areas' => $areas
        ]);
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterEstado', 'filterArea']);
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
            Empleado::create([
                'nombres' => $this->nombres,
                'id_area' => $this->id_area,
                'estado' => $this->estado,
                'usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
            ]);
            
            session()->flash('message', 'Empleado creado exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el empleado: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $empleado = Empleado::findOrFail($id);
            
            $this->empleadoId = $empleado->id;
            $this->nombres = $empleado->nombres;
            $this->id_area = $empleado->id_area;
            $this->estado = $empleado->estado;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el empleado');
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
            $empleado = Empleado::findOrFail($this->empleadoId);
            
            $empleado->update([
                'nombres' => $this->nombres,
                'id_area' => $this->id_area,
                'estado' => $this->estado,
                'usuario_actualizacion' => Auth::id(),
                'fecha_actualizacion' => now(),
            ]);
            
            session()->flash('message', 'Empleado actualizado exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el empleado: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->empleadoId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->empleadoId = null;
    }

    public function deleteEmpleado()
    {
        try {
            $empleado = Empleado::findOrFail($this->empleadoId);
            $empleado->delete();
            
            session()->flash('message', 'Empleado eliminado exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el empleado: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }

    private function resetForm()
    {
        $this->reset(['nombres', 'id_area', 'estado', 'empleadoId']);
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

    public function updatingFilterArea()
    {
        $this->resetPage();
    }
}