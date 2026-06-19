<?php

namespace App\Filament\Becario\Resources\RegistroAsistencias\Pages;

use App\Filament\Becario\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use App\Models\PlantillaEvidencia;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListRegistroAsistencias extends ListRecords
{
    protected static string $resource = RegistroAsistenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('descargarFormato')
                ->label('Descargar formato de evidencia')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function () {
                    $plantilla = PlantillaEvidencia::vigente();
                    if (! $plantilla || ! Storage::disk('local')->exists($plantilla->path)) {
                        Notification::make()
                            ->title('Aún no hay un formato disponible')
                            ->body('Tu encargado todavía no ha publicado el formato de evidencia.')
                            ->warning()
                            ->send();
                        return;
                    }
                    return Storage::disk('local')->download($plantilla->path, $plantilla->nombre);
                }),
        ];
    }
}
