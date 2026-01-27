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
        $available = Vehicle::query()->where('status', 'available')->count();
        $rented = Vehicle::query()->where('status', 'rented')->count();
        $unavailable = Vehicle::query()->whereIn('status', ['missing_docs', 'maintenance', 'in_fleet'])->count();

        return [
            Stat::make('Total de Viaturas', $total)
                ->description('Todas as viaturas da frota')
                ->icon('heroicon-o-truck')
                ->color('primary'),
            Stat::make('Viaturas Disponiveis', $available)
                ->description('Prontas para servico')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Viaturas Alugadas', $rented)
                ->description('Em uso por motoristas')
                ->icon('heroicon-o-arrows-right-left')
                ->color('warning'),
            Stat::make('Viaturas Indisponiveis', $unavailable)
                ->description('Sem docs, manutencao ou registo')
                ->icon('heroicon-o-x-circle')
                ->color('gray'),
        ];
    }
}
