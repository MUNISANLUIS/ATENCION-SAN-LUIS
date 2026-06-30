<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCliente extends Model
{
    use HasFactory;

    protected $table = 'atencion.tipo_cliente';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'peso',
        'estado',
        'id_usuario_creacion',
        'fecha_creacion',
        'estacion_creacion',
        'id_usuario_modificacion',
        'fecha_modificacion',
        'estacion_modificacion',
    ];

    // 🔁 Relación: un tipo de cliente tiene muchos registros en tcliente_tatencion_tubicacion
    public function clienteAtencionUbicacion()
    {
        return $this->hasMany(TclienteTatencionTubicacion::class, 'id_tipo_cliente', 'id');
    }
}