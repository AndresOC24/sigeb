<?php

namespace App\Filament\Widgets;

use App\Models\AsignacionBeca;
use App\Models\RegistroAsistencia;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class JefePendientesStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Encargados') ?? false;
    }

    protected function getStats(): array
    {
        $jefe = Auth::user()->jefeDeArea;
        if (! $jefe) {
            return [Stat::make('Sin área asignada', '—')->color('gray')];
        }

        $asignacionIds = AsignacionBeca::where('jefe_area_id', $jefe->id)
            ->where('estado', 'activa')
            ->pluck('id');

        $pendientes = RegistroAsistencia::whereIn('asignacion_beca_id', $asignacionIds)
            ->where('estado', 'pendiente')
            ->whereNotNull('hora_salida')
            ->count();

        $abiertas = RegistroAsistencia::whereIn('asignacion_beca_id', $asignacionIds)
            ->whereNull('hora_salida')
            ->count();

        $becariosActivos = $asignacionIds->count();

        return [
            Stat::make('Jornadas pendientes', $pendientes)
                ->description('Esperando tu validación')
                ->color($pendientes > 0 ? 'warning' : 'success'),

            Stat::make('Mis becarios activos', $becariosActivos)
                ->description('Bajo tu supervisión')
                ->color('success'),

            Stat::make('Jornadas abiertas', $abiertas)
                ->description('Sin marcar salida')
                ->color($abiertas > 0 ? 'danger' : 'success'),
        ];
    }
}