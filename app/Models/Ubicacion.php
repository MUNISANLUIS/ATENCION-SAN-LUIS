<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'atencion.ubicacion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'id_usuario_creacion',
        'fecha_creacion',
        'estacion_creacion',
        'id_usuario_modificacion',
        'fecha_modificacion',
        'estacion_modificacion',
    ];

    // 🔁 Relación: una ubicación tiene muchos registros en antencion_ventanilla_ubicacion_tatencion_tcliente
    public function atencionVentanilla()
    {
        return $this->hasMany(AntencionVentanillaUbicacionTatencionTcliente::class, 'id_ubicacion', 'id');
    }

    // 🔁 Relación: una ubicación tiene muchos registros en tcliente_tatencion_tubicacion
    public function clienteAtencionUbicacion()
    {
        return $this->hasMany(TclienteTatencionTubicacion::class, 'id_tipo_ubicacion_ticket', 'id');
    }
}