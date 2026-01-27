<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SupplierContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicleSupplierContracts';

    protected static ?string $title = null;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label(__('admin.labels.category'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.contract_categories.'.$state)),
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
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
                TextColumn::make('status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->state(fn ($record): string => $record->end_date ? 'ended' : 'active')
                    ->formatStateUsing(fn (string $state): string => __('admin.contract_status.'.$state))
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
            ])
            ->defaultSort('start_date', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.navigation.supplier_contracts');
    }
}
