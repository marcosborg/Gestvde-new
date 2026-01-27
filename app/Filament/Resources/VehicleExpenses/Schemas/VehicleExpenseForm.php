<?php

namespace App\Filament\Resources\VehicleExpenses\Schemas;

use App\Models\ExpenseCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class VehicleExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.expense_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('vehicle_id')
                                ->label(__('admin.labels.vehicle'))
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText(__('admin.help_texts.expense_vehicle')),
                            Select::make('supplier_id')
                                ->label(__('admin.labels.supplier'))
                                ->relationship('supplier', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Select::make('expense_type')
                                ->label(__('admin.labels.expense_type'))
                                ->options([
                                    'fleet' => __('admin.expense_types.fleet'),
                                    'company' => __('admin.expense_types.company'),
                                ])
                                ->default('fleet')
                                ->helperText(__('admin.help_texts.expense_type'))
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?string $state): void {
                                    $set('expense_category_id', null);

                                    if ($state === 'company') {
                                        $set('vehicle_id', null);
                                    }
                                }),
                            Select::make('expense_category_id')
                                ->label(__('admin.labels.category'))
                                ->relationship(
                                    'expenseCategory',
                                    'name',
                                    modifyQueryUsing: fn ($query, Get $get) => $query
                                        ->where('is_active', true)
                                        ->when(
                                            $get('expense_type'),
                                            fn ($query, string $type) => $query->whereIn('type', [$type, 'general']),
                                        )
                                )
                                ->searchable()
                                ->preload()
                                ->helperText(__('admin.help_texts.expense_category'))
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label(__('admin.labels.name'))
                                        ->required()
                                        ->maxLength(120),
                                    Select::make('type')
                                        ->label(__('admin.labels.type'))
                                        ->options([
                                            'fleet' => __('admin.expense_types.fleet'),
                                            'company' => __('admin.expense_types.company'),
                                            'general' => __('admin.expense_types.general'),
                                        ])
                                        ->required(),
                                    Toggle::make('is_active')
                                        ->label(__('admin.labels.active'))
                                        ->default(true),
                                ])
                                ->required(),
                            TextInput::make('category')
                                ->hidden()
                                ->dehydrated()
                                ->dehydrateStateUsing(function (Get $get, ?string $state): ?string {
                                    $categoryId = $get('expense_category_id');

                                    if (! $categoryId) {
                                        return $state;
                                    }

                                    return ExpenseCategory::query()->find($categoryId)?->name ?? $state;
                                }),
                            TextInput::make('description')
                                ->label(__('admin.labels.description'))
                                ->maxLength(255),
                            TextInput::make('amount')
                                ->label(__('admin.labels.value'))
                                ->numeric()
                                ->required()
                                ->prefix('EUR')
                                ->minValue(0),
                            DatePicker::make('expense_date')
                                ->label(__('admin.labels.expense_date'))
                                ->required(),
                            Select::make('expense_status')
                                ->label(__('admin.labels.status'))
                                ->options([
                                    'paid' => __('admin.expense_status.paid'),
                                    'unpaid' => __('admin.expense_status.unpaid'),
                                ])
                                ->default('unpaid')
                                ->required(),
                        ]),
                    ]),
                Section::make(__('admin.sections.recurrence'))
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('recurring')
                                ->label(__('admin.labels.recurring'))
                                ->default(false),
                            Select::make('recurrence_interval')
                                ->label(__('admin.labels.interval'))
                                ->options([
                                    'monthly' => __('admin.recurrence_intervals.monthly'),
                                    'yearly' => __('admin.recurrence_intervals.yearly'),
                                    'custom' => __('admin.recurrence_intervals.custom'),
                                ])
                                ->visible(fn (Get $get): bool => (bool) $get('recurring'))
                                ->required(fn (Get $get): bool => (bool) $get('recurring')),
                        ]),
                    ]),
            ]);
    }
}
