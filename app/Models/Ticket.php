<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'ticket';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'correlativo',
        'id_area',
        'nombres',
        'id_tipo_incidencia',
        'id_sub_incidencia',
        'descripcion',
        'estado',
        'id_usuario',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
        'estacion_creacion',
        'respuesta',
        'tiempo_respuesta'
    ];

    // 🔁 Relación: un ticket pertenece a un área
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area', 'id');
    }

    // 🔁 Relación: un ticket pertenece a una categoría de incidencia
    public function categoriaIncidencia()
    {
        return $this->belongsTo(CategoriaIncidencia::class, 'id_tipo_incidencia', 'id');
    }

    // 🔁 Relación: un ticket pertenece a una subcategoría de incidencia
    public function subCategoriaIncidencia()
    {
        return $this->belongsTo(SubCategoriaIncidencia::class, 'id_sub_incidencia', 'id');
    }

    // 🔁 Relación: un ticket pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
    public function ticketsActivos()
    {
        return $this->hasMany(Ticket::class, 'id_usuario', 'id')
                    ->whereIn('estado', [2, 3, 4, 6]);
    }

    // 🔁 Relación: un ticket pertenece a un tipo de estado
    public function tipoEstado()
    {
        return $this->belongsTo(TipoEstado::class, 'estado', 'id');
    }

    // 🔁 Relación: un ticket tiene muchos anexos
    public function anexos()
    {
        return $this->hasMany(Anexos::class, 'id_ticket', 'id');
    }
}
