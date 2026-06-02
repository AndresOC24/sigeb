<?php

namespace App\Console\Commands;

use App\Models\AsignacionBeca;
use App\Models\RegistroAsistencia;
use Illuminate\Console\Command;

class SincronizarHorasAcumuladas extends Command
{
    protected $signature = 'sync:horas-acumuladas';
    protected $description = 'Recalcula horas_acumuladas de cada asignación desde sus registros aprobados';

    public function handle(): int
    {
        $asignaciones = AsignacionBeca::all();
        $bar = $this->output->createProgressBar($asignaciones->count());

        foreach ($asignaciones as $a) {
            $total = RegistroAsistencia::where('asignacion_beca_id', $a->id)
                ->where('estado', 'aprobado')
                ->whereNotNull('hora_salida')
                ->sum('total_horas');

            $a->update(['horas_acumuladas' => round($total)]);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nListo. {$asignaciones->count()} asignaciones recalculadas.");
        return 0;
    }
}