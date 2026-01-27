<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PendingTasksWidget extends TableWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery())
            ->defaultSort('event_date')
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.labels.title'))
                    ->searchable()
                    ->limit(40),
                TextColumn::make('vehicle.plate')
                    ->label(__('admin.labels.vehicle'))
                    ->placeholder('-'),
                TextColumn::make('event_type')
                    ->label(__('admin.labels.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.event_types.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'inspection' => 'info',
                        'insurance' => 'primary',
                        'maintenance' => 'warning',
                        'document' => 'info',
                        'contract' => 'gray',
                        'tax' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('event_date')
                    ->label(__('admin.labels.date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('urgency')
                    ->label(__('admin.labels.urgency'))
                    ->badge()
                    ->state(function ($record): string {
                        $today = Carbon::today();

                        if ($record->event_date?->isPast()) {
                            return 'overdue';
                        }

                        if ($record->event_date?->diffInDays($today) <= 3) {
                            return 'urgent';
                        }

                        return 'scheduled';
                    })
                    ->formatStateUsing(fn (string $state): string => __('admin.urgency.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'overdue' => 'danger',
                        'urgent' => 'warning',
                        default => 'info',
                    }),
            ])
            ->actions([
                Action::make('complete')
                    ->label(__('admin.actions.complete'))
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (Event $record): void {
                        $record->update(['completed' => true]);

                        Notification::make()
                            ->title(__('admin.messages.task_completed'))
                            ->success()
                            ->send();
                    }),
            ]);
    }

    protected function getHeading(): ?string
    {
        return __('admin.headings.pending_tasks');
    }

    protected function getBaseQuery(): Builder
    {
        $today = now()->startOfDay();
        $limitDate = $today->copy()->addDays(30);

        return Event::query()
            ->where('completed', false)
            ->whereBetween('event_date', [$today, $limitDate])
            ->with('vehicle');
    }
}
