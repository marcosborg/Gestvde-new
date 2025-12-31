<?php

namespace App\Filament\Resources\VehicleExpenses;

use App\Filament\Resources\VehicleExpenses\Pages\CreateVehicleExpense;
use App\Filament\Resources\VehicleExpenses\Pages\EditVehicleExpense;
use App\Filament\Resources\VehicleExpenses\Pages\ListVehicleExpenses;
use App\Filament\Resources\VehicleExpenses\Schemas\VehicleExpenseForm;
use App\Filament\Resources\VehicleExpenses\Tables\VehicleExpensesTable;
use App\Models\VehicleExpense;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VehicleExpenseResource extends Resource
{
    protected static ?string $model = VehicleExpense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Despesas de Viaturas';

    protected static ?string $modelLabel = 'Despesa';

    protected static ?string $pluralModelLabel = 'Despesas';

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
            ->with('vehicle');
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
}
