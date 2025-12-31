<?php

namespace App\Models;

use App\Services\DriverVehicleAssignmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class DriverVehicleAssignment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'weekly_rate_override' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (DriverVehicleAssignment $assignment): void {
            if (! $assignment->driver_id || ! $assignment->vehicle_id || ! $assignment->start_date) {
                return;
            }

            app(DriverVehicleAssignmentService::class)->ensureNoOverlapFor(
                $assignment->driver_id,
                $assignment->vehicle_id,
                $assignment->start_date,
                $assignment->end_date,
                $assignment->getKey()
            );
        });
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeActive(Builder $query, ?Carbon $today = null): Builder
    {
        $today ??= now()->startOfDay();

        return $query
            ->whereDate('start_date', '<=', $today)
            ->where(function (Builder $query) use ($today): Builder {
                return $query
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            });
    }

    public function scopeForDriver(Builder $query, int $driverId): Builder
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForVehicle(Builder $query, int $vehicleId): Builder
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeOverlaps(Builder $query, string | Carbon $start, string | Carbon | null $end): Builder
    {
        $start = Carbon::parse($start)->startOfDay();
        $end = $end ? Carbon::parse($end)->startOfDay() : null;

        return $query
            ->when($end, fn (Builder $query): Builder => $query->whereDate('start_date', '<=', $end))
            ->where(function (Builder $query) use ($start): Builder {
                return $query
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $start);
            });
    }
}
