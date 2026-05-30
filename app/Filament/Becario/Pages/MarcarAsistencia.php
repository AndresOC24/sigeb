<?php

namespace App\Filament\Becario\Pages;

use App\Models\AsignacionBeca;
use App\Models\RegistroAsistencia;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class MarcarAsistencia extends Page
{
    protected static \BackedEnum | string | null $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $title = 'Marcar Asistencia';
    protected static ?string $navigationLabel = 'Marcar Asistencia';
    protected string $view = 'filament.becario.pages.marcar-asistencia';

    public ?AsignacionBeca $asignacion = null;
    public ?RegistroAsistencia $jornadaAbierta = null;
    public $jornadasHoy = [];

    public function mount(): void
    {
        $becario = auth()->user()->becario;

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
            ->visible(fn() => $this->asignacion && ! $this->jornadaAbierta)
            ->action(function () {
                RegistroAsistencia::create([
                    'asignacion_beca_id' => $this->asignacion->id,
                    'fecha' => today(),
                    'hora_entrada' => now(),
                    'estado' => 'pendiente',
                    'verificado_facial' => false,
                ]);

                Notification::make()->title('Entrada registrada')->success()->send();
                $this->cargarEstado();
            });
    }

    public function marcarSalidaAction(): Action
    {
        return Action::make('marcarSalida')
            ->label('Marcar Salida')
            ->color('danger')
            ->icon('heroicon-o-arrow-left-on-rectangle')
            ->visible(fn() => $this->jornadaAbierta !== null)
            ->modalHeading('Registrar salida')
            ->modalDescription('Describe la actividad principal que realizaste en esta jornada.')
            ->modalSubmitActionLabel('Confirmar salida')
            ->schema([
                \Filament\Forms\Components\Textarea::make('actividad_principal')
                    ->label('Actividad principal')
                    ->required()
                    ->minLength(10)
                    ->maxLength(2000)
                    ->rows(4)
                    ->placeholder('Ej: Soporte técnico a docentes, instalación de software en laboratorio 3...'),
            ])
            ->action(function (array $data) {
                $entrada = \Carbon\Carbon::parse($this->jornadaAbierta->hora_entrada);
                $salida = now();
                $horas = round($entrada->diffInMinutes($salida) / 60, 2);

                $this->jornadaAbierta->update([
                    'hora_salida' => $salida,
                    'total_horas' => $horas,
                    'actividad_principal' => $data['actividad_principal'],
                ]);

                Notification::make()->title('Salida registrada')->success()->send();
                $this->cargarEstado();
            });
    }
}
