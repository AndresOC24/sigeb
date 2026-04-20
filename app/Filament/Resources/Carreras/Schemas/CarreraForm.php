<?php

namespace App\Filament\Resources\Carreras\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CarreraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('descripcion'),
            ]);
    }
}
