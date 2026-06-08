<?php

namespace App\Filament\Resources\PermisoMarcadoManuals\Pages;

use App\Filament\Resources\PermisoMarcadoManuals\PermisoMarcadoManualResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPermisoMarcadoManual extends EditRecord
{
    protected static string $resource = PermisoMarcadoManualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
