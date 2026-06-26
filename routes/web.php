<?php

use App\Http\Controllers\Web\RegisterController;
use App\Http\Controllers\Web\ReporteController;
use App\Http\Controllers\Web\RespuestaPersonalizadaController;
use App\Http\Controllers\Web\TicketController;
use App\Http\Controllers\Web\UsuarioController;
use App\Http\Controllers\Web\AreaController;
use App\Http\Controllers\Web\EmpleadosController;
use App\Http\Controllers\Web\EquipoController;
use App\Http\Controllers\Web\ReservaEquipoController;
use App\Http\Controllers\Web\SistemaController;
use App\Http\Controllers\Web\CategoriaController;
use App\Http\Controllers\Web\SubCategoriaController;
use App\Http\Controllers\Web\MonitoreoController;
use App\Http\Controllers\Web\TableroController;
use App\Http\Controllers\Web\TipoAtencionController; // 👈 NUEVO: Importar el controlador
use Illuminate\Support\Facades\Route;

Route::get('/login', [UsuarioController::class, 'login'])->name('login');
Route::get('/', [TicketController::class, 'crearTicket'])->name('public.ticket');
Route::get('/ticket-seguimiento', [TicketController::class, 'seguimientoticket'])->name('public.ticket-seguimiento');
Route::get('/reservarequipo', [ReservaEquipoController::class, 'crearReserva'])->name('public.reservarequipo');
Route::get('/reserva-simple', function() {
    return view('pages.public.reserva-simple');
});

Route::middleware('auth')->group(function () {
    Route::get('/listar-tickets', [TicketController::class, 'listarTickets'])->name('admin.ticket');
    Route::get('/tablero-control', [TableroController::class, 'index'])->name('admin.tablero');
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('admin.usuario');
    Route::get('/reporte', [ReporteController::class, 'index'])->name('admin.reporte');
    Route::get('/sistemas', [SistemaController::class, 'index'])->name('admin.sistemas');
    Route::get('/monitoreo', [MonitoreoController::class, 'index'])->name('admin.monitoreo');
    Route::get('/area', [AreaController::class, 'index'])->name('admin.area');
    Route::get('/empleado', [EmpleadosController::class, 'index'])->name('admin.empleados');
    Route::get('/equipo', [EquipoController::class, 'index'])->name('admin.equipo');
    Route::get('/categoria', [CategoriaController::class, 'index'])->name('admin.categoria');
    Route::get('/subcategoria', [SubCategoriaController::class, 'index'])->name('admin.subcategoria');
    Route::get('/tipoatencion', [TipoAtencionController::class, 'index'])->name('admin.tipoatencion'); // 👈 NUEVA RUTA
    
    Route::get('/reservas-horario', function() {
        return view('pages.admin.reservas-horario');
    })->name('admin.reservas-horario');
    Route::post('/logout', [UsuarioController::class, 'logout'])->name('logout');
});