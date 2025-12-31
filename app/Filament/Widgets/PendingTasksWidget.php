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
    protected static ?string $heading = 'Tarefas Pendentes';

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery())
            ->defaultSort('event_date')
            ->columns([
                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('vehicle.plate')
                    ->label('Viatura')
                    ->placeholder('-'),
                TextColumn::make('event_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'inspection' => 'Inspecao',
                        'insurance' => 'Seguro',
                        'maintenance' => 'Manutencao',
                        'contract' => 'Contrato',
                        'tax' => 'Imposto',
                        default => 'Outro',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'inspection' => 'info',
                        'insurance' => 'primary',
                        'maintenance' => 'warning',
                        'contract' => 'gray',
                        'tax' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('event_date')
                    ->label('Data')
                    ->date()
                    ->sortable(),
                TextColumn::make('urgency')
                    ->label('Urgencia')
                    ->badge()
                    ->state(function ($record): string {
                        $today = Carbon::today();

                        if ($record->event_date?->isPast()) {
                            return 'Atrasado';
                        }

                        if ($record->event_date?->diffInDays($today) <= 3) {
                            return 'Urgente';
                        }

                        return 'Agendado';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Atrasado' => 'danger',
                        'Urgente' => 'warning',
                        default => 'info',
                    }),
            ])
            ->actions([
                Action::make('complete')
                    ->label('Concluir')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (Event $record): void {
                        $record->update(['completed' => true]);

                        Notification::make()
                            ->title('Tarefa concluida')
                            ->success()
                            ->send();
                    }),
            ]);
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
