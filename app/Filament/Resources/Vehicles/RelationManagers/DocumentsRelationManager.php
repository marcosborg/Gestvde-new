<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Filament\Resources\RelationManagers\DocumentsRelationManager as BaseDocumentsRelationManager;
use App\Filament\Resources\Vehicles\VehicleResource;

class DocumentsRelationManager extends BaseDocumentsRelationManager
{
    protected static string $ownerResource = VehicleResource::class;
}
