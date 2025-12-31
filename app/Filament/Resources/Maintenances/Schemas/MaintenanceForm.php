<?php

namespace App\Filament\Resources\Maintenances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MaintenanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes da Manutencao')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('vehicle_id')
                                ->label('Viatura')
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('type')
                                ->required()
                                ->options([
                                    'preventive' => 'Preventiva',
                                    'corrective' => 'Corretiva',
                                ]),
                            TextInput::make('description')
                                ->label('Descricao')
                                ->maxLength(255),
                            TextInput::make('cost')
                                ->label('Custo')
                                ->numeric()
                                ->required()
                                ->prefix('EUR')
                                ->minValue(0),
                            DatePicker::make('maintenance_date')
                                ->label('Data da manutencao')
                                ->required(),
                            DatePicker::make('next_due_date')
                                ->label('Proxima data prevista'),
                            TextInput::make('next_due_mileage')
                                ->label('Proxima quilometragem')
                                ->numeric()
                                ->minValue(0),
                        ]),
                    ]),
            ]);
    }
}
