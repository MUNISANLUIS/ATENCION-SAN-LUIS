<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Barryvdh\Debugbar\Facades\Debugbar as FacadesDebugbar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Register extends Component
{
    public $email;
    public $password;

    public function register()
    {
        FacadesDebugbar::info('✅ Entró al método register');
        Log::info('=== REGISTRO PRUEBA ===');
        $this->validate([
            'email' => 'required',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $this->email, // puedes usar otro campo para name si lo prefieres
            'email' => $this->email,
            'password' => $this->password,
            'estado' => 1,
            'id_tipo_usuario_rol' => 1, // reemplázalo si tienes una relación
            'usuario_creacion' => 'sistema',
            'fecha_creacion' => now(),
        ]);

        session()->flash('success', 'Usuario registrado correctamente.');
        $this->reset(['email', 'password']);
    }

    public function render()
    {
        Log::info('=== FORM ===');
        FacadesDebugbar::info('formulario');
        return view('livewire.admin.register');
    }
}
