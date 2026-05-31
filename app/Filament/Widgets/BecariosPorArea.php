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

        return [
            'datasets' => [
                [
                    'label' => 'Becarios',
                    'data'  => $datos->pluck('total')->toArray(),
                ],
            ],
            'labels' => $datos->map(fn ($d) => $d->area?->nombre ?? 'Sin área')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}