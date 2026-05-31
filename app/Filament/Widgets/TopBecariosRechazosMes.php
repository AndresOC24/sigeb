<?php

namespace App\Filament\Widgets;

use App\Models\Becario;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class TopBecariosRechazosMes extends TableWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = ['md' => 5];

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['Super Administrador', 'Encargado General']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top 5 becarios con más rechazos (este mes)')
            ->query(
                Becario::query()
                    ->select('becarios.*')
                    ->selectSub(function ($q) {
                        $q->from('registro_asistencia as ra')
                          ->join('asignaciones_becas as ab', 'ab.id', '=', 'ra.asignacion_beca_id')
                          ->whereColumn('ab.becario_id', 'becarios.id')
                          ->where('ra.estado', 'rechazado')
                          ->whereMonth('ra.fecha', now()->month)
                          ->whereYear('ra.fecha', now()->year)
                          ->selectRaw('COUNT(*)');
                    }, 'rechazos_mes')
                    ->having('rechazos_mes', '>', 0)
                    ->orderByDesc('rechazos_mes')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('user.name')->label('Becario'),
                TextColumn::make('rechazos_mes')->label('Rechazos'),
            ])
            ->paginated(false);
    }
}