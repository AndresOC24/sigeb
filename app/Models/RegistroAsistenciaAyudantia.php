<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroAsistenciaAyudantia extends Model
{
    protected $table = 'registro_asistencia_ayudantia';
    protected $fillable = ['registro_asistencia_id', 'foto_clase'];

    public function registro()
    {
        return $this->belongsTo(RegistroAsistencia::class, 'registro_asistencia_id');
    }
    public function asistentes()
    {
        return $this->hasMany(AsistenteAyudantia::class, 'registro_asistencia_ayudantia_id');
    }
}
