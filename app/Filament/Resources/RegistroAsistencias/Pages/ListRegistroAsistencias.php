<?php

namespace App\Filament\Resources\RegistroAsistencias\Pages;

use App\Filament\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use App\Models\Becario;
use App\Models\RegistroAsistencia;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListRegistroAsistencias extends ListRecords
{
    protected static string $resource = RegistroAsistenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('marcadoManual')
                ->label('Marcado Manual')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->visible(fn() => $this->puedeMarcarManual())
                ->modalHeading('Marcado manual de asistencia')
                ->modalDescription('Crea un registro sin verificación facial. Quedará aprobado automáticamente.')
                ->modalSubmitActionLabel('Registrar')
                ->schema([
                    Select::make('becario_id')
                        ->label('Becario')
                        ->options(fn() => $this->becariosDisponibles())
                        ->required()
                        ->searchable(),
                    DatePicker::make('fecha')->required()->default(today())->maxDate(today()),
                    DateTimePicker::make('hora_entrada')->required()->seconds(false)
                        ->default(now()->startOfHour()),
                    DateTimePicker::make('hora_salida')->nullable()->seconds(false)
                        ->after('hora_entrada')
                        ->helperText('Déjalo vacío si solo registras la entrada (jornada abierta).'),
                    Textarea::make('actividad_principal')
                        ->label('Actividad principal')
                        ->minLength(10)->maxLength(2000)->rows(3)
                        ->requiredWith('hora_salida'),
                    Textarea::make('motivo_manual')
                        ->label('Motivo del marcado manual')
                        ->required()->minLength(10)->maxLength(500)->rows(2)
                        ->placeholder('Ej: Becario olvidó marcar entrada, cámara averiada, etc.'),
                ])
                ->action(function (array $data) {
                    $becario = Becario::with('asignaciones')->find($data['becario_id']);
                    if (! $becario) {
                        Notification::make()->title('Becario no encontrado')->danger()->send();
                        return;
                    }

                    $asignacion = $becario->asignaciones()->where('estado', 'activa')->first();
                    if (! $asignacion) {
                        Notification::make()->title('El becario no tiene asignación activa')->danger()->send();
                        return;
                    }

                    $horas = null;
                    if (! empty($data['hora_salida'])) {
                        $entrada = Carbon::parse($data['hora_entrada']);
                        $salida = Carbon::parse($data['hora_salida']);
                        $horas = round($entrada->diffInMinutes($salida) / 60, 2);
                    }

                    $motivo = $data['motivo_manual'];
                    $actividad = $data['actividad_principal'] ?? null;

                    if ($actividad) {
                        $actividad .= "\n\n[Marcado manual: {$motivo}]";
                    } else {
                        $actividad = "[Marcado manual: {$motivo}]";
                    }

                    RegistroAsistencia::create([
                        'asignacion_beca_id' => $asignacion->id,
                        'fecha' => $data['fecha'],
                        'hora_entrada' => $data['hora_entrada'],
                        'hora_salida' => $data['hora_salida'] ?? null,
                        'total_horas' => $horas,
                        'actividad_principal' => $actividad,
                        'estado' => empty($data['hora_salida']) ? 'pendiente' : 'aprobado',
                        'verificado_facial' => false,
                        'confidence_score' => null,
                        'validado_por' => empty($data['hora_salida']) ? null : Auth::id(),
                        'fecha_validacion' => empty($data['hora_salida']) ? null : now(),
                    ]);

                    Notification::make()
                        ->title(empty($data['hora_salida']) ? 'Entrada manual registrada' : 'Jornada manual registrada y aprobada')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function puedeMarcarManual(): bool
    {
        $user = Auth::user();
        if ($user->hasRole(['Super Administrador', 'Encargado General'])) {
            return true;
        }
        if ($user->hasRole('Encargados')) {
            $jefe = $user->jefeDeArea;
            return $jefe && $jefe->tienePermisoMarcadoManualVigente();
        }
        return false;
    }

    protected function becariosDisponibles(): array
    {
        $user = Auth::user();

        if ($user->hasRole(['Super Administrador', 'Encargado General'])) {
            return Becario::whereHas('asignaciones', fn($q) => $q->where('estado', 'activa'))
                ->with('user')
                ->get()
                ->mapWithKeys(fn($b) => [$b->id => $b->user->name ?? '?'])
                ->all();
        }

        if ($user->hasRole('Encargados')) {
            $jefe = $user->jefeDeArea;
            if (! $jefe) return [];
            return $jefe->becariosPermitidosManual()
                ->mapWithKeys(fn($b) => [$b->id => $b->user->name ?? '?'])
                ->all();
        }

        return [];
    }
}