<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\SubCategoriaIncidencia;
use App\Models\TipoIncidencia;
use App\Models\CategoriaIncidencia;
use Illuminate\Support\Facades\Auth;

class Subcategorias extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Propiedades del formulario
    public $nombre = '';
    public $id_categoria_padre = '';
    public $estado = 1;
    public $orden;
    public $formato_path;
    public $archivoWord;
    public $tipo_incidencia;
    
    // Propiedades de control
    public $subcategoriaId;
    public $search = '';
    public $filterEstado = '';
    public $filterCategoria = '';
    public $perPage = 10;
    
    // Modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    protected $paginationTheme = 'tailwind';
    
    protected $rules = [
        'nombre' => 'required|string|max:100',
        'id_categoria_padre' => 'required|exists:categoria_incidencia,id',
        'estado' => 'required|boolean',
        'orden' => 'nullable|integer|min:0', 
        'archivoWord' => 'nullable|file|mimes:doc,docx|max:10240', 
        'tipo_incidencia' => 'required|exists:tipo_incidencia,id',
    ];
    
    protected $messages = [
        'nombre.required' => 'El nombre de la subcategoría es obligatorio',
        'nombre.max' => 'El nombre no puede superar los 100 caracteres',
        'id_categoria_padre.required' => 'La categoría padre es obligatoria',
        'id_categoria_padre.exists' => 'La categoría seleccionada no existe',
        'estado.required' => 'El estado es obligatorio',
        'archivoWord.mimes' => 'El archivo debe ser un documento Word (.doc o .docx)',
        'archivoWord.max' => 'El archivo no puede superar los 10MB',
        'tipo_incidencia.required' => 'El tipo de incidencia es obligatorio',
        'tipo_incidencia.exists' => 'El tipo de incidencia seleccionado no existe',
    ];
    
    public function render()
    {
        $subcategorias = SubCategoriaIncidencia::query()
            ->with(['categoriaPadre', 'tipoIncidencia'])
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->when($this->filterCategoria !== '', function ($query) {
                $query->where('id_categoria_padre', $this->filterCategoria);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
        
        // Obtener categorías activas para los selects
        $categorias = CategoriaIncidencia::where('estado', 1)
            ->orderBy('orden')
            ->get();
        
        $tiposIncidencia = TipoIncidencia::where('estado', 1)
            ->orderBy('id')
            ->get();
            
        return view('livewire.admin.subcategorias', [
            'subcategorias' => $subcategorias,
            'categorias' => $categorias,
            'tiposIncidencia' => $tiposIncidencia
        ]);
    }
    
    public function clearFilters()
    {
        $this->reset(['search', 'filterEstado', 'filterCategoria']);
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
            $formatoPath = null;
            
            // Procesar archivo si existe
            if ($this->archivoWord) {
                $filename = time() . '_' . $this->archivoWord->getClientOriginalName();
                $this->archivoWord->storeAs('formatos_subcategorias', $filename, 'public');
                $formatoPath = 'formatos_subcategorias/' . $filename;
            }
            
            SubCategoriaIncidencia::create([
                'nombre' => $this->nombre,
                'id_categoria_padre' => $this->id_categoria_padre,
                'tipo_incidencia' => $this->tipo_incidencia,
                'estado' => $this->estado,
                'orden' => $this->orden ?? 0, 
                'formato_path' => $formatoPath, 
                'usuario_creacion' => Auth::id(),
                'fecha_creacion' => now(),
            ]);
            
            session()->flash('message', 'Subcategoría creada exitosamente');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la subcategoría: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        try {
            $subcategoria = SubCategoriaIncidencia::findOrFail($id);
            
            $this->subcategoriaId = $subcategoria->id;
            $this->nombre = $subcategoria->nombre;
            $this->id_categoria_padre = $subcategoria->id_categoria_padre;
            $this->estado = $subcategoria->estado;
            $this->orden = $subcategoria->orden; 
            $this->formato_path = $subcategoria->formato_path; 
            $this->tipo_incidencia = $subcategoria->tipo_incidencia;
            
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar la subcategoría');
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
            $subcategoria = SubCategoriaIncidencia::findOrFail($this->subcategoriaId);
            
            $formatoPath = $subcategoria->formato_path; // Mantener el existente
            
            // Si se sube un nuevo archivo
            if ($this->archivoWord) {
                // Eliminar archivo anterior si existe
                if ($subcategoria->formato_path && \Storage::disk('public')->exists($subcategoria->formato_path)) {
                    \Storage::disk('public')->delete($subcategoria->formato_path);
                }
                
                $filename = time() . '_' . $this->archivoWord->getClientOriginalName();
                $this->archivoWord->storeAs('formatos_subcategorias', $filename, 'public');
                $formatoPath = 'formatos_subcategorias/' . $filename;
            }
            
            $subcategoria->update([
                'nombre' => $this->nombre,
                'id_categoria_padre' => $this->id_categoria_padre,
                'tipo_incidencia' => $this->tipo_incidencia,
                'estado' => $this->estado,
                'orden' => $this->orden ?? 0, 
                'formato_path' => $formatoPath,
                'usuario_actualizacion' => Auth::id(),
                'fecha_actualizacion' => now(),
            ]);
            
            session()->flash('message', 'Subcategoría actualizada exitosamente');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la subcategoría: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->subcategoriaId = $id;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->subcategoriaId = null;
    }
    
    public function deleteSubcategoria()
    {
        try {
            $subcategoria = SubCategoriaIncidencia::findOrFail($this->subcategoriaId);
            
            // Verificar si tiene tickets asociados
            if ($subcategoria->tickets()->count() > 0) {
                session()->flash('error', 'No se puede eliminar la subcategoría porque tiene tickets asociados');
                $this->cancelDelete();
                return;
            }
            
            $subcategoria->delete();
            
            session()->flash('message', 'Subcategoría eliminada exitosamente');
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la subcategoría: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }

    
    public function eliminarArchivo()
    {
        try {
            $subcategoria = SubCategoriaIncidencia::findOrFail($this->subcategoriaId);
            
            if ($subcategoria->formato_path && \Storage::disk('public')->exists($subcategoria->formato_path)) {
                \Storage::disk('public')->delete($subcategoria->formato_path);
                
                $subcategoria->update([
                    'formato_path' => null,
                    'usuario_actualizacion' => Auth::id(),
                    'fecha_actualizacion' => now(),
                ]);
                
                $this->formato_path = null;
                session()->flash('message', 'Archivo eliminado exitosamente');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el archivo: ' . $e->getMessage());
        }
    }
    
    private function resetForm()
    {
        $this->reset(['nombre', 'id_categoria_padre', 'tipo_incidencia', 'estado', 'orden', 'formato_path', 'archivoWord', 'subcategoriaId']);
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
    
    public function updatingFilterCategoria()
    {
        $this->resetPage();
    }
}