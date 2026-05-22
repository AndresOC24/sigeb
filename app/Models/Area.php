<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function jefesDeArea(): HasMany
    {
        return $this->hasMany(JefeDeArea::class);
    }

    // public function asignacionesBecas(): HasMany
    // {
    //     return $this->hasMany(AsignacionBeca::class);
    // }
}