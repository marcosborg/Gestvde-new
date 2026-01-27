<?php

namespace App\Filament\Resources\VehicleExpenses\Tables;

use App\Filament\Actions\ExportActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehicleExpensesTable
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
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('expense_type')
                    ->label(__('admin.labels.expense_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.expense_types.' . $state))
                    ->color(fn (string $state): string => $state === 'company' ? 'primary' : 'info')
                    ->sortable(),
                TextColumn::make('expenseCategory.name')
                    ->label(__('admin.labels.category'))
                    ->badge()
                    ->state(fn ($record): string => $record->expenseCategory?->name ?? $record->category ?? '-')
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('admin.labels.description'))
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label(__('admin.labels.value'))
                    ->money('EUR')
                    ->summarize(
                        Sum::make()
                            ->money('EUR')
                            ->label(__('admin.labels.total')),
                    )
                    ->sortable(),
                TextColumn::make('expense_date')
                    ->label(__('admin.labels.expense_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('expense_status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.expense_status.' . $state))
                    ->color(fn (string $state): string => $state === 'paid' ? 'success' : 'warning')
                    ->sortable(),
                TextColumn::make('recurring')
                    ->label(__('admin.labels.recurring'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('admin.labels.recurring') : __('admin.labels.one_time'))
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label(__('admin.labels.vehicle'))
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('supplier')
                    ->label(__('admin.labels.supplier'))
                    ->relationship('supplier', 'name'),
                SelectFilter::make('expense_type')
                    ->label(__('admin.labels.expense_type'))
                    ->options([
                        'fleet' => __('admin.expense_types.fleet'),
                        'company' => __('admin.expense_types.company'),
                    ]),
                SelectFilter::make('expense_status')
                    ->label(__('admin.labels.status'))
                    ->options([
                        'paid' => __('admin.expense_status.paid'),
                        'unpaid' => __('admin.expense_status.unpaid'),
                    ]),
                SelectFilter::make('expense_category_id')
                    ->label(__('admin.labels.category'))
                    ->relationship('expenseCategory', 'name'),
                Filter::make('expense_date')
                    ->label(__('admin.labels.expense_date'))
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '<=', $date),
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


