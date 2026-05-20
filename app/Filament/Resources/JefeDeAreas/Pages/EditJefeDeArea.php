<?php

namespace App\Filament\Resources\JefeDeAreas\Pages;

use App\Filament\Resources\JefeDeAreas\JefeDeAreaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditJefeDeArea extends EditRecord
{
    protected static string $resource = JefeDeAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
