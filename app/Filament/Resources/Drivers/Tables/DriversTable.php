<?php

namespace App\Filament\Resources\Drivers\Tables;

use App\Filament\Actions\ExportActions;
use App\Filament\Resources\Drivers\DriverResource;
use App\Models\Company;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.labels.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('admin.labels.email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label(__('admin.labels.phone'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('active')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('admin.status.active') : __('admin.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextColumn::make('rentals_count')
                    ->label(__('admin.labels.rentals'))
                    ->counts('rentals')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('active')
                    ->options([
                        '1' => __('admin.status.active'),
                        '0' => __('admin.status.inactive'),
                    ]),
                Filter::make('has_rentals')
                    ->label(__('admin.labels.with_rentals'))
                    ->query(fn (Builder $query): Builder => $query->has('rentals')),
                Filter::make('created_at')
                    ->label(__('admin.labels.created_at'))
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ...ExportActions::make(),
                BulkActionGroup::make([
                    BulkAction::make('update_status')
                        ->label(__('admin.actions.update_status'))
                        ->form([
                            Select::make('active')
                                ->label(__('admin.labels.status'))
                                ->options([
                                    '1' => __('admin.status.active'),
                                    '0' => __('admin.status.inactive'),
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each->update([
                                'active' => (bool) $data['active'],
                            ]);
                        })
                        ->visible(function (): bool {
                            $permission = FilamentShield::getResourcePolicyActionsWithPermissions(DriverResource::class)['update'] ?? null;

                            return $permission ? (auth()->user()?->can($permission) ?? false) : false;
                        }),
                    BulkAction::make('assign_company')
                        ->label(__('admin.actions.assign_company'))
                        ->form([
                            Select::make('company_id')
                                ->label(__('admin.labels.company'))
                                ->options(fn (): array => Company::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each->update([
                                'company_id' => $data['company_id'],
                            ]);
                        })
                        ->visible(function (): bool {
                            $permission = FilamentShield::getResourcePolicyActionsWithPermissions(DriverResource::class)['update'] ?? null;

                            return $permission ? (auth()->user()?->can($permission) ?? false) : false;
                        }),
                    BulkAction::make('bulk_update')
                        ->label(__('admin.actions.bulk_update'))
                        ->form([
                            Select::make('on_vacation')
                                ->label(__('admin.labels.holidays'))
                                ->options([
                                    '1' => __('admin.status.active'),
                                    '0' => __('admin.status.inactive'),
                                ])
                                ->nullable(),
                            Select::make('blacklisted')
                                ->label(__('admin.labels.blacklist'))
                                ->options([
                                    '1' => __('admin.status.active'),
                                    '0' => __('admin.status.inactive'),
                                ])
                                ->nullable(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $updates = [];

                            if (array_key_exists('on_vacation', $data) && $data['on_vacation'] !== null && $data['on_vacation'] !== '') {
                                $updates['on_vacation'] = (bool) $data['on_vacation'];
                            }

                            if (array_key_exists('blacklisted', $data) && $data['blacklisted'] !== null && $data['blacklisted'] !== '') {
                                $updates['blacklisted'] = (bool) $data['blacklisted'];
                            }

                            if ($updates === []) {
                                return;
                            }

                            $records->each->update($updates);
                        })
                        ->visible(function (): bool {
                            $permission = FilamentShield::getResourcePolicyActionsWithPermissions(DriverResource::class)['update'] ?? null;

                            return $permission ? (auth()->user()?->can($permission) ?? false) : false;
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
