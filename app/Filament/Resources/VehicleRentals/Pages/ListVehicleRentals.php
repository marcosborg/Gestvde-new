<?php

namespace App\Filament\Resources\VehicleRentals\Pages;

use App\Filament\Resources\VehicleRentals\VehicleRentalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleRentals extends ListRecords
{
    protected static string $resource = VehicleRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
