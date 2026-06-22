<?php

namespace App\Filament\Resources\RegistroAsistencias;

use App\Filament\Resources\RegistroAsistencias\Pages;
use App\Models\AsignacionBeca;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Becario;
use App\Models\PermisoMarcadoManual;
use App\Models\RegistroAsistencia;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Actions\CreateAction;

class RegistroAsistenciaResource extends Resource
{
    protected static ?string $model = RegistroAsistencia::class;
    protected static string|\UnitEnum|null $navigationGroup = 'Asistencias';
    protected static \BackedEnum | string | null $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Validar Asistencias';
    protected static ?string $modelLabel = 'Registro de Asistencia';
    protected static ?string $pluralModelLabel = 'Registros de Asistencia';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        if ($user->hasRole(['Super Administrador', 'Encargado General'])) {
            return $query;
        }

        if ($user->hasRole('Encargados')) {
            $jefe = $user->jefeDeArea;
            if (! $jefe) {
                return $query->whereRaw('1 = 0');
            }
            $asignacionIds = AsignacionBeca::query()->where('jefe_area_id', $jefe->id)->pluck('id');
            return $query->whereIn('asignacion_beca_id', $asignacionIds);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asignacionBeca.becario.user.name')
                    ->label('Becario')->searchable()->sortable(),
                TextColumn::make('asignacionBeca.area.nombre')->label('Área')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fecha')->date('d/m/Y')->sortable(),
                TextColumn::make('hora_entrada')->time('H:i')->label('Entrada'),
                TextColumn::make('hora_salida')->time('H:i')->label('Salida')->placeholder('—'),
                TextColumn::make('total_horas')->numeric(2)->label('Horas')->placeholder('—'),
                TextColumn::make('actividad_principal')->label('Actividad')->limit(40)->placeholder('—'),
                \Filament\Tables\Columns\IconColumn::make('evidencia')
                    ->label('Evidencia')
                    ->boolean()
                    ->state(fn(RegistroAsistencia $r) => filled($r->evidencia)),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'aprobado'  => 'success',
                        'rechazado' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado'  => 'Aprobado',
                        'rechazado' => 'Rechazado',
                    ])
                    ->default('pendiente'),
            ])
            ->recordActions([
                Action::make('descargarEvidencia')
                    ->label('Evidencia')->icon('heroicon-o-document-arrow-down')->color('gray')
                    ->visible(fn(RegistroAsistencia $r) => filled($r->evidencia))
                    ->action(function (RegistroAsistencia $r) {
                        if (! $r->evidencia || ! \Illuminate\Support\Facades\Storage::disk('local')->exists($r->evidencia)) {
                            Notification::make()->title('El archivo ya no está disponible')->warning()->send();
                            return;
                        }
                        return \Illuminate\Support\Facades\Storage::disk('local')->download($r->evidencia, $r->evidencia_nombre);
                    }),
                Action::make('aprobar')
                    ->label('Aprobar')->icon('heroicon-o-check-circle')->color('success')
                    ->requiresConfirmation()
                    // El documento de evidencia es obligatorio: sin él no se puede aprobar la jornada.
                    ->visible(fn(RegistroAsistencia $r) => $r->estado === 'pendiente' && $r->hora_salida !== null && filled($r->evidencia))
                    ->action(function (RegistroAsistencia $r) {
                        $r->update([
                            'estado' => 'aprobado',
                            'validado_por' => Auth::id(),
                            'motivo_rechazo' => null,
                        ]);
                        Notification::make()->title('Jornada aprobada')->success()->send();
                    }),
                Action::make('rechazar')
                    ->label('Rechazar')->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn(RegistroAsistencia $r) => $r->estado === 'pendiente' && $r->hora_salida !== null)
                    ->schema([
                        Textarea::make('motivo_rechazo')
                            ->label('Motivo del rechazo')
                            ->required()
                            ->minLength(5),
                    ])
                    ->action(function (RegistroAsistencia $r, array $data) {
                        $r->update([
                            'estado' => 'rechazado',
                            'validado_por' => Auth::id(),
                            'motivo_rechazo' => $data['motivo_rechazo'],
                        ]);
                        Notification::make()->title('Jornada rechazada')->success()->send();
                    }),
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn(RegistroAsistencia $r) => $r->estado === 'pendiente')
                    ->fillForm(fn(RegistroAsistencia $r) => [
                        'hora_entrada' => $r->hora_entrada,
                        'hora_salida'  => $r->hora_salida,
                    ])
                    ->schema([
                        \Filament\Forms\Components\DateTimePicker::make('hora_entrada')
                            ->label('Hora de entrada')
                            ->seconds(false)
                            ->required(),
                        \Filament\Forms\Components\DateTimePicker::make('hora_salida')
                            ->label('Hora de salida')
                            ->seconds(false)
                            ->required()
                            ->after('hora_entrada'),
                    ])
                    ->action(function (RegistroAsistencia $r, array $data) {
                        $entrada = \Carbon\Carbon::parse($data['hora_entrada']);
                        $salida  = \Carbon\Carbon::parse($data['hora_salida']);
                        $horas   = round($entrada->diffInMinutes($salida) / 60, 2);

                        $r->update([
                            'hora_entrada' => $entrada,
                            'hora_salida'  => $salida,
                            'total_horas'  => $horas,
                            'fecha'        => $entrada->toDateString(),
                        ]);

                        Notification::make()->title('Registro actualizado')->success()->send();
                    }),
                ViewAction::make(),
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
