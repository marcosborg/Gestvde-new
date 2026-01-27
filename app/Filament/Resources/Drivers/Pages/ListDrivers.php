<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Imports\DriverImporter;
use App\Filament\Resources\Drivers\DriverResource;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->label(__('admin.actions.import_csv'))
                ->importer(DriverImporter::class)
                ->visible(function (): bool {
                    $permission = FilamentShield::getResourcePolicyActionsWithPermissions(DriverResource::class)['create'] ?? null;

                    return $permission ? (auth()->user()?->can($permission) ?? false) : false;
                }),
        ];
    }
}
