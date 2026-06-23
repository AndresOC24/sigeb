<?php

namespace App\Filament\Resources\PermisoMarcadoManuals;

use App\Filament\Resources\PermisoMarcadoManuals\Pages;
use App\Models\Becario;
use App\Models\JefeDeArea;
use App\Models\PermisoMarcadoManual;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PermisoMarcadoManualResource extends Resource
{
    protected static ?string $model = PermisoMarcadoManual::class;
    protected static \BackedEnum | string | null $navigationIcon = 'heroicon-o-key';
    protected static string|\UnitEnum|null $navigationGroup = 'Becas';
    protected static ?string $navigationLabel = 'Solicitudes de Marcado Manual';
    protected static ?string $modelLabel = 'Solicitud de Marcado Manual';
    protected static ?string $pluralModelLabel = 'Solicitudes de Marcado Manual';

    /**
     * ¿El usuario actual es un encargado de área (y no parte de la administración general)?
     */
    protected static function esEncargadoDeArea(): bool
    {
        $user = Auth::user();
        return $user
            && $user->hasRole('Encargados')
            && ! $user->hasRole(['Super Administrador', 'Encargado General']);
    }

    protected static function esAdministracion(): bool
    {
        return Auth::user()?->hasRole(['Super Administrador', 'Encargado General']) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('jefe_de_area_id')
                ->label('Jefe de Área')
                ->options(fn() => JefeDeArea::with('user', 'area')->get()
                    ->mapWithKeys(fn($j) => [$j->id => ($j->user->name ?? '?') . ' — ' . ($j->area->nombre ?? '?')]))
                ->required()
                ->searchable()
                ->live()
                // El encargado de área solicita siempre para su propia área:
                // el jefe se asigna automáticamente al crear.
                ->visible(fn() => static::esAdministracion()),
            Select::make('becario_id')
                ->label('Becario')
                ->helperText('Déjalo vacío para solicitar el permiso para TODOS los becarios del área.')
                ->options(function (callable $get) {
                    $jefeId = $get('jefe_de_area_id');
                    if (! $jefeId && static::esEncargadoDeArea()) {
                        $jefeId = Auth::user()->jefeDeArea?->id;
                    }
                    if (! $jefeId) return [];
                    $jefe = JefeDeArea::find($jefeId);
                    if (! $jefe) return [];
                    return Becario::whereHas('asignaciones', function ($q) use ($jefe) {
                        $q->where('estado', 'activa')->where('area_id', $jefe->area_id);
                    })->with('user')->get()->mapWithKeys(fn($b) => [$b->id => $b->user->name ?? '?']);
                })
                ->nullable()
                ->searchable(),
            DateTimePicker::make('fecha_inicio')->label('Vigente desde')->required()->default(now()),
            DateTimePicker::make('fecha_fin')->label('Vigente hasta')->required()->after('fecha_inicio')
                ->default(now()->addDays(7)),
            Textarea::make('motivo')->required()->minLength(10)->maxLength(500)->rows(3)
                ->placeholder('Ej: Cámara del becario averiada hasta nueva orden.')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jefeDeArea.user.name')->label('Jefe')->searchable(),
                TextColumn::make('jefeDeArea.area.nombre')->label('Área'),
                TextColumn::make('becario.user.name')->label('Becario')->placeholder('Todos del área'),
                TextColumn::make('fecha_inicio')->dateTime('d/m/Y H:i')->label('Desde'),
                TextColumn::make('fecha_fin')->dateTime('d/m/Y H:i')->label('Hasta'),
                TextColumn::make('estado')->badge()
                    ->getStateUsing(fn($record) => $record?->estado)
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'por_aprobar' => 'Por aprobar',
                        'vigente' => 'Vigente',
                        'pendiente' => 'Pendiente',
                        'vencido' => 'Vencido',
                        'revocado' => 'Revocado',
                        'rechazada' => 'Rechazada',
                        default => $state,
                    })
                    ->color(fn(string $state) => match ($state) {
                        'vigente' => 'success',
                        'por_aprobar' => 'warning',
                        'pendiente' => 'info',
                        'vencido' => 'gray',
                        'revocado', 'rechazada' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('solicitadoPor.name')->label('Solicitado por')->placeholder('—'),
                TextColumn::make('otorgadoPor.name')->label('Aprobado por')->placeholder('—'),
                TextColumn::make('motivo')->limit(40)->tooltip(fn($record) => $record?->motivo),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('estado_solicitud')
                    ->label('Estado de solicitud')
                    ->options([
                        'pendiente' => 'Por aprobar',
                        'aprobada' => 'Aprobada',
                        'rechazada' => 'Rechazada',
                    ]),
            ])
            ->recordActions([
                Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->estado_solicitud === 'pendiente' && static::esAdministracion())
                    ->schema([
                        DateTimePicker::make('fecha_inicio')->label('Vigente desde')->required(),
                        DateTimePicker::make('fecha_fin')->label('Vigente hasta')->required()->after('fecha_inicio'),
                    ])
                    ->fillForm(fn($record) => [
                        'fecha_inicio' => $record->fecha_inicio,
                        'fecha_fin' => $record->fecha_fin,
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'estado_solicitud' => 'aprobada',
                            'otorgado_por' => Auth::id(),
                            'revisado_en' => now(),
                            'fecha_inicio' => $data['fecha_inicio'],
                            'fecha_fin' => $data['fecha_fin'],
                            'motivo_rechazo' => null,
                        ]);
                        static::notificarSolicitante($record, 'Tu solicitud de marcado manual fue aprobada', 'success');
                        Notification::make()->title('Solicitud aprobada')->success()->send();
                    }),
                Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->estado_solicitud === 'pendiente' && static::esAdministracion())
                    ->schema([
                        Textarea::make('motivo_rechazo')->label('Motivo del rechazo')
                            ->required()->minLength(10)->maxLength(500)->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'estado_solicitud' => 'rechazada',
                            'motivo_rechazo' => $data['motivo_rechazo'],
                            'otorgado_por' => Auth::id(),
                            'revisado_en' => now(),
                        ]);
                        static::notificarSolicitante($record, 'Tu solicitud de marcado manual fue rechazada', 'danger');
                        Notification::make()->title('Solicitud rechazada')->success()->send();
                    }),
                Action::make('revocar')
                    ->label('Revocar')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->estado_solicitud === 'aprobada' && ! $record->revocado && static::esAdministracion())
                    ->action(function ($record) {
                        $record->update(['revocado' => true]);
                        Notification::make()->title('Permiso revocado')->success()->send();
                    }),
                EditAction::make()
                    ->visible(fn($record) => static::esAdministracion() || ($record->estado_solicitud === 'pendiente' && $record->solicitado_por === Auth::id())),
                DeleteAction::make()
                    ->visible(fn() => static::esAdministracion()),
            ]);
    }

    /**
     * Avisa por notificación de base de datos al encargado de área que pidió el permiso.
     */
    protected static function notificarSolicitante(PermisoMarcadoManual $record, string $titulo, string $color): void
    {
        if (! $record->solicitado_por) {
            return;
        }

        $solicitante = User::find($record->solicitado_por);
        if (! $solicitante) {
            return;
        }

        Notification::make()
            ->title($titulo)
            ->body($color === 'danger' && $record->motivo_rechazo ? "Motivo: {$record->motivo_rechazo}" : $record->motivo)
            ->{$color === 'danger' ? 'danger' : 'success'}()
            ->sendToDatabase($solicitante);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // El encargado de área solo ve sus propias solicitudes.
        if (static::esEncargadoDeArea()) {
            $query->where('jefe_de_area_id', Auth::user()->jefeDeArea?->id ?? 0);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermisoMarcadoManuals::route('/'),
            'create' => Pages\CreatePermisoMarcadoManual::route('/create'),
            'edit' => Pages\EditPermisoMarcadoManual::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->hasRole(['Super Administrador', 'Encargado General', 'Encargados']) ?? false;
    }

    public static function canAccess(): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::esAdministracion()
            || ($record->estado_solicitud === 'pendiente' && $record->solicitado_por === Auth::id());
    }

    public static function canDelete($record): bool
    {
        return static::esAdministracion();
    }
}
