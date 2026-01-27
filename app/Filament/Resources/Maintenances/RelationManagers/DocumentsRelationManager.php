<?php

namespace App\Filament\Resources\Maintenances\RelationManagers;

use App\Filament\Resources\Maintenances\MaintenanceResource;
use App\Filament\Resources\RelationManagers\DocumentsRelationManager as BaseDocumentsRelationManager;

class DocumentsRelationManager extends BaseDocumentsRelationManager
{
    protected static string $ownerResource = MaintenanceResource::class;
}
