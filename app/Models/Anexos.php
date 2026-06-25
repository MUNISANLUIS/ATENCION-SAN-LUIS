<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexos extends Model
{
    use HasFactory;

    protected $table = 'anexos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_ticket',
        'ruta',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
    ];

    // 🔁 Relación: un anexo pertenece a un ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id_ticket', 'id');
    }

}
