<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $table = 'equipo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'codigo',
        'marca',
        'modelo',
        'estado',
        'id_area',
        'id_usuario',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion'
    ];

    // Relación: un equipo pertenece a un área
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area', 'id');
    }

    // Relación: un equipo pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    // Relación: un equipo tiene muchas reservas
    public function reservas()
    {
        return $this->hasMany(ReservaEquipo::class, 'id_equipo', 'id');
    }
}
