<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.event_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('vehicle_id')
                                ->label(__('admin.labels.vehicle'))
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Select::make('event_type')
                                ->label(__('admin.labels.type'))
                                ->required()
                                ->options([
                                    'inspection' => __('admin.event_types.inspection'),
                                    'insurance' => __('admin.event_types.insurance'),
                                    'maintenance' => __('admin.event_types.maintenance'),
                                    'document' => __('admin.event_types.document'),
                                    'contract' => __('admin.event_types.contract'),
                                    'tax' => __('admin.event_types.tax'),
                                    'other' => __('admin.event_types.other'),
                                ]),
                            TextInput::make('title')
                                ->label(__('admin.labels.title'))
                                ->required()
                                ->maxLength(150),
                            TextInput::make('notify_before_days')
                                ->label(__('admin.labels.advance_notice_days'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            DatePicker::make('event_date')
                                ->label(__('admin.labels.event_date'))
                                ->required(),
                            Toggle::make('completed')
                                ->label(__('admin.labels.completed'))
                                ->default(false),
                        ]),
                        Textarea::make('description')
                            ->label(__('admin.labels.description'))
                            ->rows(4)
                            ->maxLength(2000),
                    ]),
            ]);
    }
}
