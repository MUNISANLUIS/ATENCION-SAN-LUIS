<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUbicacionTicket extends Model
{
    use HasFactory;

    protected $table = 'atencion.tipo_ubicacion_ticket';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ubicacion',
        'estado',
        'id_usuario_creacion',
        'fecha_creacion',
        'estacion_creacion',
        'id_usuario_modificacion',
        'fecha_modificacion',
        'estacion_modificacion',
    ];

    // 🔁 Relación: un tipo ubicación ticket tiene muchos registros en tcliente_tatencion_tubicacion
    public function clienteAtencionUbicacion()
    {
        return $this->hasMany(TclienteTatencionTubicacion::class, 'id_tipo_ubicacion_ticket', 'id');
    }

    // 🔁 Relación: un tipo ubicación ticket tiene muchos registros en ventanilla
    public function ventanillas()
    {
        return $this->hasMany(Ventanilla::class, 'id_tipo_ubicacion_ticket', 'id');
    }
}