<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermisoMarcadoManual extends Model
{
    protected $table = 'permisos_marcado_manual';

    protected $fillable = [
        'jefe_de_area_id', 'becario_id', 'fecha_inicio', 'fecha_fin',
        'motivo', 'otorgado_por', 'revocado',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
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

    public function otorgadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'otorgado_por');
    }

    public function getEstadoAttribute(): string
    {
        if ($this->revocado) return 'revocado';
        if (now()->lt($this->fecha_inicio)) return 'pendiente';
        if (now()->gt($this->fecha_fin)) return 'vencido';
        return 'vigente';
    }

    public function scopeVigentes($query)
    {
        return $query->where('revocado', false)
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now());
    }
}