<?php

namespace App\Filament\Widgets;

use App\Models\AsignacionBeca;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class BecariosPorArea extends ChartWidget
{
    protected ?string $heading = 'Becarios por Área';
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = ['md' => 6];

    protected ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['Super Administrador', 'Encargado General']) ?? false;
    }

    protected function getData(): array
    {
        $datos = AsignacionBeca::query()
            ->where('estado', 'activa')
            ->selectRaw('area_id, COUNT(DISTINCT becario_id) as total')
            ->groupBy('area_id')
            ->with('area:id,nombre')
            ->get();

        // Paleta de colores; cicla si hay más áreas que colores
        $paleta = [
            '#3b82f6', // azul
            '#10b981', // verde
            '#f59e0b', // ámbar
            '#ef4444', // rojo
            '#8b5cf6', // púrpura
            '#ec4899', // rosa
            '#14b8a6', // teal
            '#f97316', // naranja
            '#06b6d4', // cyan
            '#84cc16', // lima
        ];

        $colores = $datos->map(fn($d, $i) => $paleta[$i % count($paleta)])->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Becarios',
                    'data'  => $datos->pluck('total')->toArray(),
                    'backgroundColor' => $colores,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $datos->map(fn($d) => $d->area?->nombre ?? 'Sin área')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
