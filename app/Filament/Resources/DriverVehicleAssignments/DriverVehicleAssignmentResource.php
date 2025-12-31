<?php

namespace App\Filament\Resources\DriverVehicleAssignments;

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
    protected static ?string $model = DriverVehicleAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = 'Atribuicoes';

    protected static ?string $modelLabel = 'Atribuicao';

    protected static ?string $pluralModelLabel = 'Atribuicoes';

    protected static string | UnitEnum | null $navigationGroup = 'Frota';

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
}
