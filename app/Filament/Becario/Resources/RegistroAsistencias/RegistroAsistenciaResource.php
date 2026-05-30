<?php

namespace App\Filament\Becario\Resources\RegistroAsistencias;

use App\Filament\Becario\Resources\RegistroAsistencias\Pages;
use App\Models\RegistroAsistencia;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\IconColumn;

class RegistroAsistenciaResource extends Resource
{
    protected static ?string $model = RegistroAsistencia::class;
    protected static \BackedEnum | string | null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Mi Historial';
    protected static ?string $modelLabel = 'Registro de Asistencia';
    protected static ?string $pluralModelLabel = 'Registros de Asistencia';

    public static function getEloquentQuery(): Builder
    {
        $becario = auth()->user()->becario;
        $asignacionIds = $becario
            ? $becario->asignaciones()->pluck('id')
            : collect();

        return parent::getEloquentQuery()
            ->whereIn('asignacion_beca_id', $asignacionIds);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('hora_entrada')
                    ->time('H:i')
                    ->label('Entrada'),
                TextColumn::make('hora_salida')
                    ->time('H:i')
                    ->label('Salida')
                    ->placeholder('—'),
                TextColumn::make('total_horas')
                    ->label('Horas')
                    ->numeric(2)
                    ->placeholder('—'),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'aprobado'  => 'success',
                        'rechazado' => 'danger',
                        default     => 'gray',
                    }),
                IconColumn::make('verificado_facial')
                    ->label('Facial')
                    ->boolean(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado'  => 'Aprobado',
                        'rechazado' => 'Rechazado',
                    ]),
                Filter::make('fecha')
                    ->schema([
                        DatePicker::make('desde'),
                        DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn($q, $d) => $q->whereDate('fecha', '>=', $d))
                            ->when($data['hasta'] ?? null, fn($q, $d) => $q->whereDate('fecha', '<=', $d));
                    }),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistroAsistencias::route('/'),
            'view'  => Pages\ViewRegistroAsistencia::route('/{record}'),
        ];
    }
}
