<?php

namespace App\Filament\Resources\AsignacionBecas\Pages;

use App\Filament\Resources\AsignacionBecas\AsignacionBecaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsignacionBecas extends ListRecords
{
    protected static string $resource = AsignacionBecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
