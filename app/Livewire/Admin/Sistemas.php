<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sistema;
use Illuminate\Support\Facades\Auth;

class Sistemas extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $nombre = '';
    public $url_base = '';
    public $headers = '';
    public $intervalo_minutos = 5;
    public $timeout_segundos = 30;
    public $estado = 1;

    // Propiedades de control
    public $sistemaId;
    public $search = '';
    public $filterEstado = '';
    public $perPage = 10;

    // Modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'url_base' => 'required|url|max:500',
        'headers' => 'nullable|string',
        'intervalo_minutos' => 'required|integer|min:1|max:1440',
        'timeout_segundos' => 'required|integer|min:5|max:300',
        'estado' => 'required|boolean',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre del sistema es obligatorio',
        'nombre.max' => 'El nombre no puede superar los 255 caracteres',
        'url_base.required' => 'La URL base es obligatoria',
        'url_base.url' => 'La URL debe ser válida (ejemplo: https://ejemplo.com)',
        'url_base.max' => 'La URL no puede superar los 500 caracteres',
        'intervalo_minutos.required' => 'El intervalo es obligatorio',
        'intervalo_minutos.min' => 'El intervalo mínimo es 1 minuto',
        'intervalo_minutos.max' => 'El intervalo máximo es 1440 minutos (24 horas)',
        'timeout_segundos.required' => 'El timeout es obligatorio',
        'timeout_segundos.min' => 'El timeout mínimo es 5 segundos',
        'timeout_segundos.max' => 'El timeout máximo es 300 segundos (5 minutos)',
        'estado.required' => 'El estado es obligatorio',
    ];

    public function render()
    {
        $sistemas = Sistema::query()
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('url_base', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.sistemas', [
            'sistemas' => $sistemas
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
            Sistema::create([
                'nombre' => $this->nombre,
                'url_base' => $this->url_base,
                'headers' => $this->headers,
                'intervalo_minutos' => $this->intervalo_minutos,
                'timeout_segundos' => $this->timeout_segundos,
                'estado' => $this->estado,
                'usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
                'estacion_creacion' => gethostname(),
            ]);

            session()->flash('message', 'Sistema registrado exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el sistema: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $sistema = Sistema::findOrFail($id);

            $this->sistemaId = $sistema->id;
            $this->nombre = $sistema->nombre;
            $this->url_base = $sistema->url_base;
            $this->headers = $sistema->headers;
            $this->intervalo_minutos = $sistema->intervalo_minutos;
            $this->timeout_segundos = $sistema->timeout_segundos;
            $this->estado = $sistema->estado;

            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el sistema');
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
            $sistema = Sistema::findOrFail($this->sistemaId);

            $sistema->update([
                'nombre' => $this->nombre,
                'url_base' => $this->url_base,
                'headers' => $this->headers,
                'intervalo_minutos' => $this->intervalo_minutos,
                'timeout_segundos' => $this->timeout_segundos,
                'estado' => $this->estado,
                'usuario_actualizacion' => Auth::id(),
                'fecha_actualizacion' => now(),
            ]);

            session()->flash('message', 'Sistema actualizado exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el sistema: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->sistemaId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->sistemaId = null;
    }

    public function deleteSistema()
    {
        try {
            $sistema = Sistema::findOrFail($this->sistemaId);
            $sistema->delete();

            session()->flash('message', 'Sistema eliminado exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el sistema: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }

    private function resetForm()
    {
        $this->reset([
            'nombre',
            'url_base',
            'headers',
            'intervalo_minutos',
            'timeout_segundos',
            'estado',
            'sistemaId'
        ]);
        $this->intervalo_minutos = 5;
        $this->timeout_segundos = 30;
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
