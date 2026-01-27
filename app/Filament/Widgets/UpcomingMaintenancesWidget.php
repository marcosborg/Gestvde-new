<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Maintenances\MaintenanceResource;
use App\Models\Maintenance;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingMaintenancesWidget extends TableWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 8;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery())
            ->defaultSort('next_due_date')
            ->columns([
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->url(fn (Maintenance $record): string => MaintenanceResource::getUrl('edit', ['record' => $record]))
                    ->searchable(),
                TextColumn::make('next_due_date')
                    ->label(__('admin.labels.next_date'))
                    ->date()
                    ->badge()
                    ->color(fn (Maintenance $record): ?string => $record->dueSeverity())
                    ->sortable(),
                TextColumn::make('next_due_mileage')
                    ->label(__('admin.labels.next_mileage'))
                    ->numeric()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->state(fn (Maintenance $record): string => $record->resolvedStatus())
                    ->formatStateUsing(fn (string $state): string => __('admin.maintenance_status.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'overdue' => 'danger',
                        'in_progress' => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('description')
                    ->label(__('admin.labels.description'))
                    ->limit(40)
                    ->toggleable(),
            ]);
    }

    protected function getHeading(): ?string
    {
        return __('admin.headings.upcoming_maintenances');
    }

    protected function getBaseQuery(): Builder
    {
        $today = now()->startOfDay();

        return Maintenance::query()
            ->where(function (Builder $query) use ($today): Builder {
                return $query
                    ->overdue($today)
                    ->orWhere(fn (Builder $query): Builder => $query->dueSoon(30, $today));
            })
            ->with('vehicle');
    }
}
