<?php

namespace App\Filament\Resources\VehicleExpenses\Tables;

use App\Filament\Actions\ExportActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehicleExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.plate')
                    ->label('Viatura')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fuel' => 'Combustivel',
                        'tolls' => 'Portagens',
                        'insurance' => 'Seguro',
                        'maintenance' => 'Manutencao',
                        'inspection' => 'Inspecao',
                        'taxes' => 'Impostos',
                        'parking' => 'Estacionamento',
                        'fines' => 'Multas',
                        'depreciation' => 'Depreciacao',
                        default => 'Outro',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'fuel' => 'info',
                        'maintenance' => 'warning',
                        'insurance' => 'primary',
                        'fines' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descricao')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('EUR')
                    ->summarize(
                        Sum::make()
                            ->money('EUR')
                            ->label('Total'),
                    )
                    ->sortable(),
                TextColumn::make('expense_date')
                    ->label('Data da despesa')
                    ->date()
                    ->sortable(),
                TextColumn::make('recurring')
                    ->label('Recorrente')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Recorrente' : 'Pontual')
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label('Viatura')
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('category')
                    ->label('Categoria')
                    ->options([
                        'fuel' => 'Combustivel',
                        'tolls' => 'Portagens',
                        'insurance' => 'Seguro',
                        'maintenance' => 'Manutencao',
                        'inspection' => 'Inspecao',
                        'taxes' => 'Impostos',
                        'parking' => 'Estacionamento',
                        'fines' => 'Multas',
                        'depreciation' => 'Depreciacao',
                        'other' => 'Outro',
                    ]),
                Filter::make('expense_date')
                    ->label('Data da despesa')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '<=', $date),
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
