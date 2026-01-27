<?php

namespace App\Filament\Resources\Suppliers\Tables;

use App\Filament\Actions\ExportActions;
use App\Models\Vehicle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.labels.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nif')
                    ->label(__('admin.labels.nif'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('admin.labels.email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label(__('admin.labels.phone'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('vehicle_supplier_contracts_count')
                    ->label(__('admin.labels.contracts'))
                    ->counts('vehicleSupplierContracts')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label(__('admin.labels.vehicle'))
                    ->options(fn (): array => Vehicle::query()->orderBy('plate')->pluck('plate', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] ?? null,
                            fn (Builder $query, $vehicleId): Builder => $query->whereHas(
                                'vehicleSupplierContracts',
                                fn (Builder $query): Builder => $query->where('vehicle_id', $vehicleId),
                            ),
                        );
                    }),
                Filter::make('has_contracts')
                    ->label(__('admin.labels.with_contracts'))
                    ->query(fn (Builder $query): Builder => $query->has('vehicleSupplierContracts')),
                Filter::make('created_at')
                    ->label(__('admin.labels.created_at'))
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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
