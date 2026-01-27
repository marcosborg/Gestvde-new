<?php

namespace App\Filament\Resources\VehicleSupplierContracts\Tables;

use App\Filament\Actions\ExportActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehicleSupplierContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label(__('admin.labels.supplier'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label(__('admin.labels.category'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.contract_categories.'.$state))
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label(__('admin.labels.start_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('admin.labels.end_date'))
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('monthly_cost')
                    ->label(__('admin.labels.monthly_cost'))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('recurring')
                    ->label(__('admin.labels.recurring'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('admin.labels.recurring') : __('admin.labels.not_recurring'))
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray')
                    ->toggleable(),
                TextColumn::make('recurrence_interval')
                    ->label(__('admin.labels.interval'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('admin.recurrence_intervals.'.$state) : '-')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->state(fn ($record): string => $record->end_date ? 'ended' : 'active')
                    ->formatStateUsing(fn (string $state): string => __('admin.contract_status.'.$state))
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label(__('admin.labels.vehicle'))
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('supplier')
                    ->label(__('admin.labels.supplier'))
                    ->relationship('supplier', 'name'),
                SelectFilter::make('category')
                    ->label(__('admin.labels.category'))
                    ->options([
                        'fleet' => __('admin.contract_categories.fleet'),
                        'operations' => __('admin.contract_categories.operations'),
                        'administration' => __('admin.contract_categories.administration'),
                    ]),
                SelectFilter::make('status')
                    ->label(__('admin.labels.status'))
                    ->options([
                        'active' => __('admin.contract_status.active'),
                        'ended' => __('admin.contract_status.ended'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (($data['value'] ?? null) === 'active') {
                            return $query->whereNull('end_date');
                        }

                        if (($data['value'] ?? null) === 'ended') {
                            return $query->whereNotNull('end_date');
                        }

                        return $query;
                    }),
                SelectFilter::make('recurring')
                    ->label(__('admin.labels.recurring'))
                    ->options([
                        '1' => __('admin.labels.recurring'),
                        '0' => __('admin.labels.not_recurring'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        if ($value === null) {
                            return $query;
                        }

                        return $query->where('recurring', (bool) $value);
                    }),
                Filter::make('start_date')
                    ->label(__('admin.labels.start_date'))
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ...ExportActions::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
