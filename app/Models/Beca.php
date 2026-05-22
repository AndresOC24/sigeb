<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beca extends Model
{
    use HasFactory;

    protected $table = 'becas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'horas_requeridas',
        'porcentaje_beca',
        'tipo_beca',
    ];

    protected $casts = [
        'horas_requeridas' => 'integer',
        'porcentaje_beca' => 'integer',
    ];
}