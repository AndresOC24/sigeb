<?php

namespace App\Filament\Resources\JefeDeAreas\Pages;

use App\Filament\Resources\JefeDeAreas\JefeDeAreaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewJefeDeArea extends ViewRecord
{
    protected static string $resource = JefeDeAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
