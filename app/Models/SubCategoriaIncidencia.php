<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategoriaIncidencia extends Model
{
    use HasFactory;

    protected $table = 'sub_categoria_incidencia';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_categoria_padre',
        'nombre',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
        'tipo_incidencia',
        'formato_path',
        'orden'
    ];

    // 🔁 Relación: una subcategoría pertenece a una categoría
    public function categoriaPadre()
    {
        return $this->belongsTo(CategoriaIncidencia::class, 'id_categoria_padre', 'id');
    }

    // 🔁 Relación: una subcategoría puede tener muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_sub_incidencia', 'id');
    }

    // 🔁 Relación: una subcategoría pertenece a un tipo de incidencia
    public function tipoIncidencia()
    {
        return $this->belongsTo(TipoIncidencia::class, 'tipo_incidencia', 'id');
    }
}
