<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaEquipo extends Model
{
    use HasFactory;

    protected $table = 'reserva_equipo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_equipo',
        'id_usuario',
        'fecha_inicio',
        'fecha_fin',
        'motivo',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_aprobacion',
        'fecha_aprobacion',
        'id_usuario_solicitante',
        'equipos_ids',
        'requiere_personal',
        'ubicacion'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'id_equipo' => 'integer',
        'id_usuario' => 'integer',
        'id_usuario_solicitante' => 'integer',
        'requiere_personal' => 'boolean'
    ];

    // Relación: una reserva pertenece a un equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'id_equipo', 'id');
    }

    // Relación: una reserva pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    // Relación: una reserva pertenece a un empleado solicitante
    public function solicitante()
    {
        return $this->belongsTo(Empleado::class, 'id_usuario_solicitante', 'id');
    }

    // Scopes
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'APROBADA');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'PENDIENTE');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'RECHAZADA');
    }

    public function scopePorEquipo($query, $idEquipo)
    {
        return $query->where('id_equipo', $idEquipo);
    }

    public function scopePorUsuario($query, $idUsuario)
    {
        return $query->where('id_usuario', $idUsuario);
    }

    public function scopeActivas($query)
    {
        return $query->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now());
    }

    public function scopeFuturas($query)
    {
        return $query->where('fecha_inicio', '>', now());
    }

    // Accessors
    public function getEstadoTextoAttribute()
    {
        $estados = [
            'PENDIENTE' => 'Pendiente',
            'APROBADA' => 'Aprobada',
            'RECHAZADA' => 'Rechazada',
            'COMPLETADA' => 'Completada',
            'CANCELADA' => 'Cancelada'
        ];

        return $estados[$this->estado] ?? $this->estado;
    }

    public function getEstadoColorAttribute()
    {
        $colores = [
            'PENDIENTE' => 'yellow',
            'APROBADA' => 'green',
            'RECHAZADA' => 'red',
            'COMPLETADA' => 'blue',
            'CANCELADA' => 'gray'
        ];

        return $colores[$this->estado] ?? 'gray';
    }

    // Métodos útiles
    public function estaActiva()
    {
        return $this->estado === 'APROBADA' 
               && $this->fecha_inicio <= now() 
               && $this->fecha_fin >= now();
    }

    public function esFutura()
    {
        return $this->fecha_inicio > now();
    }

    public function esPasada()
    {
        return $this->fecha_fin < now();
    }

    public function tieneConflicloCon($fechaInicio, $fechaFin)
    {
        return $this->estado === 'APROBADA' && (
            ($this->fecha_inicio <= $fechaInicio && $this->fecha_fin >= $fechaInicio) ||
            ($this->fecha_inicio <= $fechaFin && $this->fecha_fin >= $fechaFin) ||
            ($this->fecha_inicio >= $fechaInicio && $this->fecha_fin <= $fechaFin)
        );
    }

    public function puedeAprobarse()
    {
        return $this->estado === 'PENDIENTE';
    }

    public function puedeRechazarse()
    {
        return $this->estado === 'PENDIENTE';
    }

    public function puedeCancelarse()
    {
        return in_array($this->estado, ['PENDIENTE', 'APROBADA']) && $this->esFutura();
    }
}
