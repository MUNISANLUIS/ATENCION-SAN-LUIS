<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEstado extends Model
{
    use HasFactory;

    protected $table = 'tipo_estado';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'orden',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
    ];

    // 🔁 Relación: un tipo de estado puede estar asociado a muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'estado', 'id');
    }
}
