<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'acquisition_date' => 'date',
        'annual_depreciation_percent' => 'decimal:2',
        'acquisition_value' => 'decimal:2',
        'fuel_type' => 'array',
        'mileage' => 'integer',
        'weekly_rent' => 'decimal:2',
        'year' => 'integer',
        'leasing_entry_amount' => 'decimal:2',
        'leasing_monthly_installment' => 'decimal:2',
        'leasing_initial_installment' => 'decimal:2',
        'vehicle_documents' => 'array',
        'seats_count' => 'integer',
        'registration_date' => 'date',
    ];

    /**
     * @return array<int, string>
     */
    public function getFuelTypeAttribute(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode((string) $value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return is_array($decoded) ? $decoded : [(string) $decoded];
        }

        return [(string) $value];
    }

    public function setFuelTypeAttribute(mixed $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['fuel_type'] = null;

            return;
        }

        if (is_string($value)) {
            $value = [$value];
        }

        if (! is_array($value)) {
            $value = [$value];
        }

        $normalized = array_values(array_unique(array_filter(array_map(
            static fn (mixed $item): ?string => $item !== null && $item !== ''
                ? strtolower((string) $item)
                : null,
            $value,
        ))));

        $this->attributes['fuel_type'] = json_encode($normalized, JSON_UNESCAPED_UNICODE);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(VehicleRental::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DriverVehicleAssignment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(VehicleExpense::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(VehicleCheckin::class);
    }

    public function damages(): HasManyThrough
    {
        return $this->hasManyThrough(
            VehicleDamage::class,
            VehicleCheckin::class,
            'vehicle_id',
            'checkin_id',
            'id',
            'id'
        );
    }

    public function supplierContracts(): HasMany
    {
        return $this->hasMany(VehicleSupplierContract::class);
    }

    public function currentRental(): HasOne
    {
        return $this->hasOne(VehicleRental::class)
            ->whereNull('end_date')
            ->latestOfMany('start_date');
    }

    public function currentSupplierContract(): HasOne
    {
        return $this->hasOne(VehicleSupplierContract::class)
            ->whereNull('end_date')
            ->latestOfMany('start_date');
    }

    public function nextInspectionDate(?Carbon $referenceDate = null): ?Carbon
    {
        $registrationDate = $this->registration_date;

        if (! $registrationDate) {
            return null;
        }

        return self::calculateNextInspectionDate($registrationDate->toDateString(), $referenceDate);
    }

    public static function calculateNextInspectionDate(?string $registrationDate, ?Carbon $referenceDate = null): ?Carbon
    {
        if (! $registrationDate) {
            return null;
        }

        $registeredAt = Carbon::parse($registrationDate)->startOfDay();
        $referenceDate = ($referenceDate ?? now())->startOfDay();

        $firstInspection = $registeredAt->copy()->addYears(4);
        if ($referenceDate->lessThanOrEqualTo($firstInspection)) {
            return $firstInspection;
        }

        $secondInspection = $registeredAt->copy()->addYears(6);
        if ($referenceDate->lessThanOrEqualTo($secondInspection)) {
            return $secondInspection;
        }

        $yearsSinceSecond = $secondInspection->diffInYears($referenceDate);
        $nextInspection = $secondInspection->copy()->addYears($yearsSinceSecond + 1);

        if ($nextInspection->lessThanOrEqualTo($referenceDate)) {
            $nextInspection->addYear();
        }

        return $nextInspection;
    }
}
