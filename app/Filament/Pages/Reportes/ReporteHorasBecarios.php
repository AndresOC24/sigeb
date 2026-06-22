<?php

namespace App\Filament\Pages\Reportes;

use App\Filament\Exports\HorasBecariosExporter;
use App\Models\Area;
use App\Models\AsignacionBeca;
use App\Models\Beca;
use App\Models\Gestion;
use App\Models\JefeDeArea;
use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReporteHorasBecarios extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Horas Acumuladas';
    protected static ?string $title = 'Horas Acumuladas por Becario';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected string $view = 'filament.pages.reportes.reporte-base';

    public ?array $filters = [
        'gestion_id' => null,
        'area_id' => null,
        'beca_id' => null,
        'estado' => null,
    ];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->hasAnyRole(['Super Administrador', 'Encargado General', 'Encargados']) ?? false;
    }

    public function mount(): void
    {
        $this->filtersForm->fill($this->filters);
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('gestion_id')
                    ->label('Gestión')
                    ->options(Gestion::orderByDesc('fecha_inicio')->pluck('nombre', 'id'))
                    ->placeholder('Todas')
                    ->live(),
                Select::make('area_id')
                    ->label('Área')
                    ->options(Area::orderBy('nombre')->pluck('nombre', 'id'))
                    ->placeholder('Todas')
                    ->hidden(fn () => auth()->user()->hasRole('Encargados'))
                    ->live(),
                Select::make('beca_id')
                    ->label('Beca')
                    ->options(Beca::orderBy('nombre')->pluck('nombre', 'id'))
                    ->placeholder('Todas')
                    ->live(),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'activa' => 'Activa',
                        'suspendida' => 'Suspendida',
                        'finalizada' => 'Finalizada',
                    ])
                    ->placeholder('Todos')
                    ->live(),
            ])
            ->statePath('filters')
            ->columns(4);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->getReporteQuery())
            ->columns([
                TextColumn::make('becario.user.name')->label('Becario')->searchable()->sortable(),
                TextColumn::make('becario.codigo_estudiante')->label('Código')->searchable(),
                TextColumn::make('becario.carrera.nombre')->label('Carrera')->toggleable(),
                TextColumn::make('area.nombre')->label('Área')->sortable(),
                TextColumn::make('beca.nombre')->label('Beca'),
                TextColumn::make('gestion.nombre')->label('Gestión'),
                TextColumn::make('horas_acumuladas')->label('Acum.')->alignCenter()->sortable(),
                TextColumn::make('beca.horas_requeridas')->label('Req.')->alignCenter(),
                TextColumn::make('progreso')
                    ->label('% Progreso')
                    ->alignCenter()
                    ->state(function (AsignacionBeca $record): string {
                        $req = $record->beca?->horas_requeridas;
                        if (! $req) {
                            return '—';
                        }
                        return round(($record->horas_acumuladas / $req) * 100, 1) . '%';
                    }),
                TextColumn::make('estado')->badge()->colors([
                    'success' => 'activa',
                    'warning' => 'suspendida',
                    'gray' => 'finalizada',
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar')
                    ->exporter(HorasBecariosExporter::class)
                    ->modifyQueryUsing(fn (Builder $query) => $this->getReporteQuery()),
            ])
            ->defaultSort('horas_acumuladas', 'desc')
            ->striped();
    }

    protected function getReporteQuery(): Builder
    {
        $query = AsignacionBeca::query()
            ->with(['becario.user', 'becario.carrera', 'beca', 'gestion', 'area', 'jefeArea.user']);

        $user = auth()->user();
        if ($user->hasRole('Encargados') && ! $user->hasRole('Encargado General')) {
            $jefeArea = JefeDeArea::where('user_id', $user->id)->first();
            $query->where('jefe_area_id', $jefeArea?->id ?? 0);
        }

        $f = $this->filters;
        return $query
            ->when($f['gestion_id'] ?? null, fn ($q, $v) => $q->where('gestion_id', $v))
            ->when($f['area_id'] ?? null, fn ($q, $v) => $q->where('area_id', $v))
            ->when($f['beca_id'] ?? null, fn ($q, $v) => $q->where('beca_id', $v))
            ->when($f['estado'] ?? null, fn ($q, $v) => $q->where('estado', $v));
    }
}