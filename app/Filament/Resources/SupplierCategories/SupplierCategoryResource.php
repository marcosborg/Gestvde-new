<?php

namespace App\Filament\Resources\SupplierCategories;

use App\Filament\Resources\Concerns\HasShieldPermissions;
use App\Filament\Resources\SupplierCategories\Pages\CreateSupplierCategory;
use App\Filament\Resources\SupplierCategories\Pages\EditSupplierCategory;
use App\Filament\Resources\SupplierCategories\Pages\ListSupplierCategories;
use App\Filament\Resources\SupplierCategories\Schemas\SupplierCategoryForm;
use App\Filament\Resources\SupplierCategories\Tables\SupplierCategoriesTable;
use App\Models\SupplierCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupplierCategoryResource extends Resource
{
    use HasShieldPermissions;

    protected static ?string $model = SupplierCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SupplierCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupplierCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupplierCategories::route('/'),
            'create' => CreateSupplierCategory::route('/create'),
            'edit' => EditSupplierCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.supplier_categories');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.finance');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.supplier_category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.supplier_categories');
    }
}
