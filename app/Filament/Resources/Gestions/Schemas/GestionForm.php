<?php

namespace App\Filament\Resources\Gestions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                DatePicker::make('fecha_inicio')
                    ->required(),
                DatePicker::make('fecha_fin')
                    ->required(),
            ]);
    }
}
