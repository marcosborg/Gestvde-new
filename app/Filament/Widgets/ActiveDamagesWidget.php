<?php

namespace App\Filament\Widgets;

use App\Enums\VehicleCheckinType;
use App\Filament\Resources\VehicleCheckins\VehicleCheckinResource;
use App\Models\VehicleCheckin;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ActiveDamagesWidget extends TableWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 9;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery())
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->url(fn (VehicleCheckin $record): string => VehicleCheckinResource::getUrl('edit', ['record' => $record]))
                    ->searchable(),
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
                    }),
                TextColumn::make('occurred_at')
                    ->label(__('admin.labels.occurred_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('damages_count')
                    ->label(__('admin.labels.damages'))
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('driver.name')
                    ->label(__('admin.labels.driver'))
                    ->placeholder('-')
                    ->toggleable(),
            ]);
    }

    protected function getHeading(): ?string
    {
        return __('admin.headings.active_damages');
    }

    protected function getBaseQuery(): Builder
    {
        $latestCheckins = VehicleCheckin::query()
            ->select('vehicle_id', DB::raw('MAX(occurred_at) AS latest_occurred_at'))
            ->groupBy('vehicle_id');

        return VehicleCheckin::query()
            ->select('vehicle_checkins.*')
            ->joinSub($latestCheckins, 'latest_checkins', function ($join): void {
                $join->on('vehicle_checkins.vehicle_id', '=', 'latest_checkins.vehicle_id')
                    ->on('vehicle_checkins.occurred_at', '=', 'latest_checkins.latest_occurred_at');
            })
            ->with(['vehicle', 'driver'])
            ->withCount('damages')
            ->whereHas('damages');
    }
}
