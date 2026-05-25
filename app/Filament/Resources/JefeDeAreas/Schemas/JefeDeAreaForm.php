<?php

namespace App\Filament\Resources\JefeDeAreas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JefeDeAreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Nombre del encargado')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Select::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'nombre'),
                TextInput::make('cargo'),
            ]);
    }
}
