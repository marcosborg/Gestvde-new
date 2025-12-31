<?php

namespace App\Filament\Resources\VehicleRentals\Pages;

use App\Filament\Resources\VehicleRentals\VehicleRentalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleRental extends EditRecord
{
    protected static string $resource = VehicleRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
