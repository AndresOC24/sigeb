<?php

namespace App\Filament\Resources\RegistroAsistencias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RegistroAsistenciasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('asignacionBeca.becario.user.name')
                    ->label('Becario')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asignacionBeca.area.nombre')
                    ->label('Área')
                    ->toggleable(),
                TextColumn::make('fecha')->date('d/m/Y')->label('Fecha')->sortable(),
                TextColumn::make('hora_entrada')->dateTime('H:i')->label('Entrada'),
                TextColumn::make('hora_salida')->dateTime('H:i')->label('Salida')->placeholder('—'),
                TextColumn::make('total_horas')->label('Horas')->placeholder('—'),
                IconColumn::make('verificado_facial')
                    ->label('Verificación')
                    ->boolean()
                    ->trueIcon('heroicon-o-face-smile')
                    ->falseIcon('heroicon-o-pencil-square')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn($record) => $record->verificado_facial ? 'Facial' : 'Manual (sin verificación facial)'),
                TextColumn::make('estado')->badge()->label('Estado')
                    ->color(fn(?string $state) => match ($state) {
                        'aprobado' => 'success',
                        'pendiente' => 'warning',
                        'rechazado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                    ]),
                TernaryFilter::make('verificado_facial')
                    ->label('Verificación facial')
                    ->placeholder('Todas')
                    ->trueLabel('Solo facial')
                    ->falseLabel('Solo manual'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
