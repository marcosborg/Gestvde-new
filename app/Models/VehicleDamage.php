<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleDamage extends Model
{
    protected $guarded = [];

    public function checkin(): BelongsTo
    {
        return $this->belongsTo(VehicleCheckin::class, 'checkin_id');
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VehicleDamagePhoto::class, 'damage_id');
    }
}
