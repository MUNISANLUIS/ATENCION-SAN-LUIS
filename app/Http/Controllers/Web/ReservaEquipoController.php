<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReservaEquipoController extends Controller
{
    public function index()
    {
        return view('pages.admin.reserva-equipo');
    }

    public function crearReserva()
    {
        return view('pages.public.reserva-simple');
    }

    public function editarReserva()
    {
        return view('pages.admin.editar-reserva');
    }

    public function aprobarReserva()
    {
        return view('pages.admin.aprobar-reserva');
    }
}
