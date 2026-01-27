<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Enums\VehicleCheckinType;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VehicleCheckinsRelationManager extends RelationManager
{
    protected static string $relationship = 'checkins';

    protected static ?string $title = null;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount(['photos', 'damages'])->with('driver'))
            ->columns([
                TextColumn::make('occurred_at')
                    ->label(__('admin.labels.occurred_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
                TextColumn::make('driver.name')
                    ->label(__('admin.labels.driver'))
                    ->placeholder('-')
                    ->toggleable(),
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
            ->defaultSort('occurred_at', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.navigation.vehicle_checkins');
    }
}
