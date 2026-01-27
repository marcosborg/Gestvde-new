<?php

namespace App\Filament\Resources\SupplierCategories\Pages;

use App\Filament\Resources\SupplierCategories\SupplierCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupplierCategories extends ListRecords
{
    protected static string $resource = SupplierCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
