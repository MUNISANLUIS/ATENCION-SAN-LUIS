<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleado';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombres',
        'id_area',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
    ];

    // 🔁 Relación: un empleado pertenece a un área
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area', 'id');
    }
}
