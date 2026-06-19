<?php

namespace App\Filament\Resources\RegistroAsistencias\Pages;

use App\Filament\Resources\RegistroAsistencias\RegistroAsistenciaResource;
use App\Models\Becario;
use App\Models\PlantillaEvidencia;
use App\Models\RegistroAsistencia;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ListRegistroAsistencias extends ListRecords
{
    protected static string $resource = RegistroAsistenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('gestionarFormato')
                ->label('Formato de evidencia')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->visible(fn() => Auth::user()->hasRole(['Super Administrador', 'Encargado General']))
                ->modalHeading('Formato de evidencia para becarios')
                ->modalDescription('Sube el documento (PDF o Word) que los becarios descargarán como plantilla. El más reciente reemplaza al anterior como formato vigente.')
                ->modalSubmitActionLabel('Publicar formato')
                ->schema([
                    FileUpload::make('archivo')
                        ->label('Documento del formato')
                        ->disk('local')
                        ->directory('plantillas-evidencia')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ])
                        ->maxSize(10240)
                        ->storeFileNamesIn('archivo_nombre')
                        ->required(),
                ])
                ->action(function (array $data) {
                    PlantillaEvidencia::create([
                        'path' => $data['archivo'],
                        'nombre' => $data['archivo_nombre'] ?? basename($data['archivo']),
                        'subido_por' => Auth::id(),
                    ]);
                    Notification::make()->title('Formato publicado')->success()->send();
                }),
            Action::make('descargarFormato')
                ->label('Ver formato actual')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->visible(fn() => Auth::user()->hasRole(['Super Administrador', 'Encargado General']) && PlantillaEvidencia::vigente() !== null)
                ->action(function () {
                    $plantilla = PlantillaEvidencia::vigente();
                    if (! $plantilla || ! Storage::disk('local')->exists($plantilla->path)) {
                        Notification::make()->title('El archivo ya no está disponible')->warning()->send();
                        return;
                    }
                    return Storage::disk('local')->download($plantilla->path, $plantilla->nombre);
                }),
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