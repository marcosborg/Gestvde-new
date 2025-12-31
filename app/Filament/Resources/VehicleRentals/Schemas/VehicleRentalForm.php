<?php

namespace App\Filament\Resources\VehicleRentals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleRentalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Aluguer')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('vehicle_id')
                                ->label('Viatura')
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('driver_id')
                                ->label('Motorista')
                                ->relationship('driver', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DatePicker::make('start_date')
                                ->label('Data de inicio')
                                ->required(),
                            DatePicker::make('end_date')
                                ->label('Data de fim'),
                            TextInput::make('weekly_price')
                                ->label('Preco semanal')
                                ->numeric()
                                ->required()
                                ->prefix('EUR')
                                ->minValue(0),
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
