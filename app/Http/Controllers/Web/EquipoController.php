<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index()
    {
        return view('pages.admin.equipo');
    }

    public function crearEquipo()
    {
        return view('pages.admin.crear-equipo');
    }

    public function editarEquipo()
    {
        return view('pages.admin.editar-equipo');
    }

    public function reservarEquipo()
    {
        return view('pages.admin.reservar-equipo');
    }
}
