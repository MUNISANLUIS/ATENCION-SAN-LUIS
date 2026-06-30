<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoUbicacionTicketController extends Controller
{
    public function index()
    {
        return view('pages.admin.tipos-ubicacion-ticket');
    }
}