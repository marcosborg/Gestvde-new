<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SupplierMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'movements';

    protected static ?string $title = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.movement'))
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('date')
                                ->label(__('admin.labels.date'))
                                ->required(),
                            Select::make('type')
                                ->label(__('admin.labels.type'))
                                ->options([
                                    'debit' => __('admin.movement_types.debit'),
                                    'credit' => __('admin.movement_types.credit'),
                                ])
                                ->required(),
                            TextInput::make('amount')
                                ->label(__('admin.labels.value'))
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0.01)
                                ->required(),
                            TextInput::make('reference')
                                ->label(__('admin.labels.reference'))
                                ->maxLength(120),
                            Textarea::make('notes')
                                ->label(__('admin.labels.notes'))
                                ->rows(3)
                                ->maxLength(2000)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label(__('admin.labels.date'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('admin.labels.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'debit' ? __('admin.movement_types.debit') : __('admin.movement_types.credit'))
                    ->color(fn (string $state): string => $state === 'debit' ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('admin.labels.value'))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('reference')
                    ->label(__('admin.labels.reference'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('notes')
                    ->label(__('admin.labels.notes'))
                    ->limit(40)
                    ->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.navigation.account_statement');
    }
}


