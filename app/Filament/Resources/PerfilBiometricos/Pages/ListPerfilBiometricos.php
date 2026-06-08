<?php

namespace App\Filament\Resources\PerfilBiometricos\Pages;

use App\Filament\Resources\PerfilBiometricos\PerfilBiometricoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPerfilBiometricos extends ListRecords
{
    protected static string $resource = PerfilBiometricoResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
