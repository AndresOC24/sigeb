<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoAyudantia extends Model
{
    protected $table = 'alumnos_ayudantia';
    protected $fillable = ['nombre', 'codigo_alumno', 'materia_id'];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }
}
