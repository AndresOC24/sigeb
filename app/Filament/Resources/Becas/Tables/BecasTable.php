<?php

namespace App\Filament\Resources\Becas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BecasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Beca')
                    ->searchable(),
                TextColumn::make('horas_requeridas')
                    ->label('Horas Requeridas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('porcentaje_beca')
                    ->label('Porcentaje de Beca')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tipo_beca')
                    ->label('Tipo de Beca')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
