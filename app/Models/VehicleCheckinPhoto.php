<?php

namespace App\Models;

use App\Enums\VehicleCheckinPhotoSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleCheckinPhoto extends Model
{
    protected $guarded = [];

    protected $casts = [
        'section' => VehicleCheckinPhotoSection::class,
    ];

    public function checkin(): BelongsTo
    {
        return $this->belongsTo(VehicleCheckin::class, 'checkin_id');
    }
}
