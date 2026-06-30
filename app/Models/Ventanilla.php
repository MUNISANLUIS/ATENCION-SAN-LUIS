<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventanilla extends Model
{
    use HasFactory;

    protected $table = 'atencion.ventanilla';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'numero',
        'descripcion',
        'id_tipo_ubicacion_ticket',
        'estado',
        'id_usuario_creacion',
        'fecha_creacion',
        'estacion_creacion',
        'id_usuario_modificacion',
        'fecha_modificacion',
        'estacion_modificacion',
    ];

    // 🔁 Relación: una ventanilla pertenece a un tipo de ubicación ticket
    public function tipoUbicacionTicket()
    {
        return $this->belongsTo(TipoUbicacionTicket::class, 'id_tipo_ubicacion_ticket', 'id');
    }

    // 🔁 Relación: una ventanilla tiene muchos registros en antencion_ventanilla_ubicacion_tatencion_tcliente
    public function atencionVentanilla()
    {
        return $this->hasMany(AntencionVentanillaUbicacionTatencionTcliente::class, 'id_ventanilla', 'id');
    }
}