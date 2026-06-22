<?php

namespace App\Filament\Becario\Pages;

use App\Models\AsignacionBeca;
use App\Models\RegistroAsistencia;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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
            ->visible(fn() => ! $this->shouldEnroll && $this->asignacion && ! $this->jornadaAbierta)
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
            ->visible(fn() => ! $this->shouldEnroll && $this->jornadaAbierta !== null)
            ->extraAttributes([
                'x-on:click.prevent' => "\$dispatch('open-verificacion', { tipo: 'salida' })",
            ]);
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
