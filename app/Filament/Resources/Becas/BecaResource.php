<?php

namespace App\Filament\Resources\Becas;

use App\Filament\Resources\Becas\Pages\CreateBeca;
use App\Filament\Resources\Becas\Pages\EditBeca;
use App\Filament\Resources\Becas\Pages\ListBecas;
use App\Filament\Resources\Becas\Pages\ViewBeca;
use App\Filament\Resources\Becas\Schemas\BecaForm;
use App\Filament\Resources\Becas\Schemas\BecaInfolist;
use App\Filament\Resources\Becas\Tables\BecasTable;
use App\Models\Beca;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BecaResource extends Resource
{
    protected static ?string $model = Beca::class;

      protected static string|\UnitEnum|null $navigationGroup = 'Becas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $recordTitleAttribute = 'Becas';

    public static function form(Schema $schema): Schema
    {
        return BecaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BecaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BecasTable::configure($table);
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
            'index' => ListBecas::route('/'),
            'create' => CreateBeca::route('/create'),
            'view' => ViewBeca::route('/{record}'),
            'edit' => EditBeca::route('/{record}/edit'),
        ];
    }
}
