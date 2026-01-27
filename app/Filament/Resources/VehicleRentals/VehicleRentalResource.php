<?php

namespace App\Filament\Resources\VehicleRentals;

use App\Filament\Resources\Concerns\HasShieldPermissions;
use App\Filament\Resources\VehicleRentals\Pages\CreateVehicleRental;
use App\Filament\Resources\VehicleRentals\Pages\EditVehicleRental;
use App\Filament\Resources\VehicleRentals\Pages\ListVehicleRentals;
use App\Filament\Resources\VehicleRentals\Schemas\VehicleRentalForm;
use App\Filament\Resources\VehicleRentals\Tables\VehicleRentalsTable;
use App\Models\VehicleRental;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VehicleRentalResource extends Resource
{
    use HasShieldPermissions;

    protected static ?string $model = VehicleRental::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return VehicleRentalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleRentalsTable::configure($table);
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
            'index' => ListVehicleRentals::route('/'),
            'create' => CreateVehicleRental::route('/create'),
            'edit' => EditVehicleRental::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.rentals');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.operations');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.rental');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.rentals');
    }
}
