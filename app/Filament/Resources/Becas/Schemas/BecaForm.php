<?php

namespace App\Filament\Resources\Becas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BecaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
                TextInput::make('horas_requeridas')
                    ->numeric(),
                TextInput::make('porcentaje_beca')
                    ->numeric(),
                Select::make('tipo_beca')
                    ->options([
            'Beca ayudantía' => 'Beca ayudantía',
            'Beca Apoyo Institucional' => 'Beca apoyo institucional',
        ])
                    ->required(),
            ]);
    }
}
