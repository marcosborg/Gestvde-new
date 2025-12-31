<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Motorista')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nome')
                                ->required()
                                ->maxLength(120),
                            TextInput::make('phone')
                                ->label('Telefone')
                                ->maxLength(30),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(150),
                            Toggle::make('active')
                                ->label('Ativo')
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
