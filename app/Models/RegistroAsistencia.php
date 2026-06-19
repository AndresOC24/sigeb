<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroAsistencia extends Model
{
    protected $table = 'registro_asistencia';
    protected $fillable = ['asignacion_beca_id', 'validado_por', 'fecha', 'hora_entrada', 'hora_salida', 'total_horas', 'actividad_principal', 'motivo_rechazo', 'verificado_facial', 'confidence_score', 'estado', 'evidencia', 'evidencia_nombre', 'evidencia_subida_en'];
    protected $casts = ['fecha' => 'date', 'hora_entrada' => 'datetime', 'hora_salida' => 'datetime', 'verificado_facial' => 'boolean', 'evidencia_subida_en' => 'datetime'];

    public function asignacionBeca()
    {
        return $this->belongsTo(AsignacionBeca::class, 'asignacion_beca_id');
    }
    public function validadoPor()
    {
        return $this->belongsTo(User::class, 'validado_por');
    }
    public function ayudantia()
    {
        return $this->hasOne(RegistroAsistenciaAyudantia::class, 'registro_asistencia_id');
    }
}
