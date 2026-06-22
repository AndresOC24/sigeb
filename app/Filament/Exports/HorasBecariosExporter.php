<?php

namespace App\Filament\Exports;

use App\Models\AsignacionBeca;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Facades\Auth;

class HorasBecariosExporter extends Exporter
{
    protected static ?string $model = AsignacionBeca::class;

    public static function getColumns(): array
    {
        $columns = [
            ExportColumn::make('becario.user.name')->label('Becario'),
            ExportColumn::make('becario.codigo_estudiante')->label('Código'),
            ExportColumn::make('becario.user.email')->label('Correo'),
            ExportColumn::make('becario.carrera.nombre')->label('Carrera'),
            ExportColumn::make('area.nombre')->label('Área'),
            ExportColumn::make('beca.nombre')->label('Beca'),
            ExportColumn::make('gestion.nombre')->label('Gestión'),
            ExportColumn::make('horas_acumuladas')->label('Horas Acumuladas'),
            ExportColumn::make('beca.horas_requeridas')->label('Horas Requeridas'),
            ExportColumn::make('progreso')
                ->label('% Progreso')
                ->state(function (AsignacionBeca $r): string {
                    $req = $r->beca?->horas_requeridas;
                    return $req ? round(($r->horas_acumuladas / $req) * 100, 1) . '%' : '—';
                }),
            ExportColumn::make('estado')->label('Estado'),
        ];

        // Solo el Encargado General o el Super Administrador pueden exportar el encargado de área.
        if (Auth::user()?->hasAnyRole(['Super Administrador', 'Encargado General'])) {
            $columns[] = ExportColumn::make('jefeArea.user.name')
                ->label('Encargado de Área');
        }

        return $columns;
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Reporte de horas exportado: ' . number_format($export->successful_rows) . ' filas.';
        if ($failed = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' fallaron.';
        }
        return $body;
    }
}