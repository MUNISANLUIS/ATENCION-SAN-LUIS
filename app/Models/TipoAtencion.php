<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAtencion extends Model
{
    use HasFactory;

    protected $table = 'atencion.tipo_atencion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'inicial',
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

    // Esta relación no existe directamente, así que la comentamos o eliminamos
    // public function tickets()
    // {
    //     return $this->hasMany(Ticket::class, 'id_tipo_atencion', 'id');
    // }

    // Esta relación tampoco existe directamente
    // public function empleados()
    // {
    //     return $this->hasMany(Empleado::class, 'id_tipo_atencion', 'id');
    // }
}