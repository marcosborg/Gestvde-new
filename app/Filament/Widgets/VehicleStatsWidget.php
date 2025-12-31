<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VehicleStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Vehicle::query()->count();
        $active = Vehicle::query()->where('status', 'active')->count();
        $inactive = Vehicle::query()->where('status', 'inactive')->count();

        return [
            Stat::make('Total de Viaturas', $total)
                ->description('Todas as viaturas da frota')
                ->icon('heroicon-o-truck')
                ->color('primary'),
            Stat::make('Viaturas Ativas', $active)
                ->description('Disponiveis para servico')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Viaturas Inativas', $inactive)
                ->description('Indisponiveis ou retiradas')
                ->icon('heroicon-o-x-circle')
                ->color('gray'),
        ];
    }
}
