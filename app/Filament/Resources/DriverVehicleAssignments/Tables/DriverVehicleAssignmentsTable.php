<?php

namespace App\Filament\Resources\DriverVehicleAssignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DriverVehicleAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('driver.name')
                    ->label('Motorista')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicle.plate')
                    ->label('Viatura')
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
                TextColumn::make('weekly_rate_used')
                    ->label('Valor semanal')
                    ->state(fn ($record): float => (float) ($record->weekly_rate_override ?? $record->vehicle?->weekly_rent ?? 0))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('daily_rate')
                    ->label('Valor diario')
                    ->state(function ($record): float {
                        $weeklyRate = (float) ($record->weekly_rate_override ?? $record->vehicle?->weekly_rent ?? 0);

                        return $weeklyRate / 7;
                    })
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->state(function ($record): string {
                        $today = Carbon::today();
                        $startDate = Carbon::parse($record->start_date)->startOfDay();
                        $endDate = $record->end_date ? Carbon::parse($record->end_date)->startOfDay() : null;
                        $active = $startDate->lte($today) && (! $endDate || $endDate->gte($today));

                        return $active ? 'Ativo' : 'Concluido';
                    })
                    ->color(fn (string $state): string => $state === 'Ativo' ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('driver')
                    ->label('Motorista')
                    ->relationship('driver', 'name'),
                SelectFilter::make('vehicle')
                    ->label('Viatura')
                    ->relationship('vehicle', 'plate'),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
