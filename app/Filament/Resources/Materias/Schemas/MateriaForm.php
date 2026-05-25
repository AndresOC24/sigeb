<?php

namespace App\Filament\Resources\Materias\Schemas;

use App\Models\Carrera;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class MateriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('carrera_id')
                    ->options(Carrera::pluck('nombre', 'id'))
                    ->label('Carrera')
                    ->searchable()
                    ->required(),
                TextInput::make('nombre')
                    ->label('Nombre de la materia')
                    ->required(),
            ]);
    }
}
