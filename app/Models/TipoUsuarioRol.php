<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUsuarioRol extends Model
{
    use HasFactory;

    protected $table = 'tipo_usuario_rol';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
    ];

    // 🔁 Relación: un rol puede estar asociado a muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_tipo_usuario_rol', 'id');
    }
}
