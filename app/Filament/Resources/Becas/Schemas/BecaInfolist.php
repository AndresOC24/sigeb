<?php

namespace App\Filament\Resources\Becas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BecaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('descripcion')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('horas_requeridas')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('porcentaje_beca')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tipo_beca')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
