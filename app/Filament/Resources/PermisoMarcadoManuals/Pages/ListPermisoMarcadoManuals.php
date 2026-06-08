<?php

namespace App\Filament\Resources\PermisoMarcadoManuals\Pages;

use App\Filament\Resources\PermisoMarcadoManuals\PermisoMarcadoManualResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPermisoMarcadoManuals extends ListRecords
{
    protected static string $resource = PermisoMarcadoManualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
