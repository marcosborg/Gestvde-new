<?php

namespace App\Models;

use App\Enums\CleanlinessLevel;
use App\Enums\ConditionLevel;
use App\Enums\FuelLevel;
use App\Enums\VehicleCheckinPhotoSection;
use App\Enums\VehicleCheckinType;
use App\Support\VehicleCheckinComparator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleCheckin extends Model
{
    protected $guarded = [];

    protected $casts = [
        'occurred_at' => 'datetime',
        'check_type' => VehicleCheckinType::class,
        'exterior_condition' => ConditionLevel::class,
        'interior_condition' => ConditionLevel::class,
        'tires_condition' => ConditionLevel::class,
        'cleanliness' => CleanlinessLevel::class,
        'fuel_level' => FuelLevel::class,
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VehicleCheckinPhoto::class, 'checkin_id');
    }

    public function exteriorPhotos(): HasMany
    {
        return $this->photos()->where('section', VehicleCheckinPhotoSection::Exterior->value);
    }

    public function interiorPhotos(): HasMany
    {
        return $this->photos()->where('section', VehicleCheckinPhotoSection::Interior->value);
    }

    public function damages(): HasMany
    {
        return $this->hasMany(VehicleDamage::class, 'checkin_id');
    }

    public function latestCheckinBefore(): ?self
    {
        return self::query()
            ->where('vehicle_id', $this->vehicle_id)
            ->where('check_type', VehicleCheckinType::CheckIn->value)
            ->where('occurred_at', '<', $this->occurred_at)
            ->orderByDesc('occurred_at')
            ->first();
    }

    public function registerCheckoutDamages(): void
    {
        if ($this->check_type?->value !== VehicleCheckinType::CheckOut->value) {
            return;
        }

        $previous = $this->latestCheckinBefore();

        if (! $previous) {
            return;
        }

        $differences = VehicleCheckinComparator::detectNewDamages(
            [
                'exterior_condition' => $previous->exterior_condition,
                'interior_condition' => $previous->interior_condition,
                'tires_condition' => $previous->tires_condition,
            ],
            [
                'exterior_condition' => $this->exterior_condition,
                'interior_condition' => $this->interior_condition,
                'tires_condition' => $this->tires_condition,
            ],
        );

        foreach ($differences as $difference) {
            $description = __('admin.damage_comparison', [
                'from' => $difference['from']->label(),
                'to' => $difference['to']->label(),
            ]);

            $this->damages()->create([
                'zone' => $difference['area']->value,
                'description' => $description,
            ]);
        }
    }
}
