<?php

namespace App\Filament\Resources\Maintenances\Tables;

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
use Illuminate\Support\Carbon;

class MaintenancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.plate')
                    ->label('Viatura')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'preventive' ? 'Preventiva' : 'Corretiva')
                    ->color(fn (string $state): string => $state === 'preventive' ? 'info' : 'warning')
                    ->sortable(),
                TextColumn::make('maintenance_date')
                    ->label('Data da manutencao')
                    ->date()
                    ->sortable(),
                TextColumn::make('next_due_date')
                    ->label('Proxima data')
                    ->date()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('next_due_mileage')
                    ->label('Proxima quilometragem')
                    ->numeric()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('cost')
                    ->label('Custo')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->state(function ($record): string {
                        $today = Carbon::today();
                        $dateOverdue = $record->next_due_date && $record->next_due_date->lte($today);
                        $mileageOverdue = $record->next_due_mileage && $record->vehicle && $record->vehicle->mileage >= $record->next_due_mileage;

                        return ($dateOverdue || $mileageOverdue) ? 'Pendente' : 'Concluido';
                    })
                    ->color(fn (string $state): string => $state === 'Pendente' ? 'danger' : 'success'),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label('Viatura')
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('type')
                    ->options([
                        'preventive' => 'Preventiva',
                        'corrective' => 'Corretiva',
                    ]),
                Filter::make('maintenance_date')
                    ->label('Data da manutencao')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('maintenance_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('maintenance_date', '<=', $date),
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
