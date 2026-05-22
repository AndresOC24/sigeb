<?php

namespace App\Filament\Resources\Gestions;

use App\Filament\Resources\Gestions\Pages\CreateGestion;
use App\Filament\Resources\Gestions\Pages\EditGestion;
use App\Filament\Resources\Gestions\Pages\ListGestions;
use App\Filament\Resources\Gestions\Schemas\GestionForm;
use App\Filament\Resources\Gestions\Tables\GestionsTable;
use App\Models\Gestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GestionResource extends Resource
{
    protected static ?string $model = Gestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Gestion';

    public static function form(Schema $schema): Schema
    {
        return GestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGestions::route('/'),
            'create' => CreateGestion::route('/create'),
            'edit' => EditGestion::route('/{record}/edit'),
        ];
    }
}
