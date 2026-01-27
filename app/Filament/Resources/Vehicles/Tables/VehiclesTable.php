<?php

namespace App\Filament\Resources\Vehicles\Tables;

use App\Filament\Actions\ExportActions;
use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\Driver;
use App\Models\Supplier;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plate')
                    ->label(__('admin.labels.license_plate'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('brand')
                    ->label(__('admin.labels.brand'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('model')
                    ->label(__('admin.labels.model'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('fuel_type')
                    ->label(__('admin.labels.fuel'))
                    ->formatStateUsing(function (mixed $state): string {
                        $values = SupportCollection::make(is_array($state) ? $state : [$state])
                            ->filter()
                            ->map(fn (string $value): string => __('admin.fuel_types.'.$value))
                            ->unique()
                            ->values();

                        return $values->isNotEmpty() ? $values->implode(', ') : '-';
                    })
                    ->toggleable(),
                TextColumn::make('year')
                    ->label(__('admin.labels.year'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'rented' => __('admin.vehicle_status.rented'),
                        'available' => __('admin.vehicle_status.available'),
                        'missing_docs' => __('admin.vehicle_status.missing_docs'),
                        'maintenance' => __('admin.vehicle_status.maintenance'),
                        'in_fleet' => __('admin.vehicle_status.in_fleet'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'rented' => 'warning',
                        'available' => 'success',
                        'missing_docs' => 'danger',
                        'maintenance' => 'info',
                        'in_fleet' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('acquisition_type')
                    ->label(__('admin.labels.acquisition_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'leasing' => __('admin.acquisition_types.leasing'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'leasing' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('currentRental.driver.name')
                    ->label(__('admin.labels.driver'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('currentSupplierContract.supplier.name')
                    ->label(__('admin.labels.supplier'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('mileage')
                    ->label(__('admin.labels.mileage'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('weekly_rent')
                    ->label(__('admin.labels.weekly_rent'))
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('expenses_sum_amount')
                    ->label(__('admin.labels.total_expenses'))
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.labels.status'))
                    ->options([
                        'rented' => __('admin.vehicle_status.rented'),
                        'available' => __('admin.vehicle_status.available'),
                        'missing_docs' => __('admin.vehicle_status.missing_docs'),
                        'maintenance' => __('admin.vehicle_status.maintenance'),
                        'in_fleet' => __('admin.vehicle_status.in_fleet'),
                    ]),
                SelectFilter::make('acquisition_type')
                    ->label(__('admin.labels.acquisition_type'))
                    ->options([
                        'leasing' => __('admin.acquisition_types.leasing'),
                    ]),
                Filter::make('fuel_type')
                    ->label(__('admin.labels.fuel'))
                    ->form([
                        Select::make('fuel_type')
                            ->multiple()
                            ->options([
                                'petrol' => __('admin.fuel_types.petrol'),
                                'diesel' => __('admin.fuel_types.diesel'),
                                'lpg' => __('admin.fuel_types.lpg'),
                                'electric' => __('admin.fuel_types.electric'),
                                'hybrid' => __('admin.fuel_types.hybrid'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $fuelTypes = $data['fuel_type'] ?? [];

                        if (! is_array($fuelTypes) || $fuelTypes === []) {
                            return $query;
                        }

                        return $query->where(function (Builder $query) use ($fuelTypes): Builder {
                            foreach ($fuelTypes as $fuelType) {
                                $query->whereJsonContains('fuel_type', $fuelType);
                            }

                            return $query;
                        });
                    }),
                SelectFilter::make('supplier')
                    ->label(__('admin.labels.supplier'))
                    ->options(fn (): array => Supplier::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] ?? null,
                            fn (Builder $query, $supplierId): Builder => $query->whereHas(
                                'supplierContracts',
                                fn (Builder $query): Builder => $query->where('supplier_id', $supplierId),
                            ),
                        );
                    }),
                SelectFilter::make('driver')
                    ->label(__('admin.labels.driver'))
                    ->options(fn (): array => Driver::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] ?? null,
                            fn (Builder $query, $driverId): Builder => $query->whereHas(
                                'rentals',
                                fn (Builder $query): Builder => $query->where('driver_id', $driverId),
                            ),
                        );
                    }),
                Filter::make('acquisition_date')
                    ->label(__('admin.labels.acquisition_date'))
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('acquisition_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('acquisition_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ...ExportActions::make(),
                BulkActionGroup::make([
                    BulkAction::make('update_status')
                        ->label(__('admin.actions.update_status'))
                        ->form([
                            Select::make('status')
                                ->label(__('admin.labels.status'))
                                ->options([
                                    'rented' => __('admin.vehicle_status.rented'),
                                    'available' => __('admin.vehicle_status.available'),
                                    'missing_docs' => __('admin.vehicle_status.missing_docs'),
                                    'maintenance' => __('admin.vehicle_status.maintenance'),
                                    'in_fleet' => __('admin.vehicle_status.in_fleet'),
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each->update([
                                'status' => $data['status'],
                            ]);
                        })
                        ->visible(function (): bool {
                            $permission = FilamentShield::getResourcePolicyActionsWithPermissions(VehicleResource::class)['update'] ?? null;

                            return $permission ? (auth()->user()?->can($permission) ?? false) : false;
                        }),
                    BulkAction::make('bulk_update')
                        ->label(__('admin.actions.bulk_update'))
                        ->form([
                            TextInput::make('weekly_rent')
                                ->label(__('admin.labels.weekly_rent'))
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                            Select::make('fuel_type')
                                ->label(__('admin.labels.fuel'))
                                ->multiple()
                                ->options([
                                    'petrol' => __('admin.fuel_types.petrol'),
                                    'diesel' => __('admin.fuel_types.diesel'),
                                    'lpg' => __('admin.fuel_types.lpg'),
                                    'electric' => __('admin.fuel_types.electric'),
                                    'hybrid' => __('admin.fuel_types.hybrid'),
                                ]),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $updates = [];

                            if (filled($data['weekly_rent'] ?? null)) {
                                $updates['weekly_rent'] = $data['weekly_rent'];
                            }

                            if (! empty($data['fuel_type'])) {
                                $updates['fuel_type'] = $data['fuel_type'];
                            }

                            if ($updates === []) {
                                return;
                            }

                            $records->each->update($updates);
                        })
                        ->visible(function (): bool {
                            $permission = FilamentShield::getResourcePolicyActionsWithPermissions(VehicleResource::class)['update'] ?? null;

                            return $permission ? (auth()->user()?->can($permission) ?? false) : false;
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
