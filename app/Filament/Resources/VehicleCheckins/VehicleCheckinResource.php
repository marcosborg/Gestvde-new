<?php

namespace App\Filament\Resources\VehicleCheckins;

use App\Filament\Resources\Concerns\HasShieldPermissions;
use App\Filament\Resources\VehicleCheckins\Pages\CreateVehicleCheckin;
use App\Filament\Resources\VehicleCheckins\Pages\EditVehicleCheckin;
use App\Filament\Resources\VehicleCheckins\Pages\ListVehicleCheckins;
use App\Filament\Resources\VehicleCheckins\Schemas\VehicleCheckinForm;
use App\Filament\Resources\VehicleCheckins\Tables\VehicleCheckinsTable;
use App\Models\VehicleCheckin;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class VehicleCheckinResource extends Resource
{
    use HasShieldPermissions;

    protected static ?string $model = VehicleCheckin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = 'occurred_at';

    protected static ?int $navigationSort = 40;

    protected static string|UnitEnum|null $navigationGroup = null;

    public static function form(Schema $schema): Schema
    {
        return VehicleCheckinForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleCheckinsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['vehicle', 'driver'])
            ->withCount(['photos', 'damages']);
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
            'index' => ListVehicleCheckins::route('/'),
            'create' => CreateVehicleCheckin::route('/create'),
            'edit' => EditVehicleCheckin::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.vehicle_checkins');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.operations');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.vehicle_checkin');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.vehicle_checkins');
    }
}
