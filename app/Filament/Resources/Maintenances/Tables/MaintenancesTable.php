<?php

namespace App\Filament\Resources\Maintenances\Tables;

use App\Enums\MaintenanceType;
use App\Filament\Actions\ExportActions;
use App\Models\Maintenance;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MaintenancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('overdue_indicator')
                    ->label(__('admin.labels.overdue'))
                    ->boolean()
                    ->state(fn (Maintenance $record): bool => $record->isOverdue())
                    ->trueIcon('heroicon-o-exclamation-circle')
                    ->falseIcon(false)
                    ->trueColor('danger'),
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('admin.labels.type'))
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        $type = MaintenanceType::tryFrom($state);

                        return $type ? $type->label() : $state;
                    })
                    ->color(fn (string $state): string => $state === MaintenanceType::Preventive->value ? 'info' : 'warning')
                    ->sortable(),
                TextColumn::make('maintenance_kind')
                    ->label(__('admin.labels.maintenance_kind'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? __('admin.maintenance_kinds.'.$state)
                        : '-')
                    ->color(fn (?string $state): string => match ($state) {
                        'periodic' => 'info',
                        'scheduled' => 'warning',
                        'corrective' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('maintenance_date')
                    ->label(__('admin.labels.maintenance_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('next_due_date')
                    ->label(__('admin.labels.next_date'))
                    ->date()
                    ->placeholder('-')
                    ->badge()
                    ->color(fn (Maintenance $record): ?string => $record->dueSeverity())
                    ->toggleable(),
                TextColumn::make('next_due_mileage')
                    ->label(__('admin.labels.next_mileage'))
                    ->numeric()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('cost')
                    ->label(__('admin.labels.cost'))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->state(fn ($record): string => $record->resolvedStatus())
                    ->formatStateUsing(fn (string $state): string => __('admin.maintenance_status.'.$state))
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'overdue' => 'danger',
                        'in_progress' => 'warning',
                        default => 'info',
                    }),
            ])
            ->recordClasses(fn (Maintenance $record): array => $record->isOverdue()
                ? ['bg-red-50', 'dark:bg-red-950/30']
                : [])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label(__('admin.labels.vehicle'))
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('type')
                    ->label(__('admin.labels.type'))
                    ->options(MaintenanceType::options()),
                SelectFilter::make('maintenance_kind')
                    ->label(__('admin.labels.maintenance_kind'))
                    ->options([
                        'periodic' => __('admin.maintenance_kinds.periodic'),
                        'scheduled' => __('admin.maintenance_kinds.scheduled'),
                        'corrective' => __('admin.maintenance_kinds.corrective'),
                    ]),
                SelectFilter::make('status')
                    ->label(__('admin.labels.status'))
                    ->options([
                        'scheduled' => __('admin.maintenance_status.scheduled'),
                        'in_progress' => __('admin.maintenance_status.in_progress'),
                        'completed' => __('admin.maintenance_status.completed'),
                        'overdue' => __('admin.maintenance_status.overdue'),
                    ]),
                Filter::make('overdue')
                    ->label(__('admin.maintenance_status.overdue'))
                    ->query(fn (Builder $query): Builder => $query->overdue()),
                Filter::make('maintenance_date')
                    ->label(__('admin.labels.maintenance_date'))
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
