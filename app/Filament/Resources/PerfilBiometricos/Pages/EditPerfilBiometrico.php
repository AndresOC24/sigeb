<?php

namespace App\Filament\Resources\PerfilBiometricos\Pages;

use App\Filament\Resources\PerfilBiometricos\PerfilBiometricoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPerfilBiometrico extends EditRecord
{
    protected static string $resource = PerfilBiometricoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
