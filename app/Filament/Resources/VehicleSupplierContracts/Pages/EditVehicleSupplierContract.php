<?php

namespace App\Filament\Resources\VehicleSupplierContracts\Pages;

use App\Filament\Resources\VehicleSupplierContracts\VehicleSupplierContractResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleSupplierContract extends EditRecord
{
    protected static string $resource = VehicleSupplierContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
