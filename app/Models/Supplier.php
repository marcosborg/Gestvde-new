<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public function vehicleSupplierContracts(): HasMany
    {
        return $this->hasMany(VehicleSupplierContract::class);
    }
}
