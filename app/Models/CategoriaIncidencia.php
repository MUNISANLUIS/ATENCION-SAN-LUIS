<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaIncidencia extends Model
{
    use HasFactory;

    protected $table = 'categoria_incidencia';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
        'orden'
    ];

    // 🔁 Relación: una categoría tiene muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_tipo_incidencia', 'id');
    }
}
