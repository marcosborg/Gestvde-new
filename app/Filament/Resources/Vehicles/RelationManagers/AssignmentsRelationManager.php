<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Filament\Resources\DriverVehicleAssignments\Tables\DriverVehicleAssignmentsTable;
use App\Models\DriverVehicleAssignment;
use App\Services\DriverVehicleAssignmentService;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $title = 'Atribuicoes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Atribuicao')
                    ->schema([
                        Grid::make(2)->schema([
                            Hidden::make('vehicle_id')
                                ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->id)
                                ->dehydrated(true),
                            Select::make('driver_id')
                                ->label('Motorista')
                                ->relationship('driver', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DatePicker::make('start_date')
                                ->label('Data de inicio')
                                ->required()
                                ->rule($this->overlapRule()),
                            DatePicker::make('end_date')
                                ->label('Data de fim')
                                ->rule('after_or_equal:start_date')
                                ->rule($this->overlapRule()),
                            TextInput::make('weekly_rate_override')
                                ->label('Valor semanal (override)')
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                            Placeholder::make('weekly_rate_base')
                                ->label('Valor semanal base')
                                ->content(function (Get $get): string {
                                    $weeklyRent = $this->getOwnerRecord()->weekly_rent;

                                    return $weeklyRent !== null ? number_format((float) $weeklyRent, 2) . ' EUR' : '-';
                                }),
                        ]),
                    ]),
                Section::make('Resumo')
                    ->schema([
                        Placeholder::make('current_week_days')
                            ->label('Dias cobrados na semana atual')
                            ->content(function (Get $get): string {
                                $startDate = $get('start_date');

                                if (! $startDate) {
                                    return '-';
                                }

                                $startDate = Carbon::parse($startDate)->startOfDay();
                                $endDate = $get('end_date') ? Carbon::parse($get('end_date'))->startOfDay() : null;
                                $weekStart = now()->startOfWeek(Carbon::MONDAY)->startOfDay();
                                $weekEnd = $weekStart->copy()->addDays(6);

                                $effectiveStart = $startDate->greaterThan($weekStart) ? $startDate : $weekStart;
                                $effectiveEnd = $endDate && $endDate->lessThan($weekEnd) ? $endDate : $weekEnd;

                                if ($effectiveEnd->lt($effectiveStart)) {
                                    return '0';
                                }

                                return (string) ($effectiveStart->diffInDays($effectiveEnd) + 1);
                            }),
                        Textarea::make('note')
                            ->label('Nota')
                            ->rows(3)
                            ->maxLength(2000),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return DriverVehicleAssignmentsTable::configure($table);
    }

    protected function overlapRule(): Closure
    {
        return function (Get $get, ?DriverVehicleAssignment $record): Closure {
            return function (string $attribute, $value, Closure $fail) use ($get, $record): void {
                $vehicleId = $this->getOwnerRecord()->id;
                $driverId = $get('driver_id');
                $startDate = $get('start_date');
                $endDate = $get('end_date');

                if (! $driverId || ! $vehicleId || ! $startDate) {
                    return;
                }

                try {
                    app(DriverVehicleAssignmentService::class)->ensureNoOverlapFor(
                        (int) $driverId,
                        (int) $vehicleId,
                        $startDate,
                        $endDate,
                        $record?->id
                    );
                } catch (ValidationException $exception) {
                    $messages = $exception->errors();
                    $first = collect($messages)->flatten()->first();

                    if ($first) {
                        $fail($first);
                    }
                }
            };
        };
    }
}
