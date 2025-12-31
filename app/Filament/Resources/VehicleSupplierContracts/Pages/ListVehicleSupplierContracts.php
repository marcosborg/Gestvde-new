<?php

namespace App\Filament\Resources\VehicleSupplierContracts\Pages;

use App\Filament\Resources\VehicleSupplierContracts\VehicleSupplierContractResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleSupplierContracts extends ListRecords
{
    protected static string $resource = VehicleSupplierContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
