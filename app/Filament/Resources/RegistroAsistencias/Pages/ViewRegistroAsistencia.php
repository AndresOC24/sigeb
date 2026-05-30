<?php

namespace App\Filament\Resources\RegistroAsistencias\Pages;

use App\Filament\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistroAsistencia extends ViewRecord
{
    protected static string $resource = RegistroAsistenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
