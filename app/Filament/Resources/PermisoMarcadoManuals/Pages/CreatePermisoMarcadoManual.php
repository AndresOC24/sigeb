<?php

namespace App\Filament\Resources\PermisoMarcadoManuals\Pages;

use App\Filament\Resources\PermisoMarcadoManuals\PermisoMarcadoManualResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermisoMarcadoManual extends CreateRecord
{
    protected static string $resource = PermisoMarcadoManualResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['otorgado_por'] = auth()->id();
        return $data;
    }

}
