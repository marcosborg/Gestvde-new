<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function vehicleSupplierContracts(): HasMany
    {
        return $this->hasMany(VehicleSupplierContract::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(SupplierCategory::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(SupplierMovement::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(VehicleExpense::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function currentBalance(): float
    {
        $balance = $this->movements()
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE -amount END), 0) AS balance")
            ->value('balance');

        return (float) $balance;
    }
}
