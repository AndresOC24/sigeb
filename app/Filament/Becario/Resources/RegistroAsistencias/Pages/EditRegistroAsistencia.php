<?php

namespace App\Filament\Becario\Resources\RegistroAsistencias\Pages;

use App\Filament\Becario\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRegistroAsistencia extends EditRecord
{
    protected static string $resource = RegistroAsistenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
