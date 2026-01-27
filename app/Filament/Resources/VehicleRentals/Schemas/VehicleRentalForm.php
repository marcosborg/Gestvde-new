<?php

namespace App\Filament\Resources\VehicleRentals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleRentalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.rental_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('vehicle_id')
                                ->label(__('admin.labels.vehicle'))
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('driver_id')
                                ->label(__('admin.labels.driver'))
                                ->relationship('driver', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DatePicker::make('start_date')
                                ->label(__('admin.labels.start_date'))
                                ->required(),
                            DatePicker::make('end_date')
                                ->label(__('admin.labels.end_date')),
                            TextInput::make('weekly_price')
                                ->label(__('admin.labels.weekly_price'))
                                ->numeric()
                                ->required()
                                ->prefix('EUR')
                                ->minValue(0),
                        ]),
                    ]),
                Section::make(__('admin.sections.notes'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('admin.labels.notes'))
                            ->rows(4)
                            ->maxLength(2000),
                    ]),
            ]);
    }
}
