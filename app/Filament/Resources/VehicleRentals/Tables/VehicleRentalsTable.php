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
                    ->label('Viatura')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver.name')
                    ->label('Motorista')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Data de inicio')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Data de fim')
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('weekly_price')
                    ->label('Preco semanal')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->state(fn ($record): string => $record->end_date ? 'Terminado' : 'Ativo')
                    ->color(fn (string $state): string => $state === 'Ativo' ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label('Viatura')
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('driver')
                    ->label('Motorista')
                    ->relationship('driver', 'name'),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Ativo',
                        'ended' => 'Terminado',
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
                    ->label('Data de inicio')
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
