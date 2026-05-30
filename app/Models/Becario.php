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
}
