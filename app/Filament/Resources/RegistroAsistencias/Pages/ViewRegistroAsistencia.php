<?php

namespace App\Filament\Resources\RegistroAsistencias\Pages;

use App\Filament\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewRegistroAsistencia extends ViewRecord
{
    protected static string $resource = RegistroAsistenciaResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('asignacionBeca.becario.user.name')->label('Becario'),
            TextEntry::make('asignacionBeca.area.nombre')->label('Área'),
            TextEntry::make('fecha')->date('d/m/Y'),
            TextEntry::make('hora_entrada')->dateTime('d/m/Y H:i')->label('Entrada'),
            TextEntry::make('hora_salida')->dateTime('d/m/Y H:i')->label('Salida')->placeholder('Jornada abierta'),
            TextEntry::make('total_horas')->numeric(2)->label('Horas')->placeholder('—'),
            TextEntry::make('actividad_principal')->label('Actividad')->placeholder('—'),
            TextEntry::make('estado')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pendiente' => 'warning',
                    'aprobado'  => 'success',
                    'rechazado' => 'danger',
                    default     => 'gray',
                }),
            TextEntry::make('motivo_rechazo')->placeholder('—')->visible(fn ($record) => $record->estado === 'rechazado'),
            TextEntry::make('validadoPor.name')->label('Validado por')->placeholder('—'),
            IconEntry::make('verificado_facial')->boolean()->label('Verificado facial'),
            TextEntry::make('confidence_score')->numeric(2)->placeholder('—'),
        ]);
    }
}