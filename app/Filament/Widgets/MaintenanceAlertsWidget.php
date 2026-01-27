<?php

namespace App\Filament\Widgets;

use App\Models\Maintenance;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MaintenanceAlertsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $today = now()->startOfDay();

        $overdue = Maintenance::query()->overdue($today)->count();
        $dueIn7 = Maintenance::query()->dueSoon(7, $today)->count();
        $dueIn30 = Maintenance::query()->dueSoon(30, $today)->count();

        return [
            Stat::make(__('admin.widgets.maintenance_overdue'), $overdue)
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            Stat::make(__('admin.widgets.maintenance_due_7'), $dueIn7)
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make(__('admin.widgets.maintenance_due_30'), $dueIn30)
                ->icon('heroicon-o-calendar-days')
                ->color('info'),
        ];
    }
}
