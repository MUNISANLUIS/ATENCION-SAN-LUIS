<?php

namespace App\Livewire\Admin;

use App\Models\TipoIncidencia;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\TipoUsuarioRol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Usuario extends Component
{
    use WithPagination;

    // Propiedades de búsqueda y filtros
    public $search = '';
    public $filterEstado = '';
    public $perPage = 10;

    // Propiedades del formulario
    public $usuarioId;
    public $name;
    public $username;
    public $email;
    public $password;
    public $estado = '1';
    public $id_tipo_usuario_rol;
    public $id_tipo_incidente;

    // Propiedades de modales
    public $showModal = false;
    public $showDeleteModal = false;
    public $usuarioToDelete = null;

    // Query strings
    protected $queryString = [
        'search' => ['except' => ''],
        'filterEstado' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    // Reglas de validación
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'email|max:255|unique:users,email,' . $this->usuarioId,
            'estado' => 'required|in:0,1',
            'id_tipo_usuario_rol' => 'required|exists:tipo_usuario_rol,id',
            'id_tipo_incidente' => 'required|exists:tipo_incidencia,id',
        ];

        // Si es creación, la contraseña es obligatoria
        if (!$this->usuarioId) {
            $rules['password'] = 'required|string|min:6';
        } else {
            // Si es edición, la contraseña es opcional
            $rules['password'] = 'nullable|string|min:6';
        }

        return $rules;
    }

    // Mensajes de validación personalizados
    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.max' => 'El nombre no puede tener más de 255 caracteres.',
        'username.required' => 'El Username es obligatorio.',
        'username.max' => 'El Username no puede tener más de 255 caracteres.',
        'email' => 'El email es obligatorio.',
        'email.email' => 'Debe ingresar un email válido.',
        'email.unique' => 'Este email ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'estado.required' => 'El estado es obligatorio.',
        'id_tipo_usuario_rol.required' => 'Debe seleccionar un rol.',
        'id_tipo_usuario_rol.exists' => 'El rol seleccionado no es válido.',
        'id_tipo_incidente.required' => 'Debe seleccionar un Tipo Incidencia.',
        'id_tipo_incidente.exists' => 'El Tipo Incidencia seleccionado no es válido.',
    ];

    /**
     * Resetear paginación al buscar
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Resetear paginación al filtrar
     */
    public function updatingFilterEstado()
    {
        $this->resetPage();
    }

    /**
     * Resetear paginación al cambiar items por página
     */
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Abrir modal para crear usuario
     */
    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Abrir modal para editar usuario
     */
    public function edit($id)
    {
        $this->resetForm();

        $usuario = User::findOrFail($id);

        $this->usuarioId = $usuario->id;
        $this->name = $usuario->name;
        $this->username = $usuario->username;
        $this->email = $usuario->email;
        $this->estado = $usuario->estado;
        $this->id_tipo_usuario_rol = $usuario->id_tipo_usuario_rol;
        $this->id_tipo_incidente = $usuario->id_tipo_incidente;

        $this->showModal = true;
    }

    /**
     * Guardar o actualizar usuario
     */
    public function store()
    {
        $this->validate();

        try {
            $data = [
                'username' => $this->username,
                'name' => $this->name,
                'email' => $this->email,
                'estado' => $this->estado,
                'id_tipo_usuario_rol' => $this->id_tipo_usuario_rol,
                'id_tipo_incidente' => $this->id_tipo_incidente,
            ];

            if ($this->usuarioId) {
                // Actualizar
                $usuario = User::findOrFail($this->usuarioId);

                // Si se ingresó una nueva contraseña
                if ($this->password) {
                    $data['password'] = $this->password;
                }

                $data['usuario_actualizacion'] = Auth::id();
                $data['fecha_actualizacion'] = now();

                $usuario->update($data);

                session()->flash('message', '✓ Usuario actualizado exitosamente.');
            } else {
                // Crear
                $data['password'] = $this->password;
                $data['usuario_creacion'] = Auth::id();
                $data['fecha_creacion'] = now();

                User::create($data);

                session()->flash('message', '✓ Usuario creado exitosamente.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el usuario: ' . $e->getMessage());
        }
    }
    public function ticketsActivos()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'id_usuario', 'id')
                    ->whereIn('estado', [2, 3, 4, 6]);
    }
    /**
     * Cambiar estado del usuario (activar/desactivar)
     */
    public function toggleEstado($id)
    {
        try {
            $usuario = User::findOrFail($id);

            $nuevoEstado = $usuario->estado == '1' ? '0' : '1';

            $usuario->update([
                'estado' => $nuevoEstado,
                'usuario_actualizacion' => Auth::id(),
                'fecha_actualizacion' => now(),
            ]);

            $mensaje = $nuevoEstado == '1' ? 'activado' : 'desactivado';
            session()->flash('message', "✓ Usuario {$mensaje} exitosamente.");
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Confirmar eliminación
     */
    public function confirmDelete($id)
    {
        $this->usuarioToDelete = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Eliminar usuario
     */
    public function deleteUsuario()
    {
        if ($this->usuarioToDelete) {
            try {
                $usuario = User::findOrFail($this->usuarioToDelete);

                // Verificar que no sea el usuario actual
                if ($usuario->id == Auth::id()) {
                    session()->flash('error', 'No puedes eliminar tu propio usuario.');
                    $this->cancelDelete();
                    return;
                }

                $usuario->delete();

                session()->flash('message', '✓ Usuario eliminado exitosamente.');

                // Si estamos en una página vacía después de eliminar, volver a la anterior
                if ($this->usuarios->count() === 0 && $this->usuarios->currentPage() > 1) {
                    $this->setPage($this->usuarios->currentPage() - 1);
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Error al eliminar el usuario: ' . $e->getMessage());
            }

            $this->cancelDelete();
        }
    }

    /**
     * Cancelar eliminación
     */
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->usuarioToDelete = null;
    }

    /**
     * Cerrar modal de formulario
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Resetear formulario
     */
    private function resetForm()
    {
        $this->reset([
            'usuarioId',
            'username',
            'name',
            'email',
            'password',
            'estado',
            'id_tipo_usuario_rol',
        ]);
        $this->resetValidation();
        $this->estado = '1'; // Estado activo por defecto
    }

    /**
     * Obtener usuarios filtrados
     */
    private function getUsuarios()
    {
        return User::with(['tipoUsuarioRol', 'tipoIncidencia'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEstado !== '', function ($query) {
                $query->where('estado', $this->filterEstado);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    /**
     * Obtener roles activos
     */
    private function getRoles()
    {
        return TipoUsuarioRol::where('estado', '1')
            ->orderBy('nombre')
            ->get();
    }

        private function getTipoIncidencia()
    {
        return TipoIncidencia::where('estado', '1')
            ->orderBy('nombres')
            ->get();
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        $usuarios = $this->getUsuarios();
        $roles = $this->getRoles();
        $tipoIncidencias = $this->getTipoIncidencia();

        return view('livewire.admin.usuario', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'tipoIncidencias' => $tipoIncidencias,
        ]);
    }
}
