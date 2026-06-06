<x-filament-panels::page>
    @if (! $asignacion)
        <x-filament::section>
            <p>No tienes una asignación de beca activa. Contacta al Encargado General.</p>
        </x-filament::section>
    @elseif ($shouldEnroll)
        <x-filament::section>
            <div class="space-y-2">
                <p class="text-warning-500 font-semibold">
                    Antes de marcar asistencia debes registrar tu rostro.
                </p>
                <p class="text-sm text-gray-400">
                    Se abrirá un asistente para capturar tu rostro siguiendo 5 poses.
                </p>
            </div>
        </x-filament::section>

        @include('filament.becario.enrolamiento-modal', ['shouldEnroll' => true])
    @else
        <x-filament::section>
            <div class="space-y-2">
                <p><strong>Beca:</strong> {{ $asignacion->beca->nombre ?? '—' }}</p>
                <p><strong>Área:</strong> {{ $asignacion->area->nombre ?? '—' }}</p>
                @if ($jornadaAbierta)
                    <p class="text-warning-600">
                        Jornada abierta desde {{ \Carbon\Carbon::parse($jornadaAbierta->hora_entrada)->format('H:i') }}
                    </p>
                @else
                    <p>Sin jornada activa.</p>
                @endif
            </div>

            <div class="mt-4 flex gap-2">
                {{ $this->marcarEntradaAction }}
                {{ $this->marcarSalidaAction }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Jornadas de hoy</x-slot>
            @if ($jornadasHoy->isEmpty())
                <p>Aún no hay marcas hoy.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left">
                            <th>Entrada</th><th>Salida</th><th>Horas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jornadasHoy as $j)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($j->hora_entrada)->format('H:i') }}</td>
                                <td>{{ $j->hora_salida ? \Carbon\Carbon::parse($j->hora_salida)->format('H:i') : '—' }}</td>
                                <td>{{ $j->total_horas ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::section>
    @endif
</x-filament-panels::page>