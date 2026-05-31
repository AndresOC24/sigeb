<?php

namespace App\Filament\Resources\AsignacionBecas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AsignacionBecasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('becario.user.name')
                    ->label('Nombre del Becario')
                    ->searchable(),
                TextColumn::make('beca.nombre')
                    ->label('Tipo de Beca')
                    ->searchable(),
                TextColumn::make('gestion.nombre')
                    ->label('Gestión')
                    ->searchable(),
                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable(),
                TextColumn::make('jefeArea.nombre')
                    ->label('Encargado')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('porcentaje_obtenido')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('horas_acumuladas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estado')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
