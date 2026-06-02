<?php

namespace App\Filament\Widgets;

use App\Models\Becario;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class JefeMisBecarios extends TableWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = ['md' => 6];

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Encargados') ?? false;
    }

    public function table(Table $table): Table
    {
        $jefe = Auth::user()->jefeDeArea;
        $jefeId = $jefe?->id ?? 0;

        return $table
            ->heading('Mis becarios')
            ->query(
                Becario::query()
                    ->whereHas('asignaciones', fn ($q) => $q
                        ->where('jefe_area_id', $jefeId)
                        ->where('estado', 'activa'))
                    ->select('becarios.*')
                    ->selectSub(function ($q) use ($jefeId) {
                        $q->from('registro_asistencia as ra')
                          ->join('asignaciones_becas as ab', 'ab.id', '=', 'ra.asignacion_beca_id')
                          ->whereColumn('ab.becario_id', 'becarios.id')
                          ->where('ab.jefe_area_id', $jefeId)
                          ->where('ra.estado', 'aprobado')
                          ->selectRaw('COALESCE(SUM(ra.total_horas), 0)');
                    }, 'horas_cumplidas')
                    ->selectSub(function ($q) use ($jefeId) {
                        $q->from('asignaciones_becas as ab')
                          ->join('becas as b', 'b.id', '=', 'ab.beca_id')
                          ->whereColumn('ab.becario_id', 'becarios.id')
                          ->where('ab.jefe_area_id', $jefeId)
                          ->where('ab.estado', 'activa')
                          ->selectRaw('COALESCE(b.horas_requeridas, 0)')
                          ->limit(1);
                    }, 'meta')
            )
            ->columns([
                TextColumn::make('user.name')->label('Nombre'),
                TextColumn::make('horas_cumplidas')->label('Horas')->numeric(2),
                TextColumn::make('meta')->label('Meta'),
                TextColumn::make('progreso')
                    ->label('Progreso')
                    ->state(function ($record) {
                        if (! $record->meta || $record->meta == 0) return '—';
                        $pct = ($record->horas_cumplidas / $record->meta) * 100;
                        return number_format($pct, 0) . '%';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if (! $record->meta || $record->meta == 0) return 'gray';
                        $pct = ($record->horas_cumplidas / $record->meta) * 100;
                        if ($pct >= 100) return 'success';
                        if ($pct >= 80) return 'warning';
                        return 'gray';
                    }),
            ])
            ->paginated(false);
    }
}