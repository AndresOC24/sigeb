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

    // public function asignacionesBecas(): HasMany
    // {
    //     return $this->hasMany(AsignacionBeca::class);
    // }
}