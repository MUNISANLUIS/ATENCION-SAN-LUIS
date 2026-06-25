<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAuditoria extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'ticket_auditoria';

    // Clave primaria
    protected $primaryKey = 'id';

    // Si no usas los timestamps automáticos de Laravel
    public $timestamps = false;

    // Campos asignables
    protected $fillable = [
        'id_ticket',
        'correlativo',
        'id_area',
        'area_nombre',
        'nombres',
        'id_tipo_incidencia',
        'tipo_incidencia_nombre',
        'id_sub_incidencia',
        'sub_incidencia_nombre',
        'descripcion',
        'estado',
        'estado_nombre',
        'id_usuario',
        'usuario_nombre',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
        'estacion_creacion',
        'respuesta',
        'accion',
        'fecha_auditoria',
        'id_usuario_auditoria',
    ];

    /**
     * 🔗 Relación con el ticket original.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id_ticket', 'id');
    }

    /**
     * 🔗 Relación con el usuario que realizó la auditoría.
     */
    public function usuarioAuditoria()
    {
        return $this->belongsTo(User::class, 'id_usuario_auditoria', 'id');
    }
}
