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
                    ->label('Titulo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicle.plate')
                    ->label('Viatura')
                    ->placeholder('-')
                    ->sortable(),
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
                    })
                    ->sortable(),
                TextColumn::make('event_date')
                    ->label('Data do evento')
                    ->date()
                    ->sortable(),
                TextColumn::make('notify_before_days')
                    ->label('Aviso (dias)')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->state(function ($record): string {
                        if ($record->completed) {
                            return 'Concluido';
                        }

                        if ($record->event_date?->isPast()) {
                            return 'Atrasado';
                        }

                        return 'Pendente';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Concluido' => 'success',
                        'Atrasado' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('vehicle')
                    ->label('Viatura')
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('event_type')
                    ->label('Categoria')
                    ->options([
                        'inspection' => 'Inspecao',
                        'insurance' => 'Seguro',
                        'maintenance' => 'Manutencao',
                        'contract' => 'Contrato',
                        'tax' => 'Imposto',
                        'other' => 'Outro',
                    ]),
                SelectFilter::make('completed')
                    ->label('Estado')
                    ->options([
                        '0' => 'Pendente',
                        '1' => 'Concluido',
                    ]),
                Filter::make('event_date')
                    ->label('Data do evento')
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
                    ->label('Concluir')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => ! $record->completed)
                    ->action(function ($record): void {
                        $record->update(['completed' => true]);

                        Notification::make()
                            ->title('Evento concluido')
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
