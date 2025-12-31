<?php

namespace App\Filament\Resources\VehicleExpenses\Pages;

use App\Filament\Resources\VehicleExpenses\VehicleExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleExpenses extends ListRecords
{
    protected static string $resource = VehicleExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
