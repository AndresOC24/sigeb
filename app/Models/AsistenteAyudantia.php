<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenteAyudantia extends Model
{
    protected $table = 'asistentes_ayudantia';
    protected $fillable = ['registro_asistencia_ayudantia_id', 'alumno_ayudantia_id'];

    public function alumno()
    {
        return $this->belongsTo(AlumnoAyudantia::class, 'alumno_ayudantia_id');
    }
}
