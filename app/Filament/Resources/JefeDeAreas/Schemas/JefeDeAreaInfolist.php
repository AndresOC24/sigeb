<?php

namespace App\Filament\Resources\JefeDeAreas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class JefeDeAreaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('area.id')
                    ->label('Area')
                    ->placeholder('-'),
                TextEntry::make('cargo')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
