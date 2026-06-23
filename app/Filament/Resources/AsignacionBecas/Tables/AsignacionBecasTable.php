<?php

namespace App\Filament\Resources\AsignacionBecas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;


class AsignacionBecasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('becario.user.name')
                    ->label('Nombre del Becario')
                    ->searchable(),
                TextColumn::make('beca.nombre')
                    ->label('Tipo de Beca')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('gestion.nombre')
                    ->label('Gestión')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable(),
                TextColumn::make('jefeArea.user.name')
                    ->label('Encargado')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('porcentaje_obtenido')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('horas_acumuladas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estado')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Creado el')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualizado el')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                
                ViewAction::make(),
                EditAction::make()
                
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
