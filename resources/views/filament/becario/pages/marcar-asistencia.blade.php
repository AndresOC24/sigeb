<x-filament-panels::page>
    @if (! $asignacion)
        <x-filament::section>
            <div class="flex items-start gap-3">
                <x-filament::icon
                    icon="heroicon-o-exclamation-triangle"
                    aria-hidden="true"
                    class="mt-0.5 h-5 w-5 shrink-0 text-warning-500"
                />
                <div class="space-y-1">
                    <p class="font-semibold text-gray-950 dark:text-white">
                        No tienes una asignación de beca activa.
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Contacta al Encargado General para regularizar tu situación.
                    </p>
                </div>
            </div>
        </x-filament::section>
    @elseif ($shouldEnroll)
        <x-filament::section>
            <div class="flex items-start gap-3">
                <x-filament::icon
                    icon="heroicon-o-face-smile"
                    aria-hidden="true"
                    class="mt-0.5 h-5 w-5 shrink-0 text-warning-500"
                />
                <div class="space-y-1">
                    <p class="font-semibold text-gray-950 dark:text-white">
                        Antes de marcar asistencia debes registrar tu rostro.
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Se abrirá un asistente para capturar tu rostro siguiendo 5 poses.
                    </p>
                </div>
            </div>
        </x-filament::section>

        @include('filament.becario.enrolamiento-modal', ['shouldEnroll' => true])
    @else
        <x-filament::section>
            <div class="flex flex-col gap-6">
                <div style="display:flex; flex-wrap:wrap; align-items:flex-start; justify-content:space-between; gap:1.5rem;">
                    <dl style="display:flex; flex-wrap:wrap; gap:1.25rem 2.5rem; margin:0;">
                        <div>
                            <dt style="display:flex; align-items:center; gap:0.5rem;">
                                <x-filament::icon
                                    icon="heroicon-o-academic-cap"
                                    aria-hidden="true"
                                    style="height:1.25rem; width:1.25rem; flex-shrink:0;"
                                    class="text-primary-500 dark:text-primary-400"
                                />
                                <span style="font-size:1.0625rem; font-weight:700; line-height:1.2;" class="text-gray-950 dark:text-white">
                                    Beca
                                </span>
                            </dt>
                            <dd style="margin:0.25rem 0 0 1.75rem; font-size:0.875rem;" class="text-gray-500 dark:text-gray-400">
                                {{ $asignacion->beca->nombre ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt style="display:flex; align-items:center; gap:0.5rem;">
                                <x-filament::icon
                                    icon="heroicon-o-building-office-2"
                                    aria-hidden="true"
                                    style="height:1.25rem; width:1.25rem; flex-shrink:0;"
                                    class="text-primary-500 dark:text-primary-400"
                                />
                                <span style="font-size:1.0625rem; font-weight:700; line-height:1.2;" class="text-gray-950 dark:text-white">
                                    Área
                                </span>
                            </dt>
                            <dd style="margin:0.25rem 0 0 1.75rem; font-size:0.875rem;" class="text-gray-500 dark:text-gray-400">
                                {{ $asignacion->area->nombre ?? '—' }}
                            </dd>
                        </div>
                    </dl>

                    @if ($jornadaAbierta)
                        <div style="flex-shrink:0;">
                            <x-filament::badge color="success" size="lg">
                                Jornada abierta · {{ \Carbon\Carbon::parse($jornadaAbierta->hora_entrada)->format('H:i') }}
                            </x-filament::badge>
                        </div>
                    @endif
                </div>

                <div style="border-top-width:1px; padding-top:1.5rem;" class="border-gray-200 dark:border-white/10">
                    <div style="display:flex; flex-wrap:wrap; gap:1rem 1.5rem;">
                        {{ $this->marcarEntradaAction }}
                        {{ $this->marcarSalidaAction }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Jornadas de hoy</x-slot>
            <x-slot name="description">
                {{ $jornadasHoy->count() }} {{ \Illuminate\Support\Str::plural('jornada', $jornadasHoy->count()) }} registrada{{ $jornadasHoy->count() === 1 ? '' : 's' }} hoy
            </x-slot>

            @if ($jornadasHoy->isEmpty())
                <div class="flex flex-col items-center justify-center gap-3 py-10 text-center">
                    <x-filament::icon
                        icon="heroicon-o-clock"
                        aria-hidden="true"
                        class="h-8 w-8 text-gray-400 dark:text-gray-500"
                    />
                    <div class="space-y-1">
                        <p class="font-medium text-gray-950 dark:text-white">
                            Aún no hay marcas hoy
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Tus jornadas aparecerán aquí en cuanto marques tu entrada.
                        </p>
                    </div>
                </div>
            @else
                <ul role="list" style="display:flex; flex-direction:column; gap:0.75rem; margin:0; padding:0; list-style:none;">
                    @foreach ($jornadasHoy as $j)
                        @php
                            $enCurso = $j->hora_salida === null;
                        @endphp
                        <li
                            style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; border-width:1px; border-radius:0.75rem; padding:1rem;"
                            @class([
                                'border-success-300 bg-success-50/60 dark:border-success-500/30 dark:bg-success-500/10' => $enCurso,
                                'border-gray-200 bg-gray-50/60 dark:border-white/10 dark:bg-white/5' => ! $enCurso,
                            ])
                        >
                            <div style="display:flex; align-items:center; gap:1rem; min-width:0;">
                                <span
                                    style="display:flex; align-items:center; justify-content:center; height:2.25rem; width:2.25rem; flex-shrink:0; border-radius:9999px;"
                                    @class([
                                        'bg-success-100 text-success-600 dark:bg-success-500/20 dark:text-success-400' => $enCurso,
                                        'bg-gray-200 text-gray-500 dark:bg-white/10 dark:text-gray-300' => ! $enCurso,
                                    ])
                                >
                                    <x-filament::icon
                                        :icon="$enCurso ? 'heroicon-o-play' : 'heroicon-o-check'"
                                        aria-hidden="true"
                                        style="height:1.125rem; width:1.125rem;"
                                    />
                                </span>

                                <div style="display:flex; align-items:center; gap:1rem;">
                                    <div style="display:flex; flex-direction:column;">
                                        <span style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;" class="text-gray-500 dark:text-gray-400">
                                            Entrada
                                        </span>
                                        <span style="font-size:1rem; font-weight:600; line-height:1.3;" class="text-gray-950 dark:text-white">
                                            {{ \Carbon\Carbon::parse($j->hora_entrada)->format('H:i') }}
                                        </span>
                                    </div>

                                    <x-filament::icon
                                        icon="heroicon-o-arrow-long-right"
                                        aria-hidden="true"
                                        style="height:1.125rem; width:1.125rem; flex-shrink:0;"
                                        class="text-gray-400 dark:text-gray-500"
                                    />

                                    <div style="display:flex; flex-direction:column;">
                                        <span style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;" class="text-gray-500 dark:text-gray-400">
                                            Salida
                                        </span>
                                        <span style="font-size:1rem; font-weight:600; line-height:1.3;" class="text-gray-950 dark:text-white">
                                            {{ $j->hora_salida ? \Carbon\Carbon::parse($j->hora_salida)->format('H:i') : '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div style="display:flex; align-items:center; gap:1.25rem;">
                                <div style="display:flex; flex-direction:column; text-align:right;">
                                    <span style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;" class="text-gray-500 dark:text-gray-400">
                                        Horas
                                    </span>
                                    <span style="font-size:1rem; font-weight:600; line-height:1.3;" class="text-gray-950 dark:text-white">
                                        {{ $j->total_horas ?? '—' }}
                                    </span>
                                </div>

                                @if ($enCurso)
                                    <x-filament::badge color="success">En curso</x-filament::badge>
                                @else
                                    <x-filament::badge color="info">Completada</x-filament::badge>
                                @endif
                            </div>

                            @unless ($enCurso)
                                <div
                                    style="width:100%; display:flex; flex-wrap:wrap; align-items:center; gap:0.75rem; border-top-width:1px; padding-top:0.75rem; margin-top:0.25rem;"
                                    class="border-gray-200 dark:border-white/10"
                                >
                                    @if (in_array($j->estado, ['pendiente', 'rechazado']))
                                        {{ ($this->subirEvidenciaAction)(['registro' => $j->id]) }}
                                    @endif

                                    @if (filled($j->evidencia))
                                        {{ ($this->descargarEvidenciaAction)(['registro' => $j->id]) }}
                                        <span
                                            style="display:inline-flex; align-items:center; gap:0.35rem; font-size:0.8rem;"
                                            class="text-success-600 dark:text-success-400"
                                        >
                                            <x-filament::icon
                                                icon="heroicon-o-check-circle"
                                                aria-hidden="true"
                                                style="height:1rem; width:1rem;"
                                            />
                                            Evidencia subida{{ $j->evidencia_subida_en ? ' · ' . \Carbon\Carbon::parse($j->evidencia_subida_en)->format('d/m H:i') : '' }}
                                        </span>
                                    @else
                                        <span style="font-size:0.8rem;" class="text-gray-500 dark:text-gray-400">
                                            Aún no has subido la evidencia de esta jornada.
                                        </span>
                                    @endif
                                </div>
                            @endunless
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-filament::section>
    @endif
    @unless ($shouldEnroll)
        @include('filament.becario.verificacion-modal')
    @endunless
</x-filament-panels::page>
