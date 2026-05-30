<?php

namespace App\Filament\Resources\RegistroAsistencias\Pages;

use App\Filament\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegistroAsistencias extends ListRecords
{
    protected static string $resource = RegistroAsistenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
