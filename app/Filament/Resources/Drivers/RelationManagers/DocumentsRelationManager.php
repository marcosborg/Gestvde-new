<?php

namespace App\Filament\Resources\Drivers\RelationManagers;

use App\Filament\Resources\Drivers\DriverResource;
use App\Filament\Resources\RelationManagers\DocumentsRelationManager as BaseDocumentsRelationManager;

class DocumentsRelationManager extends BaseDocumentsRelationManager
{
    protected static string $ownerResource = DriverResource::class;
}
