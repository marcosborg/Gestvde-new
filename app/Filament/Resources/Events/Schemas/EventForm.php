<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Evento')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('vehicle_id')
                                ->label('Viatura')
                                ->relationship('vehicle', 'plate')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Select::make('event_type')
                                ->required()
                                ->options([
                                    'inspection' => 'Inspecao',
                                    'insurance' => 'Seguro',
                                    'maintenance' => 'Manutencao',
                                    'contract' => 'Contrato',
                                    'tax' => 'Imposto',
                                    'other' => 'Outro',
                                ]),
                            TextInput::make('title')
                                ->label('Titulo')
                                ->required()
                                ->maxLength(150),
                            TextInput::make('notify_before_days')
                                ->label('Avisar com antecedencia (dias)')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            DatePicker::make('event_date')
                                ->label('Data do evento')
                                ->required(),
                            Toggle::make('completed')
                                ->label('Concluido')
                                ->default(false),
                        ]),
                        Textarea::make('description')
                            ->label('Descricao')
                            ->rows(4)
                            ->maxLength(2000),
                    ]),
            ]);
    }
}
