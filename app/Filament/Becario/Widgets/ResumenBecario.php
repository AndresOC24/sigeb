<?php

namespace App\Filament\Becario\Widgets;

use App\Models\RegistroAsistencia;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResumenBecario extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $becario = auth()->user()->becario;
        $asignacion = $becario?->asignaciones()->where('estado', 'activa')->first();

        if (! $asignacion) {
            return [
                Stat::make('Asignación', 'Sin beca activa'),
            ];
        }

        $horasCumplidas = (float) RegistroAsistencia::where('asignacion_beca_id', $asignacion->id)
            ->where('estado', 'aprobado')
            ->whereNotNull('hora_salida')
            ->sum('total_horas');

        $horasMeta = (int) ($asignacion->beca->horas_requeridas ?? 0);
        $restantes = max($horasMeta - $horasCumplidas, 0);

        $ultimo = RegistroAsistencia::where('asignacion_beca_id', $asignacion->id)
            ->latest('hora_entrada')
            ->first();

        $ultimoTexto = $ultimo
            ? Carbon::parse($ultimo->hora_entrada)->format('d/m/Y H:i')
            : 'Sin registros';

        return [
            Stat::make('Horas cumplidas', number_format($horasCumplidas, 2) . ' h')
                ->description($horasMeta ? "Meta: {$horasMeta} h" : 'Sin meta definida')
                ->color('success'),

            Stat::make('Horas restantes', number_format($restantes, 2) . ' h')
                ->description($horasMeta && $horasCumplidas >= $horasMeta ? 'Meta cumplida' : 'Para alcanzar la meta')
                ->color($horasMeta && $horasCumplidas >= $horasMeta ? 'success' : 'warning'),

            Stat::make('Último registro', $ultimoTexto)
                ->description($ultimo?->estado ? ucfirst($ultimo->estado) : '')
                ->color('gray'),
        ];
    }
}