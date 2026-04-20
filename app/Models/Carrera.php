<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrera extends Model
{
    use HasFactory;

    protected $table = 'carrera';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function becarios(): HasMany
    {
        return $this->hasMany(Becario::class);
    }
}