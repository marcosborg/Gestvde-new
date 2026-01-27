<?php

namespace App\Filament\Resources\Suppliers;

use App\Filament\Resources\Concerns\HasShieldPermissions;
use App\Filament\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\Resources\Suppliers\RelationManagers\SupplierContractsRelationManager;
use App\Filament\Resources\Suppliers\RelationManagers\SupplierExpensesRelationManager;
use App\Filament\Resources\Suppliers\RelationManagers\SupplierMovementsRelationManager;
use App\Filament\Resources\Suppliers\Schemas\SupplierForm;
use App\Filament\Resources\Suppliers\Tables\SuppliersTable;
use App\Models\Supplier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    use HasShieldPermissions;

    protected static ?string $model = Supplier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuppliersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SupplierContractsRelationManager::class,
            SupplierExpensesRelationManager::class,
            SupplierMovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.suppliers');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.finance');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.supplier');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.suppliers');
    }
}
