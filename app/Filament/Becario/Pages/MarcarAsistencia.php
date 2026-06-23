<?php

namespace App\Filament\Becario\Pages;

use App\Models\AsignacionBeca;
use App\Models\PermisoMarcadoManual;
use App\Models\RegistroAsistencia;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;

class MarcarAsistencia extends Page
{
    protected static \BackedEnum | string | null $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $title = 'Marcar Asistencia';
    protected static ?string $navigationLabel = 'Marcar Asistencia';
    protected string $view = 'filament.becario.pages.marcar-asistencia';

    public ?AsignacionBeca $asignacion = null;
    public ?RegistroAsistencia $jornadaAbierta = null;
    public $jornadasHoy = [];
    public bool $shouldEnroll = false;
    public ?PermisoMarcadoManual $permisoManual = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->shouldEnroll = $user->rostro === null;

        $becario = $user->becario;

        if (! $becario) {
            return; // sin perfil de becario
        }

        $this->asignacion = $becario->asignaciones()
            ->where('estado', 'activa')
            ->first();

        // Permiso vigente que exonera de la verificación facial (cámara averiada, etc.).
        $this->permisoManual = $becario->permisoMarcadoManualVigente();

        $this->cargarEstado();
    }

    protected function cargarEstado(): void
    {
        if (! $this->asignacion) return;

        $this->jornadaAbierta = RegistroAsistencia::query()
            ->where('asignacion_beca_id', $this->asignacion->id)
            ->where('fecha', today())
            ->whereNull('hora_salida')
            ->first();

        $this->jornadasHoy = RegistroAsistencia::query()
            ->where('asignacion_beca_id', $this->asignacion->id)
            ->where('fecha', today())
            ->orderBy('hora_entrada')
            ->get();
    }

    public function marcarEntradaAction(): Action
    {
        return Action::make('marcarEntrada')
            ->label('Marcar Entrada')
            ->color('success')
            ->icon('heroicon-o-arrow-right-on-rectangle')
            ->visible(fn() => ! $this->permisoManual && ! $this->shouldEnroll && $this->asignacion && ! $this->jornadaAbierta)
            ->extraAttributes([
                'x-on:click.prevent' => "\$dispatch('open-verificacion', { tipo: 'entrada' })",
            ]);
    }

    public function marcarSalidaAction(): Action
    {
        return Action::make('marcarSalida')
            ->label('Marcar Salida')
            ->color('danger')
            ->icon('heroicon-o-arrow-left-on-rectangle')
            ->visible(fn() => ! $this->permisoManual && ! $this->shouldEnroll && $this->jornadaAbierta !== null)
            ->extraAttributes([
                'x-on:click.prevent' => "\$dispatch('open-verificacion', { tipo: 'salida' })",
            ]);
    }

    /**
     * Marca la entrada sin verificación facial, amparado por un permiso vigente.
     */
    public function marcarEntradaManualAction(): Action
    {
        return Action::make('marcarEntradaManual')
            ->label('Marcar Entrada (sin verificación facial)')
            ->color('success')
            ->icon('heroicon-o-arrow-right-on-rectangle')
            ->visible(fn() => $this->permisoManual && $this->asignacion && ! $this->jornadaAbierta)
            ->requiresConfirmation()
            ->modalHeading('Marcar entrada sin verificación facial')
            ->modalDescription('Se registrará tu entrada amparada en el permiso vigente. Quedará pendiente de revisión.')
            ->action(function () {
                if (! $this->permisoManual || ! $this->asignacion || $this->jornadaAbierta) {
                    Notification::make()->title('No puedes marcar entrada en este momento')->warning()->send();
                    return;
                }

                RegistroAsistencia::create([
                    'asignacion_beca_id' => $this->asignacion->id,
                    'fecha' => today(),
                    'hora_entrada' => now(),
                    'estado' => 'pendiente',
                    'verificado_facial' => false,
                    'confidence_score' => null,
                    'permiso_marcado_manual_id' => $this->permisoManual->id,
                ]);

                Notification::make()->title('Entrada registrada (sin verificación facial)')->success()->send();
                $this->cargarEstado();
            });
    }

    /**
     * Marca la salida sin verificación facial, pidiendo la actividad principal.
     */
    public function marcarSalidaManualAction(): Action
    {
        return Action::make('marcarSalidaManual')
            ->label('Marcar Salida (sin verificación facial)')
            ->color('danger')
            ->icon('heroicon-o-arrow-left-on-rectangle')
            ->visible(fn() => $this->permisoManual && $this->jornadaAbierta !== null)
            ->modalHeading('Marcar salida sin verificación facial')
            ->modalSubmitActionLabel('Registrar salida')
            ->schema([
                Textarea::make('actividad_principal')
                    ->label('Actividad principal')
                    ->required()->minLength(10)->maxLength(2000)->rows(3)
                    ->placeholder('Ej: Soporte técnico a docentes, instalación de software en laboratorio 3...'),
            ])
            ->action(function (array $data) {
                if (! $this->permisoManual || ! $this->jornadaAbierta) {
                    Notification::make()->title('No tienes una jornada abierta')->warning()->send();
                    return;
                }

                $entrada = Carbon::parse($this->jornadaAbierta->hora_entrada);
                $salida = now();

                $this->jornadaAbierta->update([
                    'hora_salida' => $salida,
                    'total_horas' => round($entrada->diffInMinutes($salida) / 60, 2),
                    'actividad_principal' => $data['actividad_principal'],
                    'verificado_facial' => false,
                    'confidence_score' => null,
                    'permiso_marcado_manual_id' => $this->permisoManual->id,
                ]);

                Notification::make()->title('Salida registrada (sin verificación facial)')->success()->send();
                $this->cargarEstado();
            });
    }

    /**
     * Resuelve el registro recibido por argumento garantizando que pertenece
     * a la asignación del becario autenticado.
     */
    protected function resolverRegistro(array $arguments): ?RegistroAsistencia
    {
        if (! $this->asignacion) {
            return null;
        }

        return RegistroAsistencia::query()
            ->where('id', $arguments['registro'] ?? null)
            ->where('asignacion_beca_id', $this->asignacion->id)
            ->first();
    }

    public function subirEvidenciaAction(): Action
    {
        return Action::make('subirEvidencia')
            ->label(fn(array $arguments) => $this->resolverRegistro($arguments)?->evidencia
                ? 'Reemplazar evidencia'
                : 'Subir evidencia')
            ->icon('heroicon-o-paper-clip')
            ->color(fn(array $arguments) => $this->resolverRegistro($arguments)?->evidencia ? 'gray' : 'primary')
            ->size('sm')
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
            ->action(function (array $arguments, array $data) {
                $registro = $this->resolverRegistro($arguments);

                if (! $registro || $registro->hora_salida === null || ! in_array($registro->estado, ['pendiente', 'rechazado'])) {
                    Notification::make()->title('No puedes subir evidencia para esta jornada')->warning()->send();
                    return;
                }

                // Reemplazo: elimina el archivo anterior para no dejar huérfanos.
                if ($registro->evidencia && Storage::disk('local')->exists($registro->evidencia)) {
                    Storage::disk('local')->delete($registro->evidencia);
                }

                $registro->update([
                    'evidencia' => $data['evidencia'],
                    'evidencia_nombre' => $data['evidencia_nombre'] ?? basename($data['evidencia']),
                    'evidencia_subida_en' => now(),
                ]);

                Notification::make()->title('Evidencia subida correctamente')->success()->send();

                $this->cargarEstado();
            });
    }

    public function descargarEvidenciaAction(): Action
    {
        return Action::make('descargarEvidencia')
            ->label('Descargar evidencia')
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            ->size('sm')
            ->action(function (array $arguments) {
                $registro = $this->resolverRegistro($arguments);

                if (! $registro || ! $registro->evidencia || ! Storage::disk('local')->exists($registro->evidencia)) {
                    Notification::make()->title('El archivo ya no está disponible')->warning()->send();
                    return;
                }

                return Storage::disk('local')->download($registro->evidencia, $registro->evidencia_nombre);
            });
    }
}
