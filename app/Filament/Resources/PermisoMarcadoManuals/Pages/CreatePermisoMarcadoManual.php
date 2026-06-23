<?php

namespace App\Filament\Resources\PermisoMarcadoManuals\Pages;

use App\Filament\Resources\PermisoMarcadoManuals\PermisoMarcadoManualResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePermisoMarcadoManual extends CreateRecord
{
    protected static string $resource = PermisoMarcadoManualResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $data['solicitado_por'] = $user->id;

        $esEncargadoDeArea = $user->hasRole('Encargados')
            && ! $user->hasRole(['Super Administrador', 'Encargado General']);

        if ($esEncargadoDeArea) {
            // El encargado de área solo solicita para su propia área; queda pendiente de aprobación.
            $data['jefe_de_area_id'] = $user->jefeDeArea?->id;
            $data['estado_solicitud'] = 'pendiente';
            $data['otorgado_por'] = null;
        } else {
            // La administración general otorga directamente: la solicitud nace aprobada.
            $data['estado_solicitud'] = 'aprobada';
            $data['otorgado_por'] = $user->id;
            $data['revisado_en'] = now();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Si quedó pendiente, avisar a la administración general para que la revise.
        if ($this->record->estado_solicitud !== 'pendiente') {
            return;
        }

        $destinatarios = User::role('Encargado General')->get();

        if ($destinatarios->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('Nueva solicitud de marcado manual')
            ->body($this->record->motivo)
            ->warning()
            ->sendToDatabase($destinatarios);
    }
}
