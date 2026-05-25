<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionBeca extends Model
{
    protected $table = 'asignaciones_becas';

    protected $fillable = [
        'becario_id', 'beca_id', 'gestion_id', 'area_id',
        'jefe_area_id', 'materia_id', 'porcentaje_obtenido',
        'horas_acumuladas', 'estado',
    ];

    public function becario(): BelongsTo { return $this->belongsTo(Becario::class); }
    public function beca(): BelongsTo { return $this->belongsTo(Beca::class); }
    public function gestion(): BelongsTo { return $this->belongsTo(Gestion::class); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
    public function jefeArea(): BelongsTo { return $this->belongsTo(JefeDeArea::class, 'jefe_area_id'); }
    public function materia(): BelongsTo { return $this->belongsTo(Materia::class); }
}