<?php

namespace App\Filament\Resources\PermisoMarcadoManuals;

use App\Filament\Resources\PermisoMarcadoManuals\Pages;
use App\Models\Becario;
use App\Models\JefeDeArea;
use App\Models\PermisoMarcadoManual;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PermisoMarcadoManualResource extends Resource
{
    protected static ?string $model = PermisoMarcadoManual::class;
    protected static \BackedEnum | string | null $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Permisos Marcado Manual';
    protected static ?string $modelLabel = 'Permiso de Marcado Manual';
    protected static ?string $pluralModelLabel = 'Permisos de Marcado Manual';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('jefe_de_area_id')
                ->label('Jefe de Área')
                ->options(fn() => JefeDeArea::with('user', 'area')->get()
                    ->mapWithKeys(fn($j) => [$j->id => ($j->user->name ?? '?') . ' — ' . ($j->area->nombre ?? '?')]))
                ->required()
                ->searchable()
                ->live(),
            Select::make('becario_id')
                ->label('Becario')
                ->helperText('Déjalo vacío para permitir marcar a TODOS los becarios del área del jefe.')
                ->options(function (callable $get) {
                    $jefeId = $get('jefe_de_area_id');
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
                ->placeholder('Ej: Cámara del becario averiada hasta nueva orden.'),
            Toggle::make('revocado')->label('Revocado')->default(false),
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
                    ->color(fn(string $state) => match ($state) {
                        'vigente' => 'success',
                        'pendiente' => 'info',
                        'vencido' => 'gray',
                        'revocado' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('otorgadoPor.name')->label('Otorgado por'),
                TextColumn::make('motivo')->limit(40)->tooltip(fn($record) => $record?->motivo),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'vigente' => 'Vigente',
                        'pendiente' => 'Pendiente',
                        'vencido' => 'Vencido',
                        'revocado' => 'Revocado',
                    ])
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return $query;
                        return match ($data['value']) {
                            'vigente' => $query->vigentes(),
                            'pendiente' => $query->where('fecha_inicio', '>', now())->where('revocado', false),
                            'vencido' => $query->where('fecha_fin', '<', now())->where('revocado', false),
                            'revocado' => $query->where('revocado', true),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('revocar')
                    ->label('Revocar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn($record) => ! $record->revocado)
                    ->action(function ($record) {
                        $record->update(['revocado' => true]);
                        Notification::make()->title('Permiso revocado')->success()->send();
                    }),
                DeleteAction::make(),
            ]);
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
        return Auth::user()?->hasRole(['Super Administrador', 'Encargado General']) ?? false;
    }

    public static function canAccess(): bool
    {
        return static::canViewAny();
    }
}