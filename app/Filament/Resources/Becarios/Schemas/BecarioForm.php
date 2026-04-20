<?php

namespace App\Filament\Resources\Becarios\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BecarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Select::make('carrera_id')
                    ->relationship('carrera', 'nombre')
                    ->required(),
                TextInput::make('codigo_estudiante')
                    ->required(),
                Textarea::make('facial_data')
                    ->columnSpanFull(),
            ]);
    }
}
