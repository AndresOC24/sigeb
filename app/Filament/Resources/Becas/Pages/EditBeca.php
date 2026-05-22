<?php

namespace App\Filament\Resources\Becas\Pages;

use App\Filament\Resources\Becas\BecaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBeca extends EditRecord
{
    protected static string $resource = BecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
