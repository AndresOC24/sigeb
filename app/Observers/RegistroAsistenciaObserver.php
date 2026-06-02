<?php

namespace App\Observers;

use App\Models\AsignacionBeca;
use App\Models\RegistroAsistencia;

class RegistroAsistenciaObserver
{
    public function updated(RegistroAsistencia $registro): void
    {
        if (! $registro->wasChanged('estado')) {
            return;
        }

        $this->recalcular($registro->asignacion_beca_id);
    }

    public function deleted(RegistroAsistencia $registro): void
    {
        $this->recalcular($registro->asignacion_beca_id);
    }

    private function recalcular(int $asignacionId): void
    {
        $total = RegistroAsistencia::where('asignacion_beca_id', $asignacionId)
            ->where('estado', 'aprobado')
            ->whereNotNull('hora_salida')
            ->sum('total_horas');

        AsignacionBeca::where('id', $asignacionId)
            ->update(['horas_acumuladas' => round($total)]);
    }
}