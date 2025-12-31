<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes da Viatura')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('plate')
                                ->label('Matricula')
                                ->required()
                                ->maxLength(20)
                                ->unique(ignoreRecord: true),
                            TextInput::make('brand')
                                ->label('Marca')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('model')
                                ->label('Modelo')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('year')
                                ->label('Ano')
                                ->numeric()
                                ->required()
                                ->minValue(1900)
                                ->maxValue(now()->year + 1),
                            Select::make('fuel_type')
                                ->label('Combustivel')
                                ->required()
                                ->options([
                                    'petrol' => 'Gasolina',
                                    'diesel' => 'Diesel',
                                    'hybrid' => 'Hibrido',
                                    'electric' => 'Eletrico',
                                    'lpg' => 'LPG',
                                    'other' => 'Outro',
                                ]),
                            TextInput::make('mileage')
                                ->label('Quilometragem')
                                ->numeric()
                                ->required()
                                ->minValue(0),
                        ]),
                    ]),
                Section::make('Aquisicao')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('acquisition_type')
                                ->label('Tipo de aquisicao')
                                ->required()
                                ->options([
                                    'own' => 'Propria',
                                    'third_party' => 'Terceiro',
                                ]),
                            TextInput::make('acquisition_value')
                                ->label('Valor de aquisicao')
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                            DatePicker::make('acquisition_date')
                                ->label('Data de aquisicao'),
                            TextInput::make('annual_depreciation_percent')
                                ->label('Depreciacao anual')
                                ->numeric()
                                ->suffix('%')
                                ->minValue(0)
                                ->maxValue(100),
                            TextInput::make('weekly_rent')
                                ->label('Aluguer semanal')
                                ->numeric()
                                ->prefix('EUR')
                                ->minValue(0),
                        ]),
                    ]),
                Section::make('Estado')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->required()
                            ->options([
                                'active' => 'Ativo',
                                'inactive' => 'Inativo',
                                'sold' => 'Vendido',
                            ])
                            ->default('active'),
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
