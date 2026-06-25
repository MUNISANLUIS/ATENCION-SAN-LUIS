<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function login()
    {
        return view('pages.admin.login');
    }

        public function index()
    {
        return view('pages.admin.usuario');
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Cierra sesión del usuario actual

        $request->session()->invalidate(); // Invalida la sesión
        $request->session()->regenerateToken(); // Regenera el token CSRF

        return redirect('/login')->with('status', 'Sesión cerrada correctamente.');
    }
}
