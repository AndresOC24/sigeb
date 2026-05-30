<?php

namespace App\Filament\Becario\Resources\RegistroAsistencias\Pages;

use App\Filament\Becario\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;


class ViewRegistroAsistencia extends ViewRecord
{
    protected static string $resource = RegistroAsistenciaResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('fecha')->date('d/m/Y'),
            TextEntry::make('hora_entrada')->dateTime('d/m/Y H:i'),
            TextEntry::make('hora_salida')->dateTime('d/m/Y H:i')->placeholder('Jornada abierta'),
            TextEntry::make('total_horas')->numeric(2)->placeholder('—'),
            TextEntry::make('estado')->badge(),
            TextEntry::make('actividad_principal')->placeholder('—'),
            TextEntry::make('motivo_rechazo')->placeholder('—')->visible(fn ($record) => $record->estado === 'rechazado'),
            IconEntry::make('verificado_facial')->boolean()->label('Verificado facial'),
            TextEntry::make('confidence_score')->numeric(2)->placeholder('—'),
        ]);
    }
}