<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'cost' => 'decimal:2',
        'maintenance_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeOverdue(Builder $query, ?Carbon $today = null): Builder
    {
        $today ??= now()->startOfDay();

        return $query->where(function (Builder $query) use ($today): Builder {
            return $query
                ->whereNotNull('next_due_date')
                ->whereDate('next_due_date', '<=', $today)
                ->orWhere(function (Builder $query): Builder {
                    return $query
                        ->whereNotNull('next_due_mileage')
                        ->whereHas('vehicle', function (Builder $query): Builder {
                            return $query->whereColumn('vehicles.mileage', '>=', 'maintenances.next_due_mileage');
                        });
                });
        });
    }
}
