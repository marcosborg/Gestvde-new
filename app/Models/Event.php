<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    protected $casts = [
        'event_date' => 'date',
        'completed' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeUpcoming(Builder $query, ?Carbon $today = null): Builder
    {
        $today ??= now()->startOfDay();

        return $query
            ->where('completed', false)
            ->whereDate('event_date', '>=', $today)
            ->whereRaw('DATEDIFF(event_date, ?) <= notify_before_days', [$today->toDateString()]);
    }
}
