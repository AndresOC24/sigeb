<?php

namespace App\Filament\Resources\Carreras\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CarreraInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre')
                    ->label('Nombre de la carrera'),
                TextEntry::make('descripcion')
                    ->label('Descripción')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Actualizado el')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
