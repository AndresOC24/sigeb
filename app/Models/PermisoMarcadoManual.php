<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermisoMarcadoManual extends Model
{
    protected $table = 'permisos_marcado_manual';

    protected $fillable = [
        'jefe_de_area_id', 'becario_id', 'fecha_inicio', 'fecha_fin',
        'motivo', 'estado_solicitud', 'solicitado_por', 'motivo_rechazo',
        'revisado_en', 'otorgado_por', 'revocado',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'revisado_en' => 'datetime',
        'revocado' => 'boolean',
    ];

    public function jefeDeArea(): BelongsTo
    {
        return $this->belongsTo(JefeDeArea::class);
    }

    public function becario(): BelongsTo
    {
        return $this->belongsTo(Becario::class);
    }

    public function solicitadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    public function otorgadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'otorgado_por');
    }

    public function getEstadoAttribute(): string
    {
        if ($this->estado_solicitud === 'pendiente') return 'por_aprobar';
        if ($this->estado_solicitud === 'rechazada') return 'rechazada';
        if ($this->revocado) return 'revocado';
        if (now()->lt($this->fecha_inicio)) return 'pendiente';
        if (now()->gt($this->fecha_fin)) return 'vencido';
        return 'vigente';
    }

    /**
     * Solo los permisos aprobados, no revocados y dentro de su ventana habilitan
     * el marcado sin verificación facial.
     */
    public function scopeVigentes($query)
    {
        return $query->where('estado_solicitud', 'aprobada')
            ->where('revocado', false)
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now());
    }
}