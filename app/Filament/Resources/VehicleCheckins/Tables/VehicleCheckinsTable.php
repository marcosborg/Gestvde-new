<?php

namespace App\Filament\Resources\VehicleCheckins\Tables;

use App\Enums\VehicleCheckinType;
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

class VehicleCheckinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount(['photos', 'damages']))
            ->columns([
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver.name')
                    ->label(__('admin.labels.driver'))
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('check_type')
                    ->label(__('admin.labels.check_type'))
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        $type = VehicleCheckinType::tryFrom($state);

                        return $type ? $type->label() : $state;
                    })
                    ->color(fn (string $state): string => match ($state) {
                        VehicleCheckinType::CheckIn->value => 'success',
                        VehicleCheckinType::CheckOut->value => 'warning',
                        VehicleCheckinType::Inspection->value => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('occurred_at')
                    ->label(__('admin.labels.occurred_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('photos_count')
                    ->label(__('admin.labels.photos'))
                    ->badge()
                    ->color(fn (?int $state): string => $state ? 'info' : 'gray')
                    ->sortable(),
                TextColumn::make('damages_count')
                    ->label(__('admin.labels.damages'))
                    ->badge()
                    ->color(fn (?int $state): string => $state ? 'danger' : 'gray')
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('admin.labels.notes'))
                    ->limit(40)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label(__('admin.labels.vehicle'))
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('driver')
                    ->label(__('admin.labels.driver'))
                    ->relationship('driver', 'name'),
                SelectFilter::make('check_type')
                    ->label(__('admin.labels.check_type'))
                    ->options(VehicleCheckinType::options()),
                Filter::make('occurred_at')
                    ->label(__('admin.labels.occurred_at'))
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('occurred_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('occurred_at', '<=', $date),
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
