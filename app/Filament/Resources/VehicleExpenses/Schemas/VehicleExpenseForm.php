<?php

namespace App\Filament\Resources\VehicleExpenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Get;

class VehicleExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes da Despesa')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('vehicle_id')
                                ->label('Viatura')
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('category')
                                ->required()
                                ->options([
                                    'fuel' => 'Combustivel',
                                    'tolls' => 'Portagens',
                                    'insurance' => 'Seguro',
                                    'maintenance' => 'Manutencao',
                                    'inspection' => 'Inspecao',
                                    'taxes' => 'Impostos',
                                    'parking' => 'Estacionamento',
                                    'fines' => 'Multas',
                                    'depreciation' => 'Depreciacao',
                                    'other' => 'Outro',
                                ]),
                            TextInput::make('description')
                                ->label('Descricao')
                                ->maxLength(255),
                            TextInput::make('amount')
                                ->label('Valor')
                                ->numeric()
                                ->required()
                                ->prefix('EUR')
                                ->minValue(0),
                            DatePicker::make('expense_date')
                                ->label('Data da despesa')
                                ->required(),
                        ]),
                    ]),
                Section::make('Recorrencia')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('recurring')
                                ->label('Recorrente')
                                ->default(false),
                            Select::make('recurrence_interval')
                                ->label('Intervalo')
                                ->options([
                                    'monthly' => 'Mensal',
                                    'yearly' => 'Anual',
                                    'custom' => 'Personalizado',
                                ])
                                ->visible(fn (Get $get): bool => (bool) $get('recurring'))
                                ->required(fn (Get $get): bool => (bool) $get('recurring')),
                        ]),
                    ]),
            ]);
    }
}
