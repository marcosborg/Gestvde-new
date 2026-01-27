<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class DocumentExpirationsWidget extends TableWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 7;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery())
            ->defaultSort('valid_until')
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.labels.title'))
                    ->searchable()
                    ->limit(40),
                TextColumn::make('doc_type')
                    ->label(__('admin.labels.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.document_types.' . $state))
                    ->sortable(),
                TextColumn::make('document_owner')
                    ->label(__('admin.labels.document_owner'))
                    ->state(fn (Document $record): string => $record->documentable?->plate
                        ?? $record->documentable?->name
                        ?? '-')
                    ->toggleable(),
                TextColumn::make('valid_until')
                    ->label(__('admin.labels.validity'))
                    ->date()
                    ->badge()
                    ->color(fn (Document $record): string => $record->isExpired()
                        ? 'danger'
                        : ($record->isExpiringSoon() ? 'warning' : 'gray'))
                    ->sortable(),
            ]);
    }

    protected function getHeading(): ?string
    {
        return __('admin.headings.document_expirations');
    }

    protected function getBaseQuery(): Builder
    {
        return Document::query()
            ->where(function (Builder $query): Builder {
                $today = now()->startOfDay();

                return $query
                    ->expired($today)
                    ->orWhere(fn (Builder $query): Builder => $query->expiringSoon($today));
            })
            ->with('documentable');
    }
}
