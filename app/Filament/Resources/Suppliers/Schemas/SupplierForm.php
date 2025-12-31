<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Fornecedor')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nome')
                                ->required()
                                ->maxLength(100),
                            TextInput::make('nif')
                                ->label('NIF')
                                ->required()
                                ->maxLength(20)
                                ->unique(ignoreRecord: true),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(150),
                            TextInput::make('phone')
                                ->label('Telefone')
                                ->maxLength(30),
                        ]),
                    ]),
                Section::make('Notas')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(4)
                            ->maxLength(2000),
                    ]),
            ]);
    }
}
