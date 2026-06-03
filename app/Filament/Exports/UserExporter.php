<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
            ->label('Nombre'),
            ExportColumn::make('email')
            ->label('Correo Electrónico'),
            ExportColumn::make('activo')
            ->label('Estado')
            ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
            ExportColumn::make('created_at')
            ->label('Creado el'),
            ExportColumn::make('updated_at')
            ->label('Actualizado el'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Tu exportación se ha completado y ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' fallaron al exportar.';
        }

        return $body;
    }
}
