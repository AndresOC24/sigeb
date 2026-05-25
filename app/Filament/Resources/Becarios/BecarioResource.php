<?php

namespace App\Filament\Resources\Becarios;

use App\Filament\Resources\Becarios\Pages\CreateBecario;
use App\Filament\Resources\Becarios\Pages\EditBecario;
use App\Filament\Resources\Becarios\Pages\ListBecarios;
use App\Filament\Resources\Becarios\Pages\ViewBecario;
use App\Filament\Resources\Becarios\Schemas\BecarioForm;
use App\Filament\Resources\Becarios\Schemas\BecarioInfolist;
use App\Filament\Resources\Becarios\Tables\BecariosTable;
use App\Models\Becario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BecarioResource extends Resource
{
    protected static ?string $model = Becario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Becario';

    protected static ?string $navigationLabel = 'Becarios';
    protected static ?string $modelLabel = 'Becario';
    protected static ?string $pluralModelLabel = 'Becarios';

    public static function form(Schema $schema): Schema
    {
        return BecarioForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BecarioInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BecariosTable::configure($table);
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
            'index' => ListBecarios::route('/'),
            'create' => CreateBecario::route('/create'),
            'view' => ViewBecario::route('/{record}'),
            'edit' => EditBecario::route('/{record}/edit'),
        ];
    }
}
