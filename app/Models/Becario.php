<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Becario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'carrera_id',
        'codigo_estudiante',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionBeca::class);
    }

    /**
     * Permiso de marcado manual vigente que exonera a este becario de la
     * verificación facial. Aplica si hay un permiso aprobado dirigido a él
     * directamente, o uno con becario_id NULL para el área de su asignación
     * activa (todos los becarios del área).
     */
    public function permisoMarcadoManualVigente(): ?PermisoMarcadoManual
    {
        $asignacion = $this->asignaciones()->where('estado', 'activa')->first();

        if (! $asignacion) {
            return null;
        }

        $areaId = $asignacion->area_id;

        return PermisoMarcadoManual::query()
            ->vigentes()
            ->where(function ($q) use ($areaId) {
                $q->where('becario_id', $this->id)
                    ->orWhere(function ($q2) use ($areaId) {
                        $q2->whereNull('becario_id')
                            ->whereHas('jefeDeArea', fn ($j) => $j->where('area_id', $areaId));
                    });
            })
            ->latest('fecha_fin')
            ->first();
    }
}
