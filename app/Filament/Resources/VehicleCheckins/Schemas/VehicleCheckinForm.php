<?php

namespace App\Filament\Resources\VehicleCheckins\Schemas;

use App\Enums\CleanlinessLevel;
use App\Enums\ConditionLevel;
use App\Enums\FuelLevel;
use App\Enums\VehicleCheckinPhotoSection;
use App\Enums\VehicleCheckinType;
use App\Enums\VehicleDamageArea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class VehicleCheckinForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Wizard::make([
                    Step::make(__('admin.sections.checkin_details'))
                        ->schema([
                            Grid::make(['default' => 1, 'md' => 2])->schema([
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
                            Select::make('check_type')
                                ->label(__('admin.labels.check_type'))
                                ->options(function (Get $get): array {
                                    $options = VehicleCheckinType::formOptions();

                                    if ($get('check_type') === VehicleCheckinType::Inspection->value) {
                                        $options[VehicleCheckinType::Inspection->value] = VehicleCheckinType::Inspection->label();
                                    }

                                    return $options;
                                })
                                ->helperText(__('admin.help_texts.checkin_type'))
                                ->required(),
                                DateTimePicker::make('occurred_at')
                                    ->label(__('admin.labels.occurred_at'))
                                    ->default(now())
                                    ->required(),
                            ]),
                        ]),
                    Step::make(__('admin.sections.conditions'))
                        ->schema([
                            Grid::make(['default' => 1, 'md' => 2])->schema([
                                Select::make('exterior_condition')
                                    ->label(__('admin.labels.exterior_condition'))
                                    ->options(ConditionLevel::options())
                                    ->required(),
                                Select::make('interior_condition')
                                    ->label(__('admin.labels.interior_condition'))
                                    ->options(ConditionLevel::options())
                                    ->required(),
                                Select::make('tires_condition')
                                    ->label(__('admin.labels.tires_condition'))
                                    ->options(ConditionLevel::options())
                                    ->required(),
                                Select::make('cleanliness')
                                    ->label(__('admin.labels.cleanliness'))
                                    ->options(CleanlinessLevel::options())
                                    ->required(),
                                Select::make('fuel_level')
                                    ->label(__('admin.labels.fuel_level'))
                                    ->options(FuelLevel::options())
                                    ->required(),
                            ]),
                        ]),
                    Step::make(__('admin.sections.exterior_photos'))
                        ->schema([
                            Repeater::make('exterior_photos')
                                ->relationship('exteriorPhotos')
                                ->itemLabel(fn (array $state): ?string => $state['position'] ?? null)
                                ->collapsible()
                                ->schema([
                                    Hidden::make('section')
                                        ->default(VehicleCheckinPhotoSection::Exterior->value),
                                    FileUpload::make('path')
                                        ->label(__('admin.labels.image'))
                                        ->image()
                                        ->disk('public')
                                        ->directory('vehicle-checkins')
                                        ->columnSpanFull(),
                                    Select::make('position')
                                        ->label(__('admin.labels.position'))
                                        ->options([
                                            'front' => __('admin.checkin_positions.front'),
                                            'back' => __('admin.checkin_positions.back'),
                                            'left' => __('admin.checkin_positions.left'),
                                            'right' => __('admin.checkin_positions.right'),
                                        ]),
                                    Textarea::make('notes')
                                        ->label(__('admin.labels.notes'))
                                        ->rows(2)
                                        ->maxLength(500),
                                ])
                                ->columns(1)
                                ->columnSpanFull(),
                        ]),
                    Step::make(__('admin.sections.interior_photos'))
                        ->schema([
                            Repeater::make('interior_photos')
                                ->relationship('interiorPhotos')
                                ->collapsible()
                                ->schema([
                                    Hidden::make('section')
                                        ->default(VehicleCheckinPhotoSection::Interior->value),
                                    FileUpload::make('path')
                                        ->label(__('admin.labels.image'))
                                        ->image()
                                        ->disk('public')
                                        ->directory('vehicle-checkins')
                                        ->columnSpanFull(),
                                    Select::make('position')
                                        ->label(__('admin.labels.position'))
                                        ->options([
                                            'interior' => __('admin.checkin_positions.interior'),
                                        ])
                                        ->default('interior'),
                                    Textarea::make('notes')
                                        ->label(__('admin.labels.notes'))
                                        ->rows(2)
                                        ->maxLength(500),
                                ])
                                ->columns(1)
                                ->columnSpanFull(),
                        ]),
                    Step::make(__('admin.sections.damages'))
                        ->schema([
                            Repeater::make('damages')
                                ->relationship()
                                ->helperText(__('admin.help_texts.checkin_damages'))
                                ->itemLabel(fn (array $state): ?string => $state['zone'] ?? null)
                                ->collapsible()
                                ->schema([
                                    Select::make('zone')
                                        ->label(__('admin.labels.zone'))
                                        ->required()
                                        ->options(VehicleDamageArea::options()),
                                    Textarea::make('description')
                                        ->label(__('admin.labels.description'))
                                        ->rows(2)
                                        ->maxLength(500)
                                        ->helperText(__('admin.help_texts.damage_description'))
                                        ->columnSpanFull(),
                                    Repeater::make('photos')
                                        ->relationship()
                                        ->schema([
                                            FileUpload::make('path')
                                                ->label(__('admin.labels.image'))
                                                ->image()
                                                ->disk('public')
                                                ->directory('vehicle-damages')
                                                ->required()
                                                ->columnSpanFull(),
                                            Textarea::make('notes')
                                                ->label(__('admin.labels.notes'))
                                                ->rows(2)
                                                ->maxLength(500),
                                        ])
                                        ->columns(1)
                                        ->columnSpanFull(),
                                ])
                                ->columns(1)
                                ->columnSpanFull(),
                        ]),
                    Step::make(__('admin.sections.notes'))
                        ->schema([
                            Textarea::make('notes')
                                ->label(__('admin.labels.notes'))
                                ->rows(3)
                                ->maxLength(2000),
                        ]),
                ])
                    ->columnSpanFull(),
            ]);
    }
}
