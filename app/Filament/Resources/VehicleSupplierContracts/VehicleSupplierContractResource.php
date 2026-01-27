<?php

namespace App\Filament\Resources\VehicleSupplierContracts;

use App\Filament\Resources\Concerns\HasShieldPermissions;
use App\Filament\Resources\VehicleSupplierContracts\Pages\CreateVehicleSupplierContract;
use App\Filament\Resources\VehicleSupplierContracts\Pages\EditVehicleSupplierContract;
use App\Filament\Resources\VehicleSupplierContracts\Pages\ListVehicleSupplierContracts;
use App\Filament\Resources\VehicleSupplierContracts\Schemas\VehicleSupplierContractForm;
use App\Filament\Resources\VehicleSupplierContracts\Tables\VehicleSupplierContractsTable;
use App\Models\VehicleSupplierContract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VehicleSupplierContractResource extends Resource
{
    use HasShieldPermissions;

    protected static ?string $model = VehicleSupplierContract::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return VehicleSupplierContractForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleSupplierContractsTable::configure($table);
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
            'index' => ListVehicleSupplierContracts::route('/'),
            'create' => CreateVehicleSupplierContract::route('/create'),
            'edit' => EditVehicleSupplierContract::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.supplier_contracts');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.finance');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.supplier_contract');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.supplier_contracts');
    }
}
