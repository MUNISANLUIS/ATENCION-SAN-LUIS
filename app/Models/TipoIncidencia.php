<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoIncidencia extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'tipo_incidencia';

    // Clave primaria
    protected $primaryKey = 'id';

    // Si la tabla no tiene las columnas created_at / updated_at de Laravel
    public $timestamps = false;

    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'nombres',
        'fecha_creacion',
        'usuario_creacion',
        'fecha_actualizacion',
        'usuario_actualizacion',
        'estado',
    ];
}
