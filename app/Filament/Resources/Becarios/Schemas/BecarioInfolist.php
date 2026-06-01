<?php

namespace App\Filament\Resources\Becarios\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BecarioInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Becario'),
                TextEntry::make('carrera.id')
                    ->label('Carrera'),
                TextEntry::make('codigo_estudiante'),
                TextEntry::make('facial_data')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->label('Creado el')
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->label('Actualizado el')
                    ->placeholder('-'),
            ]);
    }
}
