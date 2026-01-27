<x-filament-panels::page>
    <style>
        .fc-weekdays,
        .fc-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.5rem;
        }

        .fc-weekday {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            padding: 0.25rem 0.5rem;
        }

        .fc-day {
            min-height: 140px;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.5rem;
            background: #ffffff;
        }

        .fc-day.today {
            border-color: #2563eb;
            box-shadow: inset 0 0 0 2px rgba(37, 99, 235, 0.4);
            background: #eff6ff;
        }

        .fc-day.outside {
            background: #f9fafb;
        }

        .fc-day-number {
            font-size: 0.7rem;
            font-weight: 600;
            color: #111827;
        }

        .fc-day-number.muted {
            color: #9ca3af;
        }

        .fc-event {
            border-left-width: 4px;
            border-left-style: solid;
            border-radius: 0.5rem;
            padding: 0.5rem;
            font-size: 0.75rem;
        }

        .fc-event-title {
            font-weight: 600;
        }

        .fc-event-meta {
            font-size: 0.65rem;
            opacity: 0.8;
        }

        .fc-event-status {
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        @media (prefers-color-scheme: dark) {
            .fc-weekday {
                color: #9ca3af;
            }

            .fc-day {
                border-color: #1f2937;
                background: #0f172a;
            }

            .fc-day.today {
                border-color: #60a5fa;
                box-shadow: inset 0 0 0 2px rgba(96, 165, 250, 0.35);
                background: #0b1b34;
            }

            .fc-day.outside {
                background: #0b1220;
            }

            .fc-day-number {
                color: #e5e7eb;
            }

            .fc-day-number.muted {
                color: #6b7280;
            }
        }
    </style>
    @php
        $typeStyles = [
            'inspection' => 'border-blue-400 bg-blue-50 text-blue-800',
            'maintenance' => 'border-amber-400 bg-amber-50 text-amber-800',
            'insurance' => 'border-indigo-400 bg-indigo-50 text-indigo-800',
            'tax' => 'border-red-400 bg-red-50 text-red-800',
            'document' => 'border-emerald-400 bg-emerald-50 text-emerald-800',
            'contract' => 'border-gray-400 bg-gray-50 text-gray-700',
            'other' => 'border-slate-400 bg-slate-50 text-slate-700',
        ];
        $typeLabels = [
            'inspection' => __('admin.event_types.inspection'),
            'maintenance' => __('admin.event_types.maintenance'),
            'insurance' => __('admin.event_types.insurance'),
            'tax' => __('admin.event_types.tax'),
            'document' => __('admin.event_types.document'),
            'contract' => __('admin.event_types.contract'),
            'other' => __('admin.event_types.other'),
        ];
        $weekdays = [
            __('admin.weekdays.mon'),
            __('admin.weekdays.tue'),
            __('admin.weekdays.wed'),
            __('admin.weekdays.thu'),
            __('admin.weekdays.fri'),
            __('admin.weekdays.sat'),
            __('admin.weekdays.sun'),
        ];
    @endphp

    <div class="flex items-center justify-between gap-4">
        <div class="text-lg font-semibold">
            {{ $monthLabel }}
        </div>
        <div class="flex items-center gap-2">
            <x-filament::button color="gray" wire:click="previousMonth">
                {{ __('admin.actions.previous') }}
            </x-filament::button>
            <x-filament::button color="gray" wire:click="nextMonth">
                {{ __('admin.actions.next') }}
            </x-filament::button>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-3 text-xs">
        @foreach ($typeOptions as $type => $label)
            <label class="flex items-center gap-2">
                <input
                    type="checkbox"
                    value="{{ $type }}"
                    wire:model="typeFilters"
                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
                >
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>

    <div class="mt-4 fc-weekdays">
        @foreach ($weekdays as $day)
            <div class="fc-weekday">{{ $day }}</div>
        @endforeach
    </div>

    <div class="mt-2 fc-grid">
        @foreach ($days as $day)
            @php
                $date = $day['date'];
                $inMonth = $day['inMonth'];
            @endphp
            <div class="fc-day {{ $inMonth ? '' : 'outside' }} {{ $day['isToday'] ? 'today' : '' }}">
                <div class="fc-day-number {{ $inMonth ? '' : 'muted' }}">
                    {{ $date->format('j') }}
                </div>

                <div class="mt-2 space-y-2">
                    @foreach ($day['events'] as $event)
                        @php
                            $style = $typeStyles[$event['type']] ?? 'border-gray-400 bg-gray-50 text-gray-700';
                        @endphp
                        <div class="fc-event {{ $style }}">
                            @if (! empty($event['url']))
                                <a class="fc-event-title hover:underline" href="{{ $event['url'] }}">
                                    {{ $event['title'] }}
                                </a>
                            @else
                                <div class="fc-event-title">{{ $event['title'] }}</div>
                            @endif
                            <div class="fc-event-meta">
                                {{ $typeLabels[$event['type']] ?? __('admin.labels.other') }}
                            </div>
                            @if (! empty($event['meta']))
                                <div class="fc-event-status {{ $event['meta_color'] ?? '' }}">{{ $event['meta'] }}</div>
                            @endif
                            @if (! empty($event['can_complete']))
                                <div class="mt-1 flex items-center gap-2 text-[10px]">
                                    @if ($event['completed'])
                                        <span class="text-gray-500">{{ __('admin.labels.completed') }}</span>
                                    @else
                                        <button class="text-green-700 hover:underline" wire:click="markCompleted({{ $event['id'] }})">
                                            {{ __('admin.actions.complete') }}
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
