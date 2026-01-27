<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\RelationManagers\DocumentsRelationManager as BaseDocumentsRelationManager;

class DocumentsRelationManager extends BaseDocumentsRelationManager
{
    protected static string $ownerResource = CompanyResource::class;
}
