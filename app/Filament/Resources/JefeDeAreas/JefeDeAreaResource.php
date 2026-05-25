<?php

namespace App\Filament\Resources\JefeDeAreas;

use App\Filament\Resources\JefeDeAreas\Pages\CreateJefeDeArea;
use App\Filament\Resources\JefeDeAreas\Pages\EditJefeDeArea;
use App\Filament\Resources\JefeDeAreas\Pages\ListJefeDeAreas;
use App\Filament\Resources\JefeDeAreas\Pages\ViewJefeDeArea;
use App\Filament\Resources\JefeDeAreas\Schemas\JefeDeAreaForm;
use App\Filament\Resources\JefeDeAreas\Schemas\JefeDeAreaInfolist;
use App\Filament\Resources\JefeDeAreas\Tables\JefeDeAreasTable;
use App\Models\JefeDeArea;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JefeDeAreaResource extends Resource
{
    protected static ?string $model = JefeDeArea::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Encargados';

    protected static ?string $navigationLabel = 'Encargados';
    protected static ?string $modelLabel = 'Encargado';
    protected static ?string $pluralModelLabel = 'Encargados';

    public static function form(Schema $schema): Schema
    {
        return JefeDeAreaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JefeDeAreaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JefeDeAreasTable::configure($table);
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
            'index' => ListJefeDeAreas::route('/'),
            'create' => CreateJefeDeArea::route('/create'),
            'view' => ViewJefeDeArea::route('/{record}'),
            'edit' => EditJefeDeArea::route('/{record}/edit'),
        ];
    }
}
