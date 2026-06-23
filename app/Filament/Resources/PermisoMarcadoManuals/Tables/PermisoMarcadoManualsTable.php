<?php

namespace App\Filament\Resources\PermisoMarcadoManuals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermisoMarcadoManualsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('jefeDeArea.id')
                    ->searchable(),
                TextColumn::make('becario.id')
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('fecha_fin')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('otorgado_por')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('revocado')
                    ->boolean(),
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
