<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SupplierExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static ?string $title = null;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_date')
                    ->label(__('admin.labels.expense_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('expense_type')
                    ->label(__('admin.labels.expense_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.expense_types.' . $state))
                    ->color(fn (string $state): string => $state === 'company' ? 'primary' : 'info')
                    ->sortable(),
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expenseCategory.name')
                    ->label(__('admin.labels.category'))
                    ->badge()
                    ->state(fn ($record): string => $record->expenseCategory?->name ?? $record->category ?? '-')
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('admin.labels.value'))
                    ->money('EUR')
                    ->summarize(
                        Sum::make()
                            ->money('EUR')
                            ->label(__('admin.labels.total')),
                    )
                    ->sortable(),
                TextColumn::make('expense_status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.expense_status.' . $state))
                    ->color(fn (string $state): string => $state === 'paid' ? 'success' : 'warning')
                    ->sortable(),
            ])
            ->defaultSort('expense_date', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.navigation.vehicle_expenses');
    }
}
