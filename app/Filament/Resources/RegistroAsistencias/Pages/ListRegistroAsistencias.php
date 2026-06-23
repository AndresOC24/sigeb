<?php

namespace App\Filament\Resources\RegistroAsistencias\Pages;

use App\Filament\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use App\Models\PlantillaEvidencia;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ListRegistroAsistencias extends ListRecords
{
    protected static string $resource = RegistroAsistenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('gestionarFormato')
                ->label('Formato de evidencia')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->visible(fn() => Auth::user()->hasRole(['Super Administrador', 'Encargado General']))
                ->modalHeading('Formato de evidencia para becarios')
                ->modalDescription('Sube el documento (PDF o Word) que los becarios descargarán como plantilla. El más reciente reemplaza al anterior como formato vigente.')
                ->modalSubmitActionLabel('Publicar formato')
                ->schema([
                    FileUpload::make('archivo')
                        ->label('Documento del formato')
                        ->disk('local')
                        ->directory('plantillas-evidencia')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ])
                        ->maxSize(10240)
                        ->storeFileNamesIn('archivo_nombre')
                        ->required(),
                ])
                ->action(function (array $data) {
                    PlantillaEvidencia::create([
                        'path' => $data['archivo'],
                        'nombre' => $data['archivo_nombre'] ?? basename($data['archivo']),
                        'subido_por' => Auth::id(),
                    ]);
                    Notification::make()->title('Formato publicado')->success()->send();
                }),
            Action::make('descargarFormato')
                ->label('Ver formato actual')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->visible(fn() => Auth::user()->hasRole(['Super Administrador', 'Encargado General']) && PlantillaEvidencia::vigente() !== null)
                ->action(function () {
                    $plantilla = PlantillaEvidencia::vigente();
                    if (! $plantilla || ! Storage::disk('local')->exists($plantilla->path)) {
                        Notification::make()->title('El archivo ya no está disponible')->warning()->send();
                        return;
                    }
                    return Storage::disk('local')->download($plantilla->path, $plantilla->nombre);
                }),
        ];
    }
}