<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UpcomingEventsStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $next7 = $today->copy()->addDays(7);
        $next30 = $today->copy()->addDays(30);

        $upcoming7 = Event::query()
            ->where('completed', false)
            ->whereBetween('event_date', [$today, $next7])
            ->count();

        $upcoming30 = Event::query()
            ->where('completed', false)
            ->whereBetween('event_date', [$today, $next30])
            ->count();

        return [
            Stat::make('Proximos 7 dias', $upcoming7)
                ->description('Proxima semana')
                ->icon('heroicon-o-calendar')
                ->color('warning'),
            Stat::make('Proximos 30 dias', $upcoming30)
                ->description('Proximo mes')
                ->icon('heroicon-o-calendar-days')
                ->color('info'),
        ];
    }
}
