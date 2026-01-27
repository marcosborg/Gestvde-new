<?php

namespace App\Filament\Resources\Vehicles\Pages;

use App\Filament\Imports\VehicleImporter;
use App\Filament\Resources\Vehicles\VehicleResource;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->label(__('admin.actions.import_csv'))
                ->importer(VehicleImporter::class)
                ->visible(function (): bool {
                    $permission = FilamentShield::getResourcePolicyActionsWithPermissions(VehicleResource::class)['create'] ?? null;

                    return $permission ? (auth()->user()?->can($permission) ?? false) : false;
                }),
        ];
    }
}
