<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use UnitEnum;

class FleetCalendar extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Calendario da Frota';

    protected static string | UnitEnum | null $navigationGroup = 'Operacoes';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Calendario da Frota';

    protected ?string $heading = 'Calendario da Frota';

    protected string $view = 'filament.pages.fleet-calendar';

    #[Url(as: 'month')]
    public ?string $month = null;

    public function mount(): void
    {
        $this->month ??= now()->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->month = $this->getMonth()->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->getMonth()->addMonth()->format('Y-m');
    }

    public function markCompleted(int $eventId): void
    {
        $event = Event::query()->find($eventId);

        if (! $event) {
            return;
        }

        $event->update(['completed' => true]);

        Notification::make()
            ->title('Evento concluido')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $month = $this->getMonth();
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $events = Event::query()
            ->whereBetween('event_date', [$calendarStart, $calendarEnd])
            ->whereIn('event_type', ['inspection', 'maintenance', 'insurance', 'tax'])
            ->orderBy('event_date')
            ->get();

        $eventsByDate = $events->groupBy(fn (Event $event): string => $event->event_date->toDateString());
        $days = [];
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $dateKey = $cursor->toDateString();

            $days[] = [
                'date' => $cursor->copy(),
                'inMonth' => $cursor->month === $month->month,
                'events' => $eventsByDate->get($dateKey, collect()),
            ];

            $cursor->addDay();
        }

        return [
            'monthLabel' => $month->locale('pt_PT')->isoFormat('MMMM YYYY'),
            'days' => $days,
            'eventEditUrl' => fn (Event $event): string => EventResource::getUrl('edit', ['record' => $event]),
        ];
    }

    protected function getMonth(): Carbon
    {
        $month = $this->month ?? now()->format('Y-m');

        return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    }
}
