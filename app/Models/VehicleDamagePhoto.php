<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDamagePhoto extends Model
{
    protected $guarded = [];

    public function damage(): BelongsTo
    {
        return $this->belongsTo(VehicleDamage::class, 'damage_id');
    }
}
