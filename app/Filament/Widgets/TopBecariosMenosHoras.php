<?php

namespace App\Filament\Widgets;

use App\Models\Becario;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class TopBecariosMenosHoras extends TableWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = ['md' => 6];

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['Super Administrador', 'Encargado General']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top 5 becarios con menos horas')
            ->query(
                Becario::query()
                    ->select('becarios.*')
                    ->whereHas('asignaciones', fn ($q) => $q->where('estado', 'activa'))
                    ->selectSub(function ($q) {
                        $q->from('registro_asistencia as ra')
                          ->join('asignaciones_becas as ab', 'ab.id', '=', 'ra.asignacion_beca_id')
                          ->whereColumn('ab.becario_id', 'becarios.id')
                          ->where('ra.estado', 'aprobado')
                          ->selectRaw('COALESCE(SUM(ra.total_horas), 0)');
                    }, 'horas_total')
                    ->selectSub(function ($q) {
                        $q->from('asignaciones_becas as ab')
                          ->join('becas as b', 'b.id', '=', 'ab.beca_id')
                          ->whereColumn('ab.becario_id', 'becarios.id')
                          ->where('ab.estado', 'activa')
                          ->selectRaw('COALESCE(b.horas_requeridas, 0)')
                          ->limit(1);
                    }, 'meta')
                    ->orderBy('horas_total', 'asc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('user.name')->label('Becario'),
                TextColumn::make('horas_total')->label('Horas')->numeric(2),
                TextColumn::make('meta')->label('Meta'),
                TextColumn::make('progreso')
                    ->label('Progreso')
                    ->state(function ($record) {
                        if (! $record->meta || $record->meta == 0) return '—';
                        $pct = ($record->horas_total / $record->meta) * 100;
                        return number_format($pct, 0) . '%';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if (! $record->meta || $record->meta == 0) return 'gray';
                        $pct = ($record->horas_total / $record->meta) * 100;
                        if ($pct >= 100) return 'success';
                        if ($pct >= 80) return 'warning';
                        if ($pct >= 50) return 'gray';
                        return 'danger';
                    }),
            ])
            ->paginated(false);
    }
}