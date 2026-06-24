<?php

namespace App\Filament\Becario\Resources\RegistroAsistencias;

use App\Filament\Becario\Resources\RegistroAsistencias\Pages;
use App\Models\RegistroAsistencia;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\IconColumn;

class RegistroAsistenciaResource extends Resource
{
    protected static ?string $model = RegistroAsistencia::class;
    protected static \BackedEnum | string | null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Mi Historial';
    protected static ?string $modelLabel = 'Registro de Asistencia';
    protected static ?string $pluralModelLabel = 'Registros de Asistencia';

    public static function getEloquentQuery(): Builder
    {
        $becario = auth()->user()->becario;
        $asignacionIds = $becario
            ? $becario->asignaciones()->pluck('id')
            : collect();

        return parent::getEloquentQuery()
            ->whereIn('asignacion_beca_id', $asignacionIds);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('15s')
            ->columns([
                TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('hora_entrada')
                    ->time('H:i')
                    ->label('Entrada'),
                TextColumn::make('hora_salida')
                    ->time('H:i')
                    ->label('Salida')
                    ->placeholder('—'),
                TextColumn::make('total_horas')
                    ->label('Horas')
                    ->numeric(2)
                    ->placeholder('—'),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'aprobado'  => 'success',
                        'rechazado' => 'danger',
                        default     => 'gray',
                    }),
                IconColumn::make('evidencia')
                    ->label('Evidencia')
                    ->boolean()
                    ->state(fn(RegistroAsistencia $r) => filled($r->evidencia)),
                IconColumn::make('verificado_facial')
                    ->label('Facial')
                    ->boolean(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado'  => 'Aprobado',
                        'rechazado' => 'Rechazado',
                    ]),
                Filter::make('fecha')
                    ->schema([
                        DatePicker::make('desde'),
                        DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn($q, $d) => $q->whereDate('fecha', '>=', $d))
                            ->when($data['hasta'] ?? null, fn($q, $d) => $q->whereDate('fecha', '<=', $d));
                    }),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                Action::make('subirEvidencia')
                    ->label(fn(RegistroAsistencia $r) => $r->evidencia ? 'Reemplazar evidencia' : 'Subir evidencia')
                    ->icon('heroicon-o-paper-clip')
                    ->color(fn(RegistroAsistencia $r) => $r->evidencia ? 'gray' : 'primary')
                    // Solo para jornadas cerradas que aún no se aprueban. Si fue rechazada,
                    // puede volver a subir el documento corregido.
                    ->visible(fn(RegistroAsistencia $r) => $r->hora_salida !== null && in_array($r->estado, ['pendiente', 'rechazado']))
                    ->modalHeading('Subir evidencia de la jornada')
                    ->modalDescription('Adjunta tu informe (PDF o Word) con la descripción y las fotografías de lo realizado, usando el formato del área.')
                    ->modalSubmitActionLabel('Guardar evidencia')
                    ->schema([
                        FileUpload::make('evidencia')
                            ->label('Documento de evidencia')
                            ->disk('local')
                            ->directory('evidencias')
                            ->visibility('private')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(10240)
                            ->storeFileNamesIn('evidencia_nombre')
                            ->required(),
                    ])
                    ->action(function (RegistroAsistencia $r, array $data) {
                        // Reemplazo: elimina el archivo anterior para no dejar huérfanos.
                        if ($r->evidencia && Storage::disk('local')->exists($r->evidencia)) {
                            Storage::disk('local')->delete($r->evidencia);
                        }
                        $r->update([
                            'evidencia' => $data['evidencia'],
                            'evidencia_nombre' => $data['evidencia_nombre'] ?? basename($data['evidencia']),
                            'evidencia_subida_en' => now(),
                        ]);
                        Notification::make()->title('Evidencia subida correctamente')->success()->send();
                    }),
                Action::make('descargarEvidencia')
                    ->label('Descargar evidencia')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->visible(fn(RegistroAsistencia $r) => filled($r->evidencia))
                    ->action(function (RegistroAsistencia $r) {
                        if (! $r->evidencia || ! Storage::disk('local')->exists($r->evidencia)) {
                            Notification::make()->title('El archivo ya no está disponible')->warning()->send();
                            return;
                        }
                        return Storage::disk('local')->download($r->evidencia, $r->evidencia_nombre);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistroAsistencias::route('/'),
            'view'  => Pages\ViewRegistroAsistencia::route('/{record}'),
        ];
    }
}
