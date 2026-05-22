<?php

namespace App\Filament\Resources\Becas\Pages;

use App\Filament\Resources\Becas\BecaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBeca extends ViewRecord
{
    protected static string $resource = BecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
