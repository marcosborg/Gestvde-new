<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Drivers\DriverResource;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Maintenances\MaintenanceResource;
use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\Company;
use App\Models\Document;
use App\Models\Event;
use App\Models\Maintenance;
use App\Models\Vehicle;
use App\Support\FleetCalendarHelper;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Url;
use UnitEnum;

class FleetCalendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 10;

    protected static ?string $title = null;

    protected ?string $heading = null;

    protected string $view = 'filament.pages.fleet-calendar';

    #[Url(as: 'month')]
    public ?string $month = null;

    public array $typeFilters = [];

    public function mount(): void
    {
        $this->month ??= now()->format('Y-m');

        if (! $this->typeFilters) {
            $this->typeFilters = $this->allowedTypes();
        }
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
            ->title(__('admin.messages.event_completed'))
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $month = $this->getMonth();
        $today = now()->startOfDay();
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $selectedTypes = $this->selectedTypes();
        $eventTypes = array_values(array_intersect($selectedTypes, [
            'inspection',
            'maintenance',
            'insurance',
            'document',
            'contract',
            'tax',
            'other',
        ]));

        $driver = Schema::getConnection()->getDriverName();

        $eventsQuery = Event::query()
            ->whereIn('event_type', $eventTypes)
            ->orderBy('event_date');

        $eventsQuery->where(function ($query) use ($calendarStart, $calendarEnd, $driver): void {
            $query->whereBetween('event_date', [$calendarStart, $calendarEnd]);

            if ($driver === 'pgsql') {
                $query->orWhereBetween(DB::raw("event_date - (notify_before_days || ' days')::interval"), [$calendarStart, $calendarEnd]);
            } else {
                $query->orWhereBetween(DB::raw('DATE_SUB(event_date, INTERVAL notify_before_days DAY)'), [$calendarStart, $calendarEnd]);
            }
        });

        if (in_array('document', $eventTypes, true)) {
            $eventsQuery->where(function ($query): void {
                $query
                    ->where('event_type', '!=', 'document')
                    ->orWhere(function ($query): void {
                        $query
                            ->where('event_type', 'document')
                            ->where(function ($query): void {
                                $query
                                    ->whereNull('description')
                                    ->orWhere('description', 'not like', 'document:%');
                            });
                    });
            });
        }

        $events = $eventsQuery->get();
        $items = [];

        $inspectionEventKeys = [];

        foreach ($events as $event) {
            $displayDate = FleetCalendarHelper::resolveDisplayDate(
                $event->event_date->copy(),
                (int) $event->notify_before_days,
                $calendarStart,
                $calendarEnd,
            );

            if (! $displayDate) {
                continue;
            }

            if ($event->event_type === 'inspection' && $event->vehicle_id) {
                $inspectionEventKeys[$event->vehicle_id.'|'.$event->event_date->toDateString()] = true;
            }

            $title = FleetCalendarHelper::formatTitle($event->vehicle?->plate, $event->title);

            $meta = $event->completed
                ? __('admin.event_status.completed')
                : ($event->event_date?->isPast() ? __('admin.event_status.overdue') : null);

            if (! $event->completed && FleetCalendarHelper::isWithinNoticeWindow($event->event_date, (int) $event->notify_before_days, $today)) {
                $meta = __('admin.labels.expires_soon');
            }

            $items[] = [
                'id' => $event->id,
                'type' => $event->event_type,
                'title' => $title,
                'date' => $displayDate,
                'url' => EventResource::getUrl('edit', ['record' => $event]),
                'completed' => (bool) $event->completed,
                'can_complete' => true,
                'meta' => $meta,
                'meta_color' => $event->completed
                    ? 'text-gray-500'
                    : ($meta === __('admin.labels.expires_soon') ? 'text-amber-600' : ($event->event_date?->isPast() ? 'text-red-600' : null)),
            ];
        }

        if (in_array('maintenance', $selectedTypes, true)) {
            $maintenances = Maintenance::query()
                ->where(function ($query) use ($calendarStart, $calendarEnd, $driver): void {
                    $query->whereBetween('next_due_date', [$calendarStart, $calendarEnd]);

                    if ($driver === 'pgsql') {
                        $query->orWhereBetween(DB::raw("next_due_date - interval '7 days'"), [$calendarStart, $calendarEnd]);
                    } else {
                        $query->orWhereBetween(DB::raw('DATE_SUB(next_due_date, INTERVAL 7 DAY)'), [$calendarStart, $calendarEnd]);
                    }

                    $query->orWhere(function ($query) use ($calendarStart, $calendarEnd, $driver): void {
                        $query
                            ->whereNull('next_due_date')
                            ->where(function ($query) use ($calendarStart, $calendarEnd, $driver): void {
                                $query->whereBetween('maintenance_date', [$calendarStart, $calendarEnd]);

                                if ($driver === 'pgsql') {
                                    $query->orWhereBetween(DB::raw("maintenance_date - interval '7 days'"), [$calendarStart, $calendarEnd]);
                                } else {
                                    $query->orWhereBetween(DB::raw('DATE_SUB(maintenance_date, INTERVAL 7 DAY)'), [$calendarStart, $calendarEnd]);
                                }
                            });
                    });
                })
                ->where(function ($query): void {
                    $query->whereNull('status')->orWhere('status', '!=', 'completed');
                })
                ->with('vehicle')
                ->get();

            foreach ($maintenances as $maintenance) {
                $date = $maintenance->next_due_date ?? $maintenance->maintenance_date;

                if (! $date) {
                    continue;
                }

                $displayDate = FleetCalendarHelper::resolveDisplayDate(
                    $date->copy(),
                    7,
                    $calendarStart,
                    $calendarEnd,
                );

                if (! $displayDate) {
                    continue;
                }

                $vehicleLabel = $maintenance->vehicle?->plate;
                $baseTitle = $maintenance->description ?: __('admin.calendar.maintenance_due', ['vehicle' => $maintenance->vehicle?->plate ?? '-']);
                $title = FleetCalendarHelper::formatTitle($vehicleLabel, $baseTitle);
                $status = $maintenance->resolvedStatus();

                $items[] = [
                    'id' => 'maintenance-'.$maintenance->id,
                    'type' => 'maintenance',
                    'title' => $title,
                    'date' => $displayDate,
                    'url' => MaintenanceResource::getUrl('edit', ['record' => $maintenance]),
                    'completed' => false,
                    'can_complete' => false,
                    'meta' => __('admin.maintenance_status.'.$status),
                    'meta_color' => $status === 'overdue' ? 'text-red-600' : 'text-gray-600',
                ];
            }
        }

        if (in_array('document', $selectedTypes, true)) {
            $documents = Document::query()
                ->whereNotNull('valid_until')
                ->where(function ($query) use ($calendarStart, $calendarEnd, $driver): void {
                    $query->whereBetween('valid_until', [$calendarStart, $calendarEnd]);

                    if ($driver === 'pgsql') {
                        $query->orWhereBetween(DB::raw("valid_until - (notify_before_days || ' days')::interval"), [$calendarStart, $calendarEnd]);
                    } else {
                        $query->orWhereBetween(DB::raw('DATE_SUB(valid_until, INTERVAL notify_before_days DAY)'), [$calendarStart, $calendarEnd]);
                    }
                })
                ->with('documentable')
                ->get();

            foreach ($documents as $document) {
                $displayDate = FleetCalendarHelper::resolveDisplayDate(
                    $document->valid_until->copy(),
                    (int) $document->notify_before_days,
                    $calendarStart,
                    $calendarEnd,
                );

                if (! $displayDate) {
                    continue;
                }

                $owner = $document->documentable?->plate ?? $document->documentable?->name;
                $title = FleetCalendarHelper::formatTitle($owner, $document->title);
                $status = $document->isExpired() ? 'expired' : ($document->isExpiringSoon() ? 'expires_soon' : null);

                $items[] = [
                    'id' => 'document-'.$document->id,
                    'type' => 'document',
                    'title' => $title,
                    'date' => $displayDate,
                    'url' => match (true) {
                        $document->documentable instanceof Company => CompanyResource::getUrl('edit', ['record' => $document->documentable]),
                        $document->documentable instanceof Vehicle => VehicleResource::getUrl('edit', ['record' => $document->documentable]),
                        $document->documentable instanceof \App\Models\Driver => DriverResource::getUrl('edit', ['record' => $document->documentable]),
                        default => null,
                    },
                    'completed' => false,
                    'can_complete' => false,
                    'meta' => $status ? __('admin.labels.'.$status) : null,
                    'meta_color' => $status === 'expired' ? 'text-red-600' : ($status === 'expires_soon' ? 'text-amber-600' : null),
                ];
            }
        }

        if (in_array('inspection', $selectedTypes, true)) {
            $vehicles = Vehicle::query()
                ->whereNotNull('registration_date')
                ->get();

            foreach ($vehicles as $vehicle) {
                $inspectionDate = $vehicle->nextInspectionDate($month);

                if (! $inspectionDate) {
                    continue;
                }

                if ($inspectionDate->lt($calendarStart) || $inspectionDate->gt($calendarEnd)) {
                    continue;
                }

                $key = $vehicle->id.'|'.$inspectionDate->toDateString();

                if (isset($inspectionEventKeys[$key])) {
                    continue;
                }

                $items[] = [
                    'id' => 'inspection-'.$vehicle->id,
                    'type' => 'inspection',
                    'title' => FleetCalendarHelper::formatTitle($vehicle->plate, __('admin.calendar.inspection_due', ['vehicle' => $vehicle->plate])),
                    'date' => $inspectionDate,
                    'url' => VehicleResource::getUrl('edit', ['record' => $vehicle]),
                    'completed' => false,
                    'can_complete' => false,
                    'meta' => __('admin.labels.next_inspection'),
                    'meta_color' => 'text-blue-600',
                ];
            }
        }

        $itemsByDate = collect($items)->groupBy(fn (array $item): string => $item['date']->toDateString());
        $days = [];
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $dateKey = $cursor->toDateString();

            $days[] = [
                'date' => $cursor->copy(),
                'inMonth' => $cursor->month === $month->month,
                'isToday' => $cursor->isSameDay($today),
                'events' => $itemsByDate->get($dateKey, collect()),
            ];

            $cursor->addDay();
        }

        return [
            'monthLabel' => $month->locale(app()->getLocale())->isoFormat('MMMM YYYY'),
            'days' => $days,
            'typeOptions' => $this->typeOptions(),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.fleet_calendar');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.operations');
    }

    public function getTitle(): string
    {
        return __('admin.pages.fleet_calendar');
    }

    public function getHeading(): string
    {
        return __('admin.pages.fleet_calendar');
    }

    protected function getMonth(): Carbon
    {
        $month = $this->month ?? now()->format('Y-m');

        return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    }

    private function allowedTypes(): array
    {
        return [
            'inspection',
            'maintenance',
            'insurance',
            'tax',
            'document',
            'contract',
            'other',
        ];
    }

    private function selectedTypes(): array
    {
        $allowed = $this->allowedTypes();

        return array_values(array_intersect($allowed, $this->typeFilters));
    }

    private function typeOptions(): array
    {
        return [
            'inspection' => __('admin.event_types.inspection'),
            'maintenance' => __('admin.event_types.maintenance'),
            'insurance' => __('admin.event_types.insurance'),
            'tax' => __('admin.event_types.tax'),
            'document' => __('admin.event_types.document'),
            'contract' => __('admin.event_types.contract'),
            'other' => __('admin.event_types.other'),
        ];
    }
}
