<?php

namespace App\Filament\Resources\Becarios\Pages;

use App\Filament\Resources\Becarios\BecarioResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBecario extends ViewRecord
{
    protected static string $resource = BecarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
