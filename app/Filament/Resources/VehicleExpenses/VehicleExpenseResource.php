<?php

namespace App\Filament\Resources\VehicleExpenses;

use App\Filament\Resources\Concerns\HasShieldPermissions;
use App\Filament\Resources\VehicleExpenses\Pages\CreateVehicleExpense;
use App\Filament\Resources\VehicleExpenses\Pages\EditVehicleExpense;
use App\Filament\Resources\VehicleExpenses\Pages\ListVehicleExpenses;
use App\Filament\Resources\VehicleExpenses\Schemas\VehicleExpenseForm;
use App\Filament\Resources\VehicleExpenses\Tables\VehicleExpensesTable;
use App\Models\VehicleExpense;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehicleExpenseResource extends Resource
{
    use HasShieldPermissions;

    protected static ?string $model = VehicleExpense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return VehicleExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleExpensesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['vehicle', 'supplier', 'expenseCategory']);
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
            'index' => ListVehicleExpenses::route('/'),
            'create' => CreateVehicleExpense::route('/create'),
            'edit' => EditVehicleExpense::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.vehicle_expenses');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.finance');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.vehicle_expense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.vehicle_expenses');
    }
}
