<?php

namespace App\Filament\Resources\PermisoMarcadoManuals\Pages;

use App\Filament\Resources\PermisoMarcadoManuals\PermisoMarcadoManualResource;
use App\Models\Becario;
use App\Models\PermisoMarcadoManual;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPermisoMarcadoManuals extends ListRecords
{
    protected static string $resource = PermisoMarcadoManualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Botón siempre disponible para el encargado de área: crea una solicitud
            // pendiente y notifica al Encargado General.
            Action::make('solicitar')
                ->label('Solicitar Marcado Manual')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->visible(fn() => self::esEncargadoDeArea())
                ->modalHeading('Solicitar permiso de marcado manual')
                ->modalDescription('Tu solicitud será enviada al Encargado General para su aprobación.')
                ->modalSubmitActionLabel('Enviar solicitud')
                ->schema([
                    Select::make('becario_id')
                        ->label('Becario')
                        ->helperText('Déjalo vacío para solicitarlo para TODOS los becarios de tu área.')
                        ->options(function () {
                            $jefe = Auth::user()?->jefeDeArea;
                            if (! $jefe) return [];
                            return Becario::whereHas('asignaciones', function ($q) use ($jefe) {
                                $q->where('estado', 'activa')->where('area_id', $jefe->area_id);
                            })->with('user')->get()->mapWithKeys(fn($b) => [$b->id => $b->user->name ?? '?']);
                        })
                        ->searchable()
                        ->nullable(),
                    DateTimePicker::make('fecha_inicio')->label('Vigente desde')->required()->default(now()),
                    DateTimePicker::make('fecha_fin')->label('Vigente hasta')->required()
                        ->after('fecha_inicio')->default(now()->addDays(7)),
                    Textarea::make('motivo')->label('Motivo')->required()->minLength(10)->maxLength(500)->rows(3)
                        ->placeholder('Ej: Cámara del becario averiada hasta nueva orden.'),
                ])
                ->action(function (array $data) {
                    $user = Auth::user();
                    $jefe = $user->jefeDeArea;

                    if (! $jefe) {
                        Notification::make()
                            ->title('No tienes un área asignada')
                            ->body('Pide al Encargado General que te registre como jefe de un área antes de solicitar.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $permiso = PermisoMarcadoManual::create([
                        'jefe_de_area_id' => $jefe->id,
                        'becario_id' => $data['becario_id'] ?? null,
                        'fecha_inicio' => $data['fecha_inicio'],
                        'fecha_fin' => $data['fecha_fin'],
                        'motivo' => $data['motivo'],
                        'estado_solicitud' => 'pendiente',
                        'solicitado_por' => $user->id,
                    ]);

                    self::notificarEncargadoGeneral($permiso);

                    Notification::make()
                        ->title('Solicitud enviada')
                        ->body('El Encargado General la revisará pronto.')
                        ->success()
                        ->send();
                }),

            // La administración general puede otorgar directamente.
            CreateAction::make()
                ->label('Otorgar directamente')
                ->visible(fn() => Auth::user()?->hasRole(['Super Administrador', 'Encargado General']) ?? false),
        ];
    }

    protected static function esEncargadoDeArea(): bool
    {
        $user = Auth::user();
        return $user
            && $user->hasRole('Encargados')
            && ! $user->hasRole(['Super Administrador', 'Encargado General']);
    }

    /**
     * Notifica en el panel únicamente a los Encargados Generales.
     */
    protected static function notificarEncargadoGeneral(PermisoMarcadoManual $permiso): void
    {
        $destinatarios = User::role('Encargado General')->get();

        if ($destinatarios->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('Nueva solicitud de marcado manual')
            ->body($permiso->motivo)
            ->warning()
            ->sendToDatabase($destinatarios);
    }
}
