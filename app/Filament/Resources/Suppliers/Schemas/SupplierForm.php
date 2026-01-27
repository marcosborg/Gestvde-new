<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Models\Supplier;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.account_statement'))
                    ->schema([
                        Grid::make(3)->schema([
                            Placeholder::make('current_balance')
                                ->label(__('admin.labels.current_balance'))
                                ->content(function (?Supplier $record): string {
                                    $summary = self::movementSummary($record);

                                    return number_format($summary['balance'], 2).' EUR';
                                }),
                            Placeholder::make('total_debits')
                                ->label(__('admin.labels.total_debit'))
                                ->content(function (?Supplier $record): string {
                                    $summary = self::movementSummary($record);

                                    return number_format($summary['debit'], 2).' EUR';
                                }),
                            Placeholder::make('total_credits')
                                ->label(__('admin.labels.total_credit'))
                                ->content(function (?Supplier $record): string {
                                    $summary = self::movementSummary($record);

                                    return number_format($summary['credit'], 2).' EUR';
                                }),
                        ]),
                        Placeholder::make('account_statement_note')
                            ->label(__('admin.labels.note'))
                            ->content(__('admin.help_texts.account_statement'))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.sections.identification'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('admin.labels.name'))
                                ->required()
                                ->maxLength(150),
                            TextInput::make('legal_name')
                                ->label(__('admin.labels.legal_name'))
                                ->maxLength(150),
                            TextInput::make('commercial_name')
                                ->label(__('admin.labels.trade_name'))
                                ->maxLength(150),
                            TextInput::make('nif')
                                ->label(__('admin.labels.nif'))
                                ->maxLength(20),
                            TextInput::make('contact_person')
                                ->label(__('admin.labels.contact_person'))
                                ->maxLength(120),
                        ]),
                    ]),
                Section::make(__('admin.sections.contact'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->label(__('admin.labels.email'))
                                ->email()
                                ->maxLength(150),
                            TextInput::make('phone')
                                ->label(__('admin.labels.phone'))
                                ->maxLength(30),
                        ]),
                        Textarea::make('address')
                            ->label(__('admin.labels.address'))
                            ->rows(3)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.sections.categories_recurring'))
                    ->schema([
                        Select::make('categories')
                            ->label(__('admin.labels.categories'))
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('admin.labels.name'))
                                    ->required()
                                    ->maxLength(120),
                            ])
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.sections.images'))
                    ->schema([
                        Repeater::make('images')
                            ->relationship()
                            ->schema([
                                FileUpload::make('path')
                                    ->label(__('admin.labels.image'))
                                    ->image()
                                    ->disk('public')
                                    ->directory('suppliers')
                                    ->columnSpanFull(),
                                Select::make('type')
                                    ->label(__('admin.labels.type'))
                                    ->options([
                                        'logo' => __('admin.labels.logo'),
                                        'facade' => __('admin.labels.facade'),
                                        'document' => __('admin.labels.document'),
                                    ]),
                                Textarea::make('description')
                                    ->label(__('admin.labels.description'))
                                    ->rows(2)
                                    ->maxLength(500),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.sections.notes'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('admin.labels.notes'))
                            ->rows(4)
                            ->maxLength(2000),
                    ]),
            ]);
    }

    private static function movementSummary(?Supplier $record): array
    {
        static $cache = [];

        if (! $record) {
            return [
                'debit' => 0.0,
                'credit' => 0.0,
                'balance' => 0.0,
            ];
        }

        $key = $record->getKey() ?? spl_object_hash($record);

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $totals = $record->movements()
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) AS debit_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END), 0) AS credit_total")
            ->first();

        $debit = (float) ($totals->debit_total ?? 0);
        $credit = (float) ($totals->credit_total ?? 0);

        return $cache[$key] = [
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $debit - $credit,
        ];
    }
}
