<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function listarTickets()
    {
        return view('pages.admin.ticket');
    }

    public function crearticket()
    {
        return view('pages.public.crearticket');
    }

    public function seguimientoticket()
    {
        return view('pages.public.seguimientoticket');
    }
}
