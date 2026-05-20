<?php

namespace App\Filament\Resources\JefeDeAreas\Pages;

use App\Filament\Resources\JefeDeAreas\JefeDeAreaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJefeDeAreas extends ListRecords
{
    protected static string $resource = JefeDeAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
