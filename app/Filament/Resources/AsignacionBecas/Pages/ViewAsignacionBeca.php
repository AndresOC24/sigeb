<?php

namespace App\Filament\Resources\AsignacionBecas\Pages;

use App\Filament\Resources\AsignacionBecas\AsignacionBecaResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewAsignacionBeca extends ViewRecord
{
    protected static string $resource = AsignacionBecaResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('becario.user.name')->label('Becario'),
            TextEntry::make('becario.codigo_estudiante')->label('Código estudiante'),
            TextEntry::make('becario.carrera.nombre')->label('Carrera'),
            TextEntry::make('beca.nombre')->label('Beca'),
            TextEntry::make('beca.tipo_beca')->label('Tipo de beca')->badge(),
            TextEntry::make('gestion.nombre')->label('Gestión'),
            TextEntry::make('area.nombre')->label('Área'),
            TextEntry::make('jefeArea.user.name')->label('Encargado'),
            TextEntry::make('porcentaje_obtenido')->label('Porcentaje obtenido')->suffix('%'),
            TextEntry::make('horas_acumuladas')->label('Horas acumuladas'),
            TextEntry::make('estado')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'activa' => 'success',
                    'suspendida' => 'warning',
                    'finalizada' => 'gray',
                    default => 'gray',
                }),
            TextEntry::make('created_at')->label('Creado el')->dateTime('d/m/Y H:i'),
            TextEntry::make('updated_at')->label('Actualizado el')->dateTime('d/m/Y H:i'),
        ]);
    }
}