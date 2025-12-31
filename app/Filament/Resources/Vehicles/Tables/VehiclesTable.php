<?php

namespace App\Filament\Resources\Vehicles\Tables;

use App\Filament\Actions\ExportActions;
use App\Models\Driver;
use App\Models\Supplier;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plate')
                    ->label('Matricula')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('brand')
                    ->label('Marca')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('model')
                    ->label('Modelo')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('year')
                    ->label('Ano')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Ativo',
                        'inactive' => 'Inativo',
                        'sold' => 'Vendido',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'sold' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('acquisition_type')
                    ->label('Aquisicao')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'own' => 'Propria',
                        'third_party' => 'Terceiro',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'own' => 'info',
                        'third_party' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('currentRental.driver.name')
                    ->label('Motorista atual')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('currentSupplierContract.supplier.name')
                    ->label('Fornecedor')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('mileage')
                    ->label('Quilometragem')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('weekly_rent')
                    ->label('Aluguer semanal')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('expenses_sum_amount')
                    ->label('Total de despesas')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Ativo',
                        'inactive' => 'Inativo',
                        'sold' => 'Vendido',
                    ]),
                SelectFilter::make('acquisition_type')
                    ->label('Aquisicao')
                    ->options([
                        'own' => 'Propria',
                        'third_party' => 'Terceiro',
                    ]),
                SelectFilter::make('supplier')
                    ->label('Fornecedor')
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
                    ->label('Motorista')
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
                    ->label('Data de aquisicao')
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
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
