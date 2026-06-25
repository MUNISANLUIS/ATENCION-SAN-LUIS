<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Login extends Component
{
    public $username = '';
    public $password = '';

    public function login()
    {

        $this->validate([
            'username' => 'required',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {

            session()->flash('success', 'Inicio de sesión correcto.');
            return redirect()->to('/listar-tickets'); // Redirige a la vista deseada
        } else {
            session()->flash('error', 'Credenciales incorrectas.');
        }
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}
