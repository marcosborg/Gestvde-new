<?php

namespace App\Filament\Resources\VehicleCheckins\Pages;

use App\Filament\Resources\VehicleCheckins\VehicleCheckinResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicleCheckin extends CreateRecord
{
    protected static string $resource = VehicleCheckinResource::class;

    protected function afterCreate(): void
    {
        $this->record->registerCheckoutDamages();
    }
}
