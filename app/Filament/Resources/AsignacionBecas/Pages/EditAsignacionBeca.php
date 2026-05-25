<?php

namespace App\Filament\Resources\AsignacionBecas\Pages;

use App\Filament\Resources\AsignacionBecas\AsignacionBecaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsignacionBeca extends EditRecord
{
    protected static string $resource = AsignacionBecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
