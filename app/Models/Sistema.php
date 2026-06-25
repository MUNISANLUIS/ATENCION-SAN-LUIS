<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
    protected $table = 'sistemas';
    protected $primaryKey = 'id';

    public $timestamps = false; // No usa created_at / updated_at automáticos

    protected $fillable = [
        'nombre',
        'url_base',
        'headers',
        'intervalo_minutos',
        'timeout_segundos',
        'estado',
        'ultimo_chequeo',
        'ultimo_estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
        'estacion_creacion',
        'latencia_ms',
        'codigo_http'
    ];

    protected $casts = [
        'headers' => 'array', // Si guardas JSON en NVARCHAR(MAX)
        'ultimo_chequeo' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];
}