<?php

namespace App\Filament\Resources\PerfilBiometricos;

use App\Filament\Resources\PerfilBiometricos\Pages;
use App\Models\Rostro;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PerfilBiometricoResource extends Resource
{
    protected static ?string $model = \App\Models\User::class;
    protected static \BackedEnum | string | null $navigationIcon = 'heroicon-o-finger-print';
    protected static string|\UnitEnum|null $navigationGroup = 'Becas';
    protected static ?string $navigationLabel = 'Perfiles Biométricos';
    protected static ?string $modelLabel = 'Perfil Biométrico';
    protected static ?string $pluralModelLabel = 'Perfiles Biométricos';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', fn($q) => $q->where('name', 'Becario'))
            ->with(['rostro', 'becario.carrera']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Becario')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('becario.codigo_estudiante')->label('Código')->searchable(),
                TextColumn::make('becario.carrera.nombre')->label('Carrera')->placeholder('—'),
                IconColumn::make('rostro_registrado')
                    ->label('Rostro registrado')
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->rostro !== null),
                TextColumn::make('rostro.created_at')
                    ->label('Registrado el')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin registrar'),
            ])
            ->defaultSort('name')
            ->filters([
                TernaryFilter::make('rostro_registrado')
                    ->label('Con rostro registrado')
                    ->queries(
                        true: fn(Builder $q) => $q->whereHas('rostro'),
                        false: fn(Builder $q) => $q->whereDoesntHave('rostro'),
                        blank: fn(Builder $q) => $q,
                    ),
            ])
            ->recordActions([
                Action::make('resetear')
                    ->label('Resetear rostro')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Resetear rostro registrado?')
                    ->modalDescription('Se eliminará el descriptor facial actual. El becario deberá registrar su rostro nuevamente la próxima vez que marque asistencia.')
                    ->modalSubmitActionLabel('Sí, resetear')
                    ->visible(fn($record) => $record->rostro !== null)
                    ->action(function ($record) {
                        $record->rostro?->delete();
                        Notification::make()
                            ->title('Rostro reseteado')
                            ->body("El becario {$record->name} deberá registrar su rostro nuevamente.")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerfilBiometricos::route('/'),
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

    public static function canCreate(): bool
    {
        return false;
    }
}