<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    protected $table = 'gestiones';

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];
}
