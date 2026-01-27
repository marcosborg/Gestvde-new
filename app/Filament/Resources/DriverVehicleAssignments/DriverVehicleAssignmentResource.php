<?php

namespace App\Filament\Resources\DriverVehicleAssignments;

use App\Filament\Resources\Concerns\HasShieldPermissions;
use App\Filament\Resources\DriverVehicleAssignments\Pages\CreateDriverVehicleAssignment;
use App\Filament\Resources\DriverVehicleAssignments\Pages\EditDriverVehicleAssignment;
use App\Filament\Resources\DriverVehicleAssignments\Pages\ListDriverVehicleAssignments;
use App\Filament\Resources\DriverVehicleAssignments\Schemas\DriverVehicleAssignmentForm;
use App\Filament\Resources\DriverVehicleAssignments\Tables\DriverVehicleAssignmentsTable;
use App\Models\DriverVehicleAssignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DriverVehicleAssignmentResource extends Resource
{
    use HasShieldPermissions;

    protected static ?string $model = DriverVehicleAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 30;

    protected static string|UnitEnum|null $navigationGroup = null;

    public static function form(Schema $schema): Schema
    {
        return DriverVehicleAssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DriverVehicleAssignmentsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['driver', 'vehicle']);
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
            'index' => ListDriverVehicleAssignments::route('/'),
            'create' => CreateDriverVehicleAssignment::route('/create'),
            'edit' => EditDriverVehicleAssignment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.assignments');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.fleet');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.assignment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.assignments');
    }
}
