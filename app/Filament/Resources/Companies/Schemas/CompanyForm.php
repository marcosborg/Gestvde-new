<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.company_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('admin.labels.name'))
                                ->required()
                                ->maxLength(150),
                            TextInput::make('vat_number')
                                ->label(__('admin.labels.nif'))
                                ->maxLength(20)
                                ->unique(ignoreRecord: true),
                            TextInput::make('email')
                                ->label(__('admin.labels.email'))
                                ->email()
                                ->maxLength(150),
                            TextInput::make('phone')
                                ->label(__('admin.labels.phone'))
                                ->maxLength(30),
                        ]),
                    ]),
                Section::make(__('admin.sections.address_notes'))
                    ->schema([
                        Textarea::make('address')
                            ->label(__('admin.labels.address'))
                            ->rows(3)
                            ->maxLength(2000),
                        Textarea::make('notes')
                            ->label(__('admin.labels.notes'))
                            ->rows(3)
                            ->maxLength(2000),
                    ]),
            ]);
    }
}
