<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Filtros</x-slot>
        {{ $this->filtersForm }}
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>