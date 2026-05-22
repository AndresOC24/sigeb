<?php

namespace App\Filament\Resources\Becas\Pages;

use App\Filament\Resources\Becas\BecaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBecas extends ListRecords
{
    protected static string $resource = BecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
