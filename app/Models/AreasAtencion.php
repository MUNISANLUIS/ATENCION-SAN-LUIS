<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreasAtencion extends Model
{
    use HasFactory;

    protected $table = 'atencion.areas_atencion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'abreviatura',
        'estado',
        'id_usuario_creacion',
        'fecha_creacion',
        'estacion_creacion',
        'id_usuario_modificacion',
        'fecha_modificacion',
        'estacion_modificacion',
    ];

    // 🔁 Relación: un área de atención tiene muchos empleados
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'id_area', 'id');
    }

    // 🔁 Relación: un área de atención tiene muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_area', 'id');
    }
}