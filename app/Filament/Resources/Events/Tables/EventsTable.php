<?php

namespace App\Filament\Resources\Events\Tables;

use App\Filament\Actions\ExportActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.labels.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('event_type')
                    ->label(__('admin.labels.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'inspection' => __('admin.event_types.inspection'),
                        'insurance' => __('admin.event_types.insurance'),
                        'maintenance' => __('admin.event_types.maintenance'),
                        'document' => __('admin.event_types.document'),
                        'contract' => __('admin.event_types.contract'),
                        'tax' => __('admin.event_types.tax'),
                        default => __('admin.event_types.other'),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'inspection' => 'info',
                        'insurance' => 'primary',
                        'maintenance' => 'warning',
                        'document' => 'info',
                        'contract' => 'gray',
                        'tax' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('event_date')
                    ->label(__('admin.labels.event_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('notify_before_days')
                    ->label(__('admin.labels.advance_notice_days'))
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('admin.labels.status'))
                    ->badge()
                    ->state(function ($record): string {
                        if ($record->completed) {
                            return 'completed';
                        }

                        if ($record->event_date?->isPast()) {
                            return 'overdue';
                        }

                        return 'pending';
                    })
                    ->formatStateUsing(fn (string $state): string => __('admin.event_status.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'overdue' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label(__('admin.labels.vehicle'))
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('event_type')
                    ->label(__('admin.labels.type'))
                    ->options([
                        'inspection' => __('admin.event_types.inspection'),
                        'insurance' => __('admin.event_types.insurance'),
                        'maintenance' => __('admin.event_types.maintenance'),
                        'document' => __('admin.event_types.document'),
                        'contract' => __('admin.event_types.contract'),
                        'tax' => __('admin.event_types.tax'),
                        'other' => __('admin.event_types.other'),
                    ]),
                SelectFilter::make('completed')
                    ->label(__('admin.labels.completed'))
                    ->options([
                        '0' => __('admin.event_status.pending'),
                        '1' => __('admin.event_status.completed'),
                    ]),
                Filter::make('event_date')
                    ->label(__('admin.labels.event_date'))
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('event_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('event_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('complete')
                    ->label(__('admin.actions.complete'))
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => ! $record->completed)
                    ->action(function ($record): void {
                        $record->update(['completed' => true]);

                        Notification::make()
                            ->title(__('admin.messages.event_completed'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                ...ExportActions::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}


