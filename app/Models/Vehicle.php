<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $guarded = [];

    protected $casts = [
        'acquisition_date' => 'date',
        'annual_depreciation_percent' => 'decimal:2',
        'acquisition_value' => 'decimal:2',
        'mileage' => 'integer',
        'year' => 'integer',
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(VehicleRental::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(VehicleExpense::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
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
}
