<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JefeDeArea extends Model
{
    use HasFactory;

    protected $table = 'jefe_de_area';

    protected $fillable = [
        'user_id',
        'area_id',
        'cargo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function asignacionesBecas(): HasMany
    {
        return $this->hasMany(AsignacionBeca::class);
    }

    public function permisosMarcadoManual()
    {
        return $this->hasMany(\App\Models\PermisoMarcadoManual::class);
    }

    public function tienePermisoMarcadoManualVigente(): bool
    {
        return $this->permisosMarcadoManual()->vigentes()->exists();
    }

    public function becariosPermitidosManual()
    {
        $permisos = $this->permisosMarcadoManual()->vigentes()->get();

        if ($permisos->isEmpty()) {
            return collect();
        }

        // Si algún permiso vigente tiene becario_id NULL → todos los becarios de su área (con asignación activa)
        if ($permisos->whereNull('becario_id')->isNotEmpty()) {
            return \App\Models\Becario::whereHas('asignaciones', function ($q) {
                $q->where('estado', 'activa')->where('area_id', $this->area_id);
            })->get();
        }

        // Lista específica
        return \App\Models\Becario::whereIn('id', $permisos->pluck('becario_id')->filter())->get();
    }
}
