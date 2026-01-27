<?php

namespace App\Filament\Resources\DriverVehicleAssignments\Pages;

use App\Filament\Resources\DriverVehicleAssignments\DriverVehicleAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDriverVehicleAssignments extends ListRecords
{
    protected static string $resource = DriverVehicleAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
