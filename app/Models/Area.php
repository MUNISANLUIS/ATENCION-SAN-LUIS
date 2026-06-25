<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'abreviatura',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
    ];

    // 🔁 Relación: un área tiene muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_area', 'id');
    }

    // 🔁 Relación: un área tiene muchos empleados
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'id_area', 'id');
    }
}
