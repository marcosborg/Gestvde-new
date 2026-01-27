<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Enums\VehicleCheckinType;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DamagesRelationManager extends RelationManager
{
    protected static string $relationship = 'damages';

    protected static ?string $title = null;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['checkin.driver'])->withCount('photos'))
            ->columns([
                TextColumn::make('checkin.occurred_at')
                    ->label(__('admin.labels.occurred_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('checkin.check_type')
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
                TextColumn::make('checkin.driver.name')
                    ->label(__('admin.labels.driver'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('zone')
                    ->label(__('admin.labels.zone'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('photos_count')
                    ->label(__('admin.labels.photos'))
                    ->badge()
                    ->color(fn (?int $state): string => $state ? 'info' : 'gray')
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('admin.labels.description'))
                    ->limit(40)
                    ->toggleable(),
            ])
            ->defaultSort('checkin.occurred_at', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.labels.damages');
    }
}
