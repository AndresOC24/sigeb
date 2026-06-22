<?php

namespace App\Filament\Widgets;

use App\Models\Becario;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class BecariosActivosLista extends TableWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = ['md' => 1];

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['Super Administrador', 'Encargado General']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Becarios activos')
            ->description('Becarios con beca activa')
            ->query(
                Becario::query()
                    ->whereHas('asignaciones', fn ($q) => $q->where('estado', 'activa'))
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nombre')
                    ->icon('heroicon-m-user-circle')
                    ->iconColor('success'),
                TextColumn::make('codigo_estudiante')
                    ->label('Código')
                    ->badge()
                    ->color('success'),
            ])
            ->striped()
            ->paginated(false);
    }
}