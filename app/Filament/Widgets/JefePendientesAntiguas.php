<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use App\Models\AsignacionBeca;
use App\Models\RegistroAsistencia;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class JefePendientesAntiguas extends TableWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = ['md' => 6];

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Encargados') ?? false;
    }

    public function table(Table $table): Table
    {
        $jefe = Auth::user()->jefeDeArea;
        $ids = $jefe
            ? AsignacionBeca::where('jefe_area_id', $jefe->id)->pluck('id')
            : collect();

        return $table
            ->heading('Pendientes más antiguas')
            ->query(
                RegistroAsistencia::query()
                    ->whereIn('asignacion_beca_id', $ids)
                    ->where('estado', 'pendiente')
                    ->whereNotNull('hora_salida')
                    ->orderBy('fecha')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('asignacionBeca.becario.user.name')->label('Becario'),
                TextColumn::make('fecha')->date('d/m/Y'),
                TextColumn::make('total_horas')->label('Horas')->numeric(2),
            ])
            ->recordActions([
                Action::make('ver')
                    ->label('Validar')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (RegistroAsistencia $r) => RegistroAsistenciaResource::getUrl('view', ['record' => $r])),
            ])
            ->paginated(false);
    }
}