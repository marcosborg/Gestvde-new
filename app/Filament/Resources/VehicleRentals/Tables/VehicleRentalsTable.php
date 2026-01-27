<?php

namespace App\Filament\Resources\VehicleRentals\Tables;

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

class VehicleRentalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver.name')
                    ->label(__('admin.labels.driver'))
                    ->searchable()
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
                TextColumn::make('weekly_price')
                    ->label(__('admin.labels.weekly_price'))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->state(fn ($record): string => $record->end_date ? 'ended' : 'active')
                    ->formatStateUsing(fn (string $state): string => __('admin.rental_status.' . $state))
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label(__('admin.labels.vehicle'))
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('driver')
                    ->label(__('admin.labels.driver'))
                    ->relationship('driver', 'name'),
                SelectFilter::make('status')
                    ->label(__('admin.labels.status'))
                    ->options([
                        'active' => __('admin.rental_status.active'),
                        'ended' => __('admin.rental_status.ended'),
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


