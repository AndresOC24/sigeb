<?php

namespace App\Filament\Widgets;

use App\Models\RegistroAsistencia;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class JornadasPendientesLista extends TableWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = ['md' => 5];

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['Super Administrador', 'Encargado General']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Jornadas pendientes')
            ->description('Últimas jornadas sin aprobar')
            ->query(
                RegistroAsistencia::query()
                    ->where('estado', 'pendiente')
                    ->latest('hora_entrada')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('asignacionBeca.becario.user.name')
                    ->label('Becario')
                    ->icon('heroicon-m-clock')
                    ->iconColor('warning'),
                TextColumn::make('fecha')
                    ->date('d/m/Y'),
                TextColumn::make('hora_entrada')
                    ->time('H:i')
                    ->label('Entrada')
                    ->badge()
                    ->color('warning'),
            ])
            ->striped()
            ->paginated(false);
    }
}