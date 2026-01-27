<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

class Document extends Model
{
    protected $guarded = [];

    protected $casts = [
        'valid_until' => 'date',
        'notify_before_days' => 'integer',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeExpiringSoon(Builder $query, ?Carbon $today = null): Builder
    {
        $today ??= now()->startOfDay();

        return $query
            ->whereNotNull('valid_until')
            ->whereDate('valid_until', '>=', $today)
            ->whereRaw('DATEDIFF(valid_until, ?) <= notify_before_days', [$today->toDateString()]);
    }

    public function scopeExpired(Builder $query, ?Carbon $today = null): Builder
    {
        $today ??= now()->startOfDay();

        return $query
            ->whereNotNull('valid_until')
            ->whereDate('valid_until', '<', $today);
    }

    public function isExpired(?Carbon $today = null): bool
    {
        $today ??= now()->startOfDay();

        return $this->valid_until ? $this->valid_until->lt($today) : false;
    }

    public function isExpiringSoon(?Carbon $today = null): bool
    {
        $today ??= now()->startOfDay();

        if (! $this->valid_until) {
            return false;
        }

        $daysUntil = $today->diffInDays($this->valid_until, false);

        return $daysUntil >= 0 && $daysUntil <= ($this->notify_before_days ?? 0);
    }

    public function syncCalendarEvent(): void
    {
        if (! $this->valid_until) {
            $this->deleteCalendarEvent();

            return;
        }

        $this->loadMissing('documentable');

        $eventData = [
            'vehicle_id' => $this->documentable instanceof Vehicle ? $this->documentable->getKey() : null,
            'title' => $this->calendarEventTitle(),
            'description' => $this->calendarEventMarker(),
            'event_type' => 'document',
            'event_date' => $this->valid_until,
            'notify_before_days' => $this->notify_before_days ?? 0,
            'completed' => false,
        ];

        $event = $this->calendarEventQuery()->first();

        if ($event) {
            $event->update($eventData);

            return;
        }

        Event::query()->create($eventData);
    }

    public function deleteCalendarEvent(): void
    {
        $this->calendarEventQuery()->delete();
    }

    private function calendarEventTitle(): string
    {
        $owner = $this->documentableLabel();

        if ($owner) {
            return $owner . ' - ' . $this->title;
        }

        return $this->title;
    }

    private function documentableLabel(): ?string
    {
        $documentable = $this->documentable;

        if (! $documentable) {
            return null;
        }

        $label = data_get($documentable, 'plate') ?? data_get($documentable, 'name');

        return $label ?: null;
    }

    private function calendarEventMarker(): string
    {
        return 'document:' . $this->getKey();
    }

    private function calendarEventQuery(): Builder
    {
        return Event::query()
            ->where('event_type', 'document')
            ->where('description', $this->calendarEventMarker());
    }
}
