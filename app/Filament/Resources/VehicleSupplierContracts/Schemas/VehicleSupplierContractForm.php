<?php

namespace App\Filament\Resources\VehicleSupplierContracts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleSupplierContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.contract_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('category')
                                ->label(__('admin.labels.category'))
                                ->options([
                                    'fleet' => __('admin.contract_categories.fleet'),
                                    'operations' => __('admin.contract_categories.operations'),
                                    'administration' => __('admin.contract_categories.administration'),
                                ])
                                ->required()
                                ->live(),
                            Select::make('vehicle_id')
                                ->label(__('admin.labels.vehicle'))
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->required(fn (Get $get): bool => $get('category') === 'fleet')
                                ->rules(['required_if:category,fleet']),
                            Select::make('supplier_id')
                                ->label(__('admin.labels.supplier'))
                                ->relationship('supplier', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DatePicker::make('start_date')
                                ->label(__('admin.labels.start_date'))
                                ->required(),
                            DatePicker::make('end_date')
                                ->label(__('admin.labels.end_date')),
                            TextInput::make('monthly_cost')
                                ->label(__('admin.labels.monthly_cost'))
                                ->numeric()
                                ->required()
                                ->prefix('EUR')
                                ->minValue(0),
                            Toggle::make('recurring')
                                ->label(__('admin.labels.recurring'))
                                ->default(false)
                                ->live(),
                            Select::make('recurrence_interval')
                                ->label(__('admin.labels.interval'))
                                ->options([
                                    'monthly' => __('admin.recurrence_intervals.monthly'),
                                    'quarterly' => __('admin.recurrence_intervals.quarterly'),
                                    'yearly' => __('admin.recurrence_intervals.yearly'),
                                ])
                                ->required(fn (Get $get): bool => (bool) $get('recurring'))
                                ->rules(['required_if:recurring,1'])
                                ->visible(fn (Get $get): bool => (bool) $get('recurring')),
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
