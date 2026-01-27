<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Models\Vehicle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.vehicle_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('plate')
                                ->label(__('admin.labels.license_plate'))
                                ->required()
                                ->maxLength(20)
                                ->unique(ignoreRecord: true),
                            TextInput::make('brand')
                                ->label(__('admin.labels.brand'))
                                ->required()
                                ->maxLength(50),
                            TextInput::make('model')
                                ->label(__('admin.labels.model'))
                                ->required()
                                ->maxLength(50),
                            TextInput::make('year')
                                ->label(__('admin.labels.year'))
                                ->numeric()
                                ->required()
                                ->minValue(1900)
                                ->maxValue(now()->year + 1),
                            TextInput::make('color')
                                ->label(__('admin.labels.color'))
                                ->maxLength(50),
                            TextInput::make('seats_count')
                                ->label(__('admin.labels.seats_number'))
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(99),
                            Select::make('fuel_type')
                                ->label(__('admin.labels.fuel'))
                                ->multiple()
                                ->required()
                                ->options([
                                    'petrol' => __('admin.fuel_types.petrol'),
                                    'diesel' => __('admin.fuel_types.diesel'),
                                    'lpg' => __('admin.fuel_types.lpg'),
                                    'electric' => __('admin.fuel_types.electric'),
                                    'hybrid' => __('admin.fuel_types.hybrid'),
                                ])
                                ->preload(),
                            TextInput::make('motorization_type')
                                ->label(__('admin.labels.engine_type'))
                                ->maxLength(80),
                            TextInput::make('tire_type')
                                ->label(__('admin.labels.tire_type'))
                                ->maxLength(80),
                            TextInput::make('chassis_number')
                                ->label(__('admin.labels.chassis_number'))
                                ->maxLength(80),
                            TextInput::make('gps_id')
                                ->label(__('admin.labels.gps_id'))
                                ->maxLength(80),
                            TextInput::make('mileage')
                                ->label(__('admin.labels.mileage'))
                                ->numeric()
                                ->required()
                                ->minValue(0),
                        ]),
                    ]),
                Section::make(__('admin.sections.registration_inspections'))
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('registration_date')
                                ->label(__('admin.labels.registration_date')),
                            Placeholder::make('next_inspection_due')
                                ->label(__('admin.labels.next_inspection'))
                                ->content(function (Get $get): string {
                                    $nextInspection = Vehicle::calculateNextInspectionDate($get('registration_date'));

                                    return $nextInspection ? $nextInspection->format('d/m/Y') : '-';
                                }),
                        ]),
                    ]),
                Section::make(__('admin.sections.leasing'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('acquisition_type')
                                ->label(__('admin.labels.acquisition_type'))
                                ->required()
                                ->options([
                                    'leasing' => __('admin.acquisition_types.leasing'),
                                ])
                                ->default('leasing')
                                ->disabled()
                                ->dehydrated(),
                            TextInput::make('leasing_contract_number')
                                ->label(__('admin.labels.leasing_contract_number'))
                                ->maxLength(120),
                            TextInput::make('leasing_entry_amount')
                                ->label(__('admin.labels.down_payment'))
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                            TextInput::make('leasing_initial_installment')
                                ->label(__('admin.labels.initial_installment'))
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                            TextInput::make('leasing_monthly_installment')
                                ->label(__('admin.labels.monthly_installment'))
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                            TextInput::make('acquisition_value')
                                ->label(__('admin.labels.acquisition_value'))
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                            DatePicker::make('acquisition_date')
                                ->label(__('admin.labels.acquisition_date')),
                            TextInput::make('annual_depreciation_percent')
                                ->label(__('admin.labels.annual_depreciation'))
                                ->numeric()
                                ->suffix('%')
                                ->minValue(0)
                                ->maxValue(100),
                            TextInput::make('weekly_rent')
                                ->label(__('admin.labels.weekly_rent'))
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                        ]),
                    ]),
                Section::make(__('admin.sections.documents_fleet'))
                    ->schema([
                        FileUpload::make('vehicle_documents')
                            ->label(__('admin.labels.vehicle_documents'))
                            ->multiple()
                            ->reorderable()
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            TextInput::make('dua_number')
                                ->label(__('admin.labels.car_registration'))
                                ->maxLength(120),
                            TextInput::make('fleet_insurance_policy_number')
                                ->label(__('admin.labels.fleet_insurance_policy_number'))
                                ->maxLength(120),
                            TextInput::make('via_verde_id')
                                ->label(__('admin.labels.via_verde_id'))
                                ->maxLength(120),
                            TextInput::make('fleet_card_number')
                                ->label(__('admin.labels.fleet_card_number'))
                                ->maxLength(120),
                            TextInput::make('fleet_card_code')
                                ->label(__('admin.labels.fleet_card_code'))
                                ->maxLength(120),
                        ]),
                    ]),
                Section::make(__('admin.sections.status'))
                    ->schema([
                        Select::make('status')
                            ->label(__('admin.labels.status'))
                            ->required()
                            ->options([
                                'rented' => __('admin.vehicle_status.rented'),
                                'available' => __('admin.vehicle_status.available'),
                                'missing_docs' => __('admin.vehicle_status.missing_docs'),
                                'maintenance' => __('admin.vehicle_status.maintenance'),
                                'in_fleet' => __('admin.vehicle_status.in_fleet'),
                            ])
                            ->default('available'),
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
