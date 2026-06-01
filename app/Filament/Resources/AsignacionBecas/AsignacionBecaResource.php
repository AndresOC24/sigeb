<?php

namespace App\Filament\Resources\AsignacionBecas;

use App\Filament\Resources\AsignacionBecas\Pages\CreateAsignacionBeca;
use App\Filament\Resources\AsignacionBecas\Pages\EditAsignacionBeca;
use App\Filament\Resources\AsignacionBecas\Pages\ListAsignacionBecas;
use App\Filament\Resources\AsignacionBecas\Schemas\AsignacionBecaForm;
use App\Filament\Resources\AsignacionBecas\Pages\ViewAsignacionBeca;
use App\Filament\Resources\AsignacionBecas\Tables\AsignacionBecasTable;
use App\Models\AsignacionBeca;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AsignacionBecaResource extends Resource
{
    protected static ?string $model = AsignacionBeca::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Becas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    protected static ?string $recordTitleAttribute = 'Asignaciones';

    public static function form(Schema $schema): Schema
    {
        return AsignacionBecaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AsignacionBecasTable::configure($table);
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
            'index' => ListAsignacionBecas::route('/'),
            'create' => CreateAsignacionBeca::route('/create'),
            'view' => ViewAsignacionBeca::route('/{record}'),
            'edit' => EditAsignacionBeca::route('/{record}/edit'),
        ];
    }
}
