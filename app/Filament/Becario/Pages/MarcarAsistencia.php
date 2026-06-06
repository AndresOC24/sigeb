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
}
