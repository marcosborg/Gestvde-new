<?php

namespace App\Filament\Resources\Maintenances\Schemas;

use App\Enums\MaintenanceType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class MaintenanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.maintenance_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('vehicle_id')
                                ->label(__('admin.labels.vehicle'))
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('type')
                                ->label(__('admin.labels.type'))
                                ->required()
                                ->options(MaintenanceType::options())
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?string $state): void {
                                    if (in_array($state, ['corrective', MaintenanceType::RepairInteriorDamages->value, MaintenanceType::RepairExteriorDamages->value], true)) {
                                        $set('maintenance_kind', 'corrective');
                                    } elseif ($state === MaintenanceType::Preventive->value) {
                                        $set('maintenance_kind', 'scheduled');
                                    }
                                }),
                            Select::make('maintenance_kind')
                                ->label(__('admin.labels.maintenance_kind'))
                                ->options(function (Get $get): array {
                                    if (in_array($get('type'), ['corrective', MaintenanceType::RepairInteriorDamages->value, MaintenanceType::RepairExteriorDamages->value], true)) {
                                        $options = [
                                            'corrective' => __('admin.maintenance_kinds.corrective'),
                                        ];

                                        $current = $get('maintenance_kind');

                                        if ($current && $current !== 'corrective') {
                                            $options[$current] = __('admin.maintenance_kinds.'.$current);
                                        }

                                        return $options;
                                    }

                                    return [
                                        'periodic' => __('admin.maintenance_kinds.periodic'),
                                        'scheduled' => __('admin.maintenance_kinds.scheduled'),
                                    ];
                                })
                                ->disabled(fn (Get $get): bool => in_array($get('type'), ['corrective', MaintenanceType::RepairInteriorDamages->value, MaintenanceType::RepairExteriorDamages->value], true) && $get('maintenance_kind') === 'corrective')
                                ->default('scheduled')
                                ->helperText(__('admin.help_texts.maintenance_kind'))
                                ->required(),
                            Select::make('status')
                                ->label(__('admin.labels.status'))
                                ->options([
                                    'scheduled' => __('admin.maintenance_status.scheduled'),
                                    'in_progress' => __('admin.maintenance_status.in_progress'),
                                    'completed' => __('admin.maintenance_status.completed'),
                                    'overdue' => __('admin.maintenance_status.overdue'),
                                ])
                                ->default('scheduled')
                                ->helperText(__('admin.help_texts.maintenance_status'))
                                ->required(),
                            TextInput::make('description')
                                ->label(__('admin.labels.description'))
                                ->maxLength(255),
                            Select::make('tasks')
                                ->label(__('admin.labels.maintenance_tasks'))
                                ->multiple()
                                ->relationship('tasks', 'name')
                                ->searchable()
                                ->preload(),
                            TextInput::make('cost')
                                ->label(__('admin.labels.cost'))
                                ->numeric()
                                ->required()
                                ->prefix('EUR')
                                ->minValue(0),
                            DatePicker::make('maintenance_date')
                                ->label(__('admin.labels.maintenance_date'))
                                ->helperText(__('admin.help_texts.maintenance_date'))
                                ->required(),
                            DatePicker::make('next_due_date')
                                ->label(__('admin.labels.next_date'))
                                ->rule('after_or_equal:maintenance_date')
                                ->helperText(__('admin.help_texts.maintenance_next_date')),
                            TextInput::make('next_due_mileage')
                                ->label(__('admin.labels.next_mileage'))
                                ->numeric()
                                ->minValue(0)
                                ->helperText(__('admin.help_texts.maintenance_next_mileage')),
                        ]),
                    ]),
            ]);
    }
}
