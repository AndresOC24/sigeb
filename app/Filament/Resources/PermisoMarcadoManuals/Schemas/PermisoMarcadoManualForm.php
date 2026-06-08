<?php

namespace App\Filament\Resources\PermisoMarcadoManuals\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PermisoMarcadoManualForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jefe_de_area_id')
                    ->relationship('jefeDeArea', 'id')
                    ->required(),
                Select::make('becario_id')
                    ->relationship('becario', 'id'),
                DateTimePicker::make('fecha_inicio')
                    ->required(),
                DateTimePicker::make('fecha_fin')
                    ->required(),
                Textarea::make('motivo')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('otorgado_por')
                    ->required()
                    ->numeric(),
                Toggle::make('revocado')
                    ->required(),
            ]);
    }
}
