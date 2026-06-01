<?php

namespace App\Filament\Resources\AsignacionBecas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AsignacionBecaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('becario_id')
                    ->options(
                        \App\Models\Becario::with('user')
                            ->get()
                            ->filter(fn($b) => $b->user?->name)
                            ->pluck('user.name', 'id')
                    )
                    ->required()
                    ->label('Becario'),
                Select::make('beca_id')
                    ->relationship('beca', 'nombre')
                    ->required()
                    ->label('Tipo de Beca'),
                Select::make('gestion_id')
                    ->relationship('gestion', 'nombre')
                    ->required()
                    ->label('Gestión'),
                Select::make('area_id')
                    ->relationship('area', 'nombre')
                    ->required()
                    ->label('Área'),
                Select::make('jefe_area_id')
                    ->options(
                        \App\Models\JefeDeArea::with('user')
                            ->get()
                            ->filter(fn($j) => $j->user?->name)
                            ->pluck('user.name', 'id')
                    )
                    ->required()
                    ->label('Encargado del Becario'),
                TextInput::make('porcentaje_obtenido')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->label('Descuento asignado'),
                TextInput::make('horas_acumuladas')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Horas acumuladas'),
                Select::make('estado')
                    ->options(['activa' => 'Activa', 'suspendida' => 'Suspendida', 'finalizada' => 'Finalizada'])
                    ->default('activa')
                    ->required()
                    ->label('Estado de la beca'),
            ]);
    }
}
