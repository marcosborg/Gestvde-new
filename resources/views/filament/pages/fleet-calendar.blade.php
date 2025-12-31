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

        @media (prefers-color-scheme: dark) {
            .fc-weekday {
                color: #9ca3af;
            }

            .fc-day {
                border-color: #1f2937;
                background: #0f172a;
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
        ];
        $typeLabels = [
            'inspection' => 'Inspecao',
            'maintenance' => 'Manutencao',
            'insurance' => 'Seguro',
            'tax' => 'Imposto',
        ];
        $weekdays = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'];
    @endphp

    <div class="flex items-center justify-between gap-4">
        <div class="text-lg font-semibold">
            {{ $monthLabel }}
        </div>
        <div class="flex items-center gap-2">
            <x-filament::button color="gray" wire:click="previousMonth">
                Anterior
            </x-filament::button>
            <x-filament::button color="gray" wire:click="nextMonth">
                Seguinte
            </x-filament::button>
        </div>
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
            <div class="fc-day {{ $inMonth ? '' : 'outside' }}">
                <div class="fc-day-number {{ $inMonth ? '' : 'muted' }}">
                    {{ $date->format('j') }}
                </div>

                <div class="mt-2 space-y-2">
                    @foreach ($day['events'] as $event)
                        @php
                            $style = $typeStyles[$event->event_type] ?? 'border-gray-400 bg-gray-50 text-gray-700';
                        @endphp
                        <div class="fc-event {{ $style }}">
                            <a class="fc-event-title hover:underline" href="{{ $eventEditUrl($event) }}">
                                {{ $event->title }}
                            </a>
                            <div class="fc-event-meta">
                                {{ $typeLabels[$event->event_type] ?? 'Outro' }}
                            </div>
                            <div class="mt-1 flex items-center gap-2 text-[10px]">
                                @if ($event->completed)
                                    <span class="text-gray-500">Concluido</span>
                                @else
                                    <button class="text-green-700 hover:underline" wire:click="markCompleted({{ $event->id }})">
                                        Concluir
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
