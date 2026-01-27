<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

class Maintenance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'cost' => 'decimal:2',
        'maintenance_date' => 'date',
        'next_due_date' => 'date',
        'next_due_mileage' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(MaintenanceTask::class)->withTimestamps();
    }

    public function scopeOverdue(Builder $query, ?Carbon $today = null): Builder
    {
        $today ??= now()->startOfDay();

        return $query->where(function (Builder $query) use ($today): Builder {
            return $query
                ->where('status', 'overdue')
                ->orWhere(function (Builder $query) use ($today): Builder {
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
        })->where(function (Builder $query): Builder {
            return $query
                ->whereNull('status')
                ->orWhere('status', '!=', 'completed');
        });
    }

    public function scopeDueSoon(Builder $query, int $days = 7, ?Carbon $today = null): Builder
    {
        $today ??= now()->startOfDay();
        $limit = $today->copy()->addDays($days);

        return $query
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '>=', $today)
            ->whereDate('next_due_date', '<=', $limit)
            ->where(function (Builder $query): Builder {
                return $query
                    ->whereNull('status')
                    ->orWhereNotIn('status', ['completed', 'overdue']);
            });
    }

    public function isOverdue(?Carbon $today = null): bool
    {
        $today ??= now()->startOfDay();

        if ($this->status === 'overdue') {
            return true;
        }

        if ($this->status === 'completed') {
            return false;
        }

        $dateOverdue = $this->next_due_date && $this->next_due_date->lte($today);
        $mileageOverdue = $this->next_due_mileage && $this->vehicle && $this->vehicle->mileage >= $this->next_due_mileage;

        return $dateOverdue || $mileageOverdue;
    }

    public function resolvedStatus(?Carbon $today = null): string
    {
        if ($this->status === 'completed') {
            return 'completed';
        }

        if ($this->isOverdue($today)) {
            return 'overdue';
        }

        return $this->status ?: 'scheduled';
    }

    public function dueSeverity(?Carbon $today = null): ?string
    {
        $today ??= now()->startOfDay();

        if ($this->isOverdue($today)) {
            return 'danger';
        }

        if (! $this->next_due_date) {
            return null;
        }

        $daysUntil = $today->diffInDays($this->next_due_date, false);

        if ($daysUntil >= 0 && $daysUntil <= 7) {
            return 'warning';
        }

        return null;
    }
}
