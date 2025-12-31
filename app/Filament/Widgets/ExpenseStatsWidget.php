<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Models\VehicleExpense;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpenseStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $monthlyTotal = VehicleExpense::query()
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        $vehicleCount = max(Vehicle::query()->count(), 1);
        $averagePerVehicle = $monthlyTotal / $vehicleCount;

        return [
            Stat::make('Despesa Mensal', number_format($monthlyTotal, 2))
                ->description($start->locale('pt_PT')->isoFormat('MMM YYYY'))
                ->icon('heroicon-o-currency-euro')
                ->color('warning'),
            Stat::make('Media por Viatura', number_format($averagePerVehicle, 2))
                ->description('Custo medio mensal')
                ->icon('heroicon-o-scale')
                ->color('info'),
        ];
    }
}
