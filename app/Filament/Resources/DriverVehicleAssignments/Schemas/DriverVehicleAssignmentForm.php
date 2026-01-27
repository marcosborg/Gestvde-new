<?php

namespace App\Filament\Resources\DriverVehicleAssignments\Schemas;

use App\Models\Vehicle;
use App\Support\DriverVehicleAssignmentRules;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class DriverVehicleAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.assignment'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('driver_id')
                                ->label(__('admin.labels.driver'))
                                ->relationship('driver', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('vehicle_id')
                                ->label(__('admin.labels.vehicle'))
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DatePicker::make('start_date')
                                ->label(__('admin.labels.start_date'))
                                ->required()
                                ->rule(self::overlapRule()),
                            DatePicker::make('end_date')
                                ->label(__('admin.labels.end_date'))
                                ->rule('after_or_equal:start_date')
                                ->rule(self::overlapRule()),
                            TextInput::make('weekly_rate_override')
                                ->label(__('admin.labels.weekly_value_override'))
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                            Placeholder::make('weekly_rate_base')
                                ->label(__('admin.labels.weekly_value_base'))
                                ->content(function (Get $get): string {
                                    $vehicleId = $get('vehicle_id');

                                    if (! $vehicleId) {
                                        return '-';
                                    }

                                    $weeklyRent = Vehicle::query()->whereKey($vehicleId)->value('weekly_rent');

                                    return $weeklyRent !== null ? number_format((float) $weeklyRent, 2).' EUR' : '-';
                                }),
                        ]),
                    ]),
                Section::make(__('admin.sections.summary'))
                    ->schema([
                        Placeholder::make('current_week_days')
                            ->label(__('admin.labels.billed_days_current_week'))
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
                            ->label(__('admin.labels.note'))
                            ->rows(3)
                            ->maxLength(2000),
                    ]),
            ]);
    }

    protected static function overlapRule(): Closure
    {
        return DriverVehicleAssignmentRules::overlapRule(
            fn (Get $get) => $get('driver_id'),
            fn (Get $get) => $get('vehicle_id')
        );
    }
}
