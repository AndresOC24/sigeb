<?php

namespace App\Filament\Widgets;

use App\Models\Becario;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TopBecariosHorasMes extends TableWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = ['md' => 1];

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['Super Administrador', 'Encargado General']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top 5 becarios por horas aprobadas (este mes)')
            ->query(
                Becario::query()
                    ->select('becarios.*')
                    ->selectSub(function ($q) {
                        $q->from('registro_asistencia as ra')
                          ->join('asignaciones_becas as ab', 'ab.id', '=', 'ra.asignacion_beca_id')
                          ->whereColumn('ab.becario_id', 'becarios.id')
                          ->where('ra.estado', 'aprobado')
                          ->whereMonth('ra.fecha', now()->month)
                          ->whereYear('ra.fecha', now()->year)
                          ->selectRaw('COALESCE(SUM(ra.total_horas), 0)');
                    }, 'horas_mes')
                    ->orderByDesc('horas_mes')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('user.name')->label('Becario'),
                TextColumn::make('horas_mes')->label('Horas')->numeric(2),
            ])
            ->paginated(false);
    }
}